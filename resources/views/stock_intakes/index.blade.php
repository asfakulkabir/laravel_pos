<x-app-layout>
    <div class="p-4 sm:p-6">
        <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 border-b border-slate-200 pb-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 flex items-center gap-2">
                    <span class="text-teal-600">📜</span> Stock Intake History
                </h1>
                <p class="text-sm text-slate-500 mt-1">Review, audit, and print intake sessions captured from barcode scanning.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('stock.intake.terminal') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700">
                    ➕ New Intake
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left column: Recent Sessions --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm lg:col-span-1">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Recent Sessions</h2>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $sessions->count() }} listed</span>
                </div>
                <div class="divide-y divide-slate-100 max-h-[40rem] overflow-y-auto">
                    @forelse ($sessions as $session)
                        <a href="?session_id={{ $session->id }}"
                           class="block px-5 py-4 transition hover:bg-slate-50
                           @if($selected_session && $session->id == $selected_session->id) bg-teal-50 border-l-4 border-teal-500 @endif">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-base font-bold text-slate-900">{{ $session->reference }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $session->created_at->format('M d, Y g:i A') }}
                                        @if ($session->user)
                                            · by {{ $session->user->name }}
                                        @endif
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">
                            No intake sessions recorded yet.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Right column: Detailed View --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm lg:col-span-2">
                @if ($selected_session)
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Reference</p>
                            <h2 class="text-2xl font-black text-slate-900">{{ $selected_summary->reference }}</h2>
                            <p class="text-sm text-slate-500">
                                {{ $selected_summary->created_at->format('M d, Y g:i A') }}
                                @if ($selected_summary->created_by)
                                    · User: {{ $selected_summary->created_by->name }}
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="window.print()"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                🖨 Print Page
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-slate-100">
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Items</p>
                            <p class="text-2xl font-black text-slate-900">{{ $selected_summary->item_count }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Quantity Added</p>
                            <p class="text-2xl font-black text-slate-900">{{ $selected_summary->total_quantity }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Reference ID</p>
                            <p class="text-lg font-bold text-slate-900">{{ $selected_summary->reference }}</p>
                        </div>
                    </div>

                    <div class="px-6 py-5 overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Product</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Color</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Size</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">SKU</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600 text-right">Previous</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600 text-right">New</th>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600 text-right">Added</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($selected_items as $item)
                                    <tr>
                                        <td class="px-3 py-2 font-semibold text-slate-900">{{ $item->product_name }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $item->color_name }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ $item->size_name }}</td>
                                        <td class="px-3 py-2 font-mono text-slate-600">{{ $item->sku }}</td>
                                        <td class="px-3 py-2 text-slate-700 text-right">{{ $item->previous_stock }}</td>
                                        <td class="px-3 py-2 text-slate-700 text-right">{{ $item->new_stock }}</td>
                                        <td class="px-3 py-2 text-teal-700 font-bold text-right">+{{ $item->quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-4 text-center text-sm text-slate-500">
                                            No line items recorded for this session.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-20 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">No session selected</h3>
                        <p class="text-slate-500 mt-1">Select a session from the list to view its details.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
