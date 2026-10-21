@props(['label', 'value', 'suffix' => null, 'icon', 'route' => null])

{{-- ADDED: the stat card from the admin Overview, shared by the admin, staff and student dashboards. The default slot is the caption under the value; with a route the card is a link. --}}
<a @if($route) href="{{ $route }}" @endif class="group bg-white rounded-xl border border-neutral-200/70 p-5 shadow-sm shadow-brand-950/5 {{ $route ? 'hover:border-brand-300 hover:shadow-md' : '' }} transition-all flex flex-col justify-between">
    <div>
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider group-hover:text-brand-800 transition-colors">{{ $label }}</span>
            <div class="w-9 h-9 rounded-lg bg-brand-600 hidden sm:flex items-center justify-center text-white {{ $route ? 'group-hover:bg-brand-700' : '' }} transition-all">
                <ion-icon name="{{ $icon }}" class="text-lg"></ion-icon>
            </div>
        </div>
        <div class="text-2xl sm:text-3xl font-bold text-neutral-900 tabular-nums tracking-tight leading-none text-center">
            {{ $value }}@if($suffix)<span class="text-sm sm:text-base font-semibold text-neutral-500 ml-1">{{ $suffix }}</span>@endif
        </div>
    </div>
    <div class="flex items-center justify-between mt-3 pt-2 border-t border-neutral-100/70 text-xs">
        <span class="text-neutral-500 font-medium">{{ $slot }}</span>
        @if($route)
        <span class="text-brand-700 opacity-0 group-hover:opacity-100 transition-opacity flex items-center" aria-hidden="true">
            <ion-icon name="chevron-forward-outline" class="text-sm"></ion-icon>
        </span>
        @endif
    </div>
</a>
