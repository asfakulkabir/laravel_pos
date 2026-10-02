<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Bulk SMS
            </h2>
            <div class="px-3 py-1 bg-teal-50 text-teal-600 rounded-lg text-xs font-black uppercase tracking-widest">
                {{ number_format($recipientCount) }} Ready Numbers
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(! empty($testNumbers))
                <div class="flex items-start gap-3 px-5 py-4 bg-amber-50 border border-amber-200 rounded-2xl text-sm text-amber-800">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <span class="font-black uppercase tracking-wide">Test mode is on.</span>
                        Bulk broadcasts are currently limited to
                        <span class="font-mono font-bold">{{ implode(', ', $testNumbers) }}</span>.
                        Clear <span class="font-mono">SMS_TEST_NUMBERS</span> in <span class="font-mono">.env</span> to reach real customers
                        ({{ number_format($recipientCount) }} numbers).
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="px-5 py-4 bg-red-50 border border-red-200 rounded-2xl text-sm font-semibold text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('sms_result'))
                @php
                    $result = session('sms_result');
                @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
                        <div class="px-3 py-1 rounded-lg bg-teal-50 text-teal-600 text-xs font-black uppercase tracking-widest">
                            {{ $result['sent'] }} Sent
                        </div>
                        @if($result['failed'] > 0)
                            <div class="px-3 py-1 rounded-lg bg-red-50 text-red-600 text-xs font-black uppercase tracking-widest">
                                {{ $result['failed'] }} Failed
                            </div>
                        @endif
                        @if(! empty($result['skipped']))
                            <div class="px-3 py-1 rounded-lg bg-slate-100 text-slate-500 text-xs font-black uppercase tracking-widest">
                                {{ $result['skipped'] }} Skipped (invalid number)
                            </div>
                        @endif
                        <span class="text-xs font-bold text-slate-400 uppercase">
                            {{ $result['mode'] === 'single' ? 'Single message' : 'Broadcast' }}
                        </span>
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                        @foreach($result['rows'] as $row)
                            <div class="px-6 py-3 flex items-center justify-between gap-4 text-sm">
                                <span class="font-mono font-bold text-slate-700">{{ $row['number'] }}</span>
                                @if($row['success'])
                                    <span class="text-xs font-black uppercase text-teal-600">Sent</span>
                                @else
                                    <span class="text-xs font-bold text-red-500 text-right">{{ $row['error'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="post" action="{{ route('sms.send') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100">
                @csrf

                <div class="p-6 border-b border-slate-100">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Send To</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition
                            {{ old('mode', 'bulk') === 'bulk' ? 'border-teal-500 bg-teal-50/50' : 'border-slate-200 hover:bg-slate-50' }}">
                            <input type="radio" name="mode" value="bulk" class="mt-1 text-teal-600 focus:ring-teal-500"
                                {{ old('mode', 'bulk') === 'bulk' ? 'checked' : '' }}
                                onchange="syncMode()">
                            <div>
                                <div class="text-sm font-black text-slate-800">All saved customers</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Sends one message to every valid phone number saved in the customer database
                                    ({{ number_format($recipientCount) }} of {{ number_format($customerCount) }} numbers).
                                </div>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition
                            {{ old('mode') === 'single' ? 'border-teal-500 bg-teal-50/50' : 'border-slate-200 hover:bg-slate-50' }}">
                            <input type="radio" name="mode" value="single" class="mt-1 text-teal-600 focus:ring-teal-500"
                                {{ old('mode') === 'single' ? 'checked' : '' }}
                                onchange="syncMode()">
                            <div>
                                <div class="text-sm font-black text-slate-800">Single number</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Sends one message to one mobile number you type below.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="p-6 space-y-5">

                    <div id="single-fields" class="hidden">
                        <label for="phone" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Receiver Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="01769021221"
                            class="block w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                        @error('phone')
                            <p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sender" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">
                            Sender Mobile Number
                        </label>
                        <input type="text" id="sender" name="sender" required
                            value="{{ old('sender', $defaultSender) }}" placeholder="01769021221 or MAMATA FL"
                            class="block w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                        <p class="mt-1 text-xs text-slate-400">Alphanumeric sender ID or the mobile number the message shows as from.</p>
                        @error('sender')
                            <p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Message Body</label>
                        <textarea id="message" name="message" rows="5" required maxlength="1000"
                            placeholder="Write your offer or announcement here..."
                            class="block w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('message') }}</textarea>
                        <div class="mt-1 flex justify-between text-xs">
                            <span class="text-slate-400">English or Bangla (Unicode) supported.</span>
                            <span class="font-bold text-slate-400"><span id="char-count">0</span>/1000</span>
                        </div>
                        @error('message')
                            <p class="mt-1 text-xs font-semibold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 max-w-md">
                        Every SMS is billed by the gateway. Double check the number list before sending.
                    </p>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 text-sm font-black text-white bg-teal-600 rounded-xl hover:bg-teal-700 shadow focus:outline-none focus:ring-2 focus:ring-teal-300"
                        onclick="return confirm('Send this message now?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l18 8-18 8 4-8-4-8zm4 8h14" />
                        </svg>
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const singleFields = document.getElementById('single-fields');
            const phoneInput = document.getElementById('phone');
            const messageInput = document.getElementById('message');
            const charCount = document.getElementById('char-count');

            function syncMode() {
                const isSingle = document.querySelector('input[name=mode]:checked').value === 'single';
                singleFields.classList.toggle('hidden', !isSingle);
                phoneInput.required = isSingle;
            }

            function syncCount() {
                charCount.textContent = messageInput.value.length;
            }

            document.addEventListener('DOMContentLoaded', function () {
                syncMode();
                syncCount();
                messageInput.addEventListener('input', syncCount);
            });
        </script>
    @endpush
</x-app-layout>