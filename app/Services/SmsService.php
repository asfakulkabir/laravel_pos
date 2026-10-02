<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $senderId;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.sms.api_key');
        $this->senderId = config('services.sms.sender_id');
        $this->apiUrl = config('services.sms.api_url');
    }

    /**
     * Send a message to a single recipient.
     */
    public function sendSms($number, $message, $senderId = null)
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'SMS_API_KEY is not configured.',
                'response' => null,
            ];
        }

        try {
            $response = Http::timeout(30)->get($this->apiUrl, [
                'api_key' => $this->apiKey,
                'type' => 'text',
                'number' => $number,
                'senderid' => $senderId ?: $this->senderId,
                'message' => $message,
            ]);

            return [
                'success' => $response->successful() && ! $this->apiReportedError($response->json()),
                'error' => $response->successful() ? null : 'Gateway responded with HTTP ' . $response->status(),
                'response' => $response->json(),
            ];
        } catch (ConnectionException $e) {
            Log::error('SMS gateway request failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Could not reach the SMS gateway.',
                'response' => null,
            ];
        }
    }

    /**
     * Send a message to many recipients, chunked so the gateway gets
     * a manageable comma separated list per request.
     *
     * @return array{total:int, sent:int, failed:int, results:array<int, array>}
     */
    public function sendBulkSms(array $numbers, $message, $senderId = null, int $chunkSize = 100)
    {
        $numbers = array_values(array_unique(array_filter($numbers)));

        $results = [
            'total' => count($numbers),
            'sent' => 0,
            'failed' => 0,
            'results' => [],
        ];

        foreach (array_chunk($numbers, max(1, $chunkSize)) as $chunk) {
            $outcome = $this->sendSms(implode(',', $chunk), $message, $senderId);

            foreach ($chunk as $number) {
                $results['results'][] = [
                    'number' => $number,
                    'success' => $outcome['success'],
                    'error' => $outcome['error'],
                ];

                $outcome['success'] ? $results['sent']++ : $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * The gateway usually answers 200 with an error message inside the body.
     */
    protected function apiReportedError($payload)
    {
        if (! is_array($payload)) {
            return false;
        }

        $error = $payload['error'] ?? $payload['errors'] ?? null;

        if (is_array($error)) {
            $error = implode(' ', $error);
        }

        return ! empty($error) && strtolower(trim($error)) !== 'null';
    }
}