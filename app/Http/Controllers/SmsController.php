<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct(protected SmsService $sms) {}

    /**
     * Show the bulk / single SMS composer.
     */
    public function index()
    {
        $phones = Customer::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->distinct()
            ->pluck('phone');

        $recipients = array_unique(array_filter(
            $phones->map(fn ($phone) => $this->normalizePhone($phone))->all()
        ));

        return view('sms.index', [
            'customerCount' => $phones->count(),
            'recipientCount' => count($recipients),
            'defaultSender' => config('services.sms.sender_id'),
            'testNumbers' => config('services.sms.test_numbers'),
        ]);
    }

    /**
     * Send a message to one number or to every saved customer number.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|in:bulk,single',
            'sender' => 'required|string|max:32',
            'message' => 'required|string|max:1000',
            'phone' => 'nullable|string|max:20',
        ], [], [
            'sender' => 'sender number',
            'phone' => 'phone number',
        ]);

        $mode = $validated['mode'];
        $sender = trim($validated['sender']);
        $message = trim($validated['message']);

        if ($mode === 'single') {
            $phone = $this->normalizePhone($validated['phone'] ?? '');

            if (! $phone) {
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'Enter a valid Bangladeshi mobile number (e.g. 01769021221).']);
            }

            $result = $this->sms->sendSms($phone, $message, $sender);

            return back()->with('sms_result', [
                'mode' => $mode,
                'total' => 1,
                'sent' => $result['success'] ? 1 : 0,
                'failed' => $result['success'] ? 0 : 1,
                'rows' => [[
                    'number' => $phone,
                    'success' => $result['success'],
                    'error' => $result['error'] ?? $this->describePayload($result['response']),
                ]],
            ]);
        }

        $phones = Customer::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->distinct()
            ->pluck('phone');

        $recipients = [];
        $skipped = 0;

        foreach ($phones as $phone) {
            $normalized = $this->normalizePhone($phone);

            if (! $normalized) {
                $skipped++;
                continue;
            }

            $recipients[] = $normalized;
        }

        $recipients = array_values(array_unique($recipients));
        $testNumbers = config('services.sms.test_numbers');
        $testing = ! empty($testNumbers);

        if ($testing) {
            // Test mode: only the numbers from SMS_TEST_NUMBERS ever receive the message.
            $recipients = array_values(array_filter(array_map(
                fn ($number) => $this->normalizePhone($number),
                $testNumbers
            )));
            $skipped = 0;
        }

        if (empty($recipients)) {
            return back()
                ->withInput()
                ->with('error', 'No valid Bangladeshi mobile number was found to send to.');
        }

        $result = $this->sms->sendBulkSms($recipients, $message, $sender);

        return back()->with('sms_result', [
            'mode' => $mode,
            'total' => $result['total'],
            'sent' => $result['sent'],
            'failed' => $result['failed'],
            'skipped' => $skipped,
            'testing' => $testing,
            'rows' => $result['results'],
        ]);
    }

    /**
     * Turn a stored number into 11 digit local format (01769021221).
     *
     * Handles the shapes saved by the POS: 01769021221, 880169021221,
     * +880169021221 and +8801`765615237 (dirty input).
     */
    protected function normalizePhone($phone)
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        foreach (['00880', '880'] as $prefix) {
            if (str_starts_with($digits, $prefix) && strlen($digits) - strlen($prefix) >= 10) {
                $digits = substr($digits, strlen($prefix));
                break;
            }
        }

        // The POS saves +8801XXXXXXXXX without the trunk zero, put it back.
        if (strlen($digits) === 10 && $digits[0] === '1') {
            $digits = '0' . $digits;
        }

        return preg_match('/^01[3-9]\d{8}$/', $digits) ? $digits : null;
    }

    /**
     * Turn a gateway payload into a readable message.
     */
    protected function describePayload($payload)
    {
        if (! is_array($payload) || empty($payload)) {
            return 'SMS gateway did not accept the message.';
        }

        $error = $payload['error'] ?? $payload['errors'] ?? null;

        if (is_array($error)) {
            $error = implode(' ', $error);
        }

        return $error ?: 'SMS gateway did not accept the message.';
    }
}