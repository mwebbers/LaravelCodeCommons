{{-- Minimal stand-in for a consumer's own skeleton view. A real app ships a styled one
     (e.g. Flux); this just renders the columns/rows the concern passes so placeholder()
     resolves and its data can be asserted. Like a real consumer's view, it normalizes the
     two column-entry forms — a bare label, or a `label => alignment` pair — itself: the
     concern hands the array over untouched. --}}
@php
    $normalizedColumns = [];
    foreach ($columns as $label => $align) {
        $normalizedColumns[] = is_int($label) ? [$align, 'start'] : [$label, $align];
    }
@endphp
<div data-skeleton>
    <div data-columns>@foreach ($normalizedColumns as [$label, $align])<span data-align="{{ $align }}">{{ $label }}</span>@endforeach</div>
    <div data-rows>{{ $rows }}</div>
</div>
