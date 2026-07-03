{{-- Minimal stand-in for a consumer's own skeleton view. A real app ships a styled one
     (e.g. Flux); this just renders the columns/rows the concern passes so placeholder()
     resolves and its data can be asserted. --}}
<div data-skeleton>
    <div data-columns>@foreach ($columns as $column)<span>{{ $column }}</span>@endforeach</div>
    <div data-rows>{{ $rows }}</div>
</div>
