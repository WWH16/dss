{{-- ADDED: the stall's evaluations list, moved out of staff/staff_dashboard.blade.php so the staff standings page can show the same list. Needs $evaluations (paginated). The search form submits back to whichever page includes it. --}}
    <div class="bg-white rounded-xl border border-neutral-200/70 shadow-sm overflow-hidden">
        {{-- Header with Search & Filter Controls --}}
        <div class="p-5 sm:p-6 pb-4 border-b border-neutral-100 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-neutral-900 tracking-tight flex items-center gap-2">
                        <span>Student Evaluations</span>
                        <span class="text-[11px] font-semibold text-neutral-500 bg-neutral-100 px-2 py-0.5 rounded-md">Anonymous Submissions</span>
                    </h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Feedback logs submitted by students for your stall</p>
                </div>

                {{-- Search Form --}}
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <div class="relative w-full sm:w-56">
                        <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-neutral-400 text-sm pointer-events-none"></ion-icon>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search in comments…" aria-label="Search student evaluation comments"
                            class="w-full pl-8 pr-3 py-1.5 bg-neutral-50 border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700 focus:ring-1 focus:ring-brand-700">
                    </div>
                    <select name="sort" onchange="this.form.submit()" aria-label="Sort evaluations" class="px-2.5 py-1.5 bg-neutral-50 border border-neutral-300 rounded-md text-xs font-medium focus:outline-none focus:border-brand-700">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
                        <option value="rating_high" {{ request('sort') === 'rating_high' ? 'selected' : '' }}>Highest Rating</option>
                        <option value="rating_low" {{ request('sort') === 'rating_low' ? 'selected' : '' }}>Lowest Rating</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                    @if(request('q') || request('sort'))
                        <a href="{{ url()->current() }}" class="text-xs text-neutral-500 hover:text-neutral-900 font-semibold shrink-0">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        @if($evaluations->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-md bg-brand-50 border border-brand-100 flex items-center justify-center mb-3 text-brand-700 mx-auto">
                    <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
                </div>
                <p class="text-sm font-bold text-neutral-900 mb-0.5">No evaluations found</p>
                <p class="text-xs text-neutral-500 max-w-xs mx-auto">
                    {{ request('q') ? 'No student feedback matched your search criteria.' : 'Your stall has not received any student evaluations yet.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[650px] hidden md:table">
                    <thead>
                        <tr class="text-[11px] text-neutral-500 font-bold uppercase tracking-wider bg-neutral-50/80 border-b border-neutral-200/70">
                            <th class="py-3.5 px-5 font-semibold">Date</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Cleanliness</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Service</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Taste</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Price</th>
                            <th class="py-3.5 px-3 text-center font-semibold">Average</th>
                            <th class="py-3.5 px-5 font-semibold">Student Feedback</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm">
                        @foreach($evaluations as $eval)
                            {{-- CHANGED: the date and "x ago" text are shown in Philippine time (created_at is stored in UTC), as on the printable report. --}}
                            @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                            <tr class="hover:bg-neutral-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-medium text-neutral-600 text-xs tabular-nums whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($eval->created_at, 'UTC')->timezone('Asia/Manila')->format('M d, Y') }}
                                    <span class="block text-[11px] text-neutral-500">{{ \Carbon\Carbon::parse($eval->created_at, 'UTC')->timezone('Asia/Manila')->diffForHumans() }}</span>
                                </td>
                                <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->cleanliness >= 4 ? 'text-emerald-700' : ($eval->cleanliness <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->cleanliness }}★
                                </td>
                                <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->service >= 4 ? 'text-emerald-700' : ($eval->service <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->service }}★
                                </td>
                                <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->taste >= 4 ? 'text-emerald-700' : ($eval->taste <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->taste }}★
                                </td>
                                <td class="py-3.5 px-3 text-center text-xs font-bold {{ $eval->price >= 4 ? 'text-emerald-700' : ($eval->price <= 2 ? 'text-rose-600' : 'text-neutral-800') }} tabular-nums">
                                    {{ $eval->price }}★
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-xs font-bold tabular-nums {{ $avg >= 4 ? 'bg-brand-50 text-brand-900 border border-brand-200' : 'bg-neutral-100 text-neutral-800' }}">
                                        {{ number_format($avg, 1) }}★
                                    </span>
                                </td>
                                <td class="py-3.5 px-5">
                                    @if($eval->comment)
                                        <p class="text-xs text-neutral-700 line-clamp-2 max-w-xs font-normal" title="{{ $eval->comment }}">
                                            “{{ $eval->comment }}”
                                        </p>
                                    @else
                                        <span class="text-neutral-500 text-xs italic">No comment provided</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Mobile View -->
                <div class="md:hidden divide-y divide-neutral-100">
                    @foreach($evaluations as $eval)
                        {{-- CHANGED: date shown in Philippine time, as in the desktop table. --}}
                        @php $avg = ($eval->cleanliness + $eval->service + $eval->taste + $eval->price) / 4; @endphp
                        <div class="p-4 flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-neutral-600 tabular-nums">
                                    {{ \Carbon\Carbon::parse($eval->created_at, 'UTC')->timezone('Asia/Manila')->format('M d, Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1 bg-brand-50 px-2 py-0.5 rounded-md text-xs font-bold text-brand-900 border border-brand-200">
                                    {{ number_format($avg, 1) }}★
                                </span>
                            </div>
                            <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Clean</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->cleanliness }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Serv</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->service }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Taste</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->taste }}★</span>
                                </div>
                                <div class="bg-neutral-50 rounded-md p-1.5">
                                    <span class="block text-[11px] font-semibold text-neutral-500 uppercase">Price</span>
                                    <span class="font-bold text-neutral-900">{{ $eval->price }}★</span>
                                </div>
                            </div>
                            @if($eval->comment)
                                <p class="text-xs text-neutral-700 bg-neutral-50 p-2.5 rounded-md border border-neutral-200/60 mt-1 italic">
                                    “{{ $eval->comment }}”
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pagination Footer --}}
            <div class="p-4 border-t border-neutral-100 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-neutral-500">
                <div>
                    Showing <strong class="text-neutral-900">{{ $evaluations->firstItem() ?? 0 }}</strong> to <strong class="text-neutral-900">{{ $evaluations->lastItem() ?? 0 }}</strong> of <strong class="text-neutral-900">{{ $evaluations->total() }}</strong> submissions
                </div>
                <div>
                    {{ $evaluations->links() }}
                </div>
            </div>
        @endif
    </div>
