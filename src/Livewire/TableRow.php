<?php

namespace Mwebbers\LaravelCodeCommons\Livewire;

/**
 * A tiny typed row for a demo/sortable table, so sort extractors read `$row->commits`
 * (typed) instead of an untyped array offset — which keeps a consuming example clean under
 * PHPStan level 10 (a generic `callable(TValue)` does not narrow an array-shape closure
 * param, so a typed object is the clean way to sort over shaped data). A real app sorts its
 * own models/DTOs; this ships as the reference shape for {@see Concerns\WithCollectionSorting}.
 */
final readonly class TableRow
{
    public function __construct(
        public string $name,
        public string $role,
        public int $commits,
    ) {}
}
