<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Illuminate\Support\Collection;
use Mwebbers\LaravelCodeCommons\Livewire\Concerns\WithCollectionSorting;
use Mwebbers\LaravelCodeCommons\Livewire\TableRow;

/**
 * A minimal named host for {@see WithCollectionSorting} so the concern's sort logic can be
 * exercised without a Livewire runtime (and so static analysis has a concrete type to resolve).
 * It sorts over {@see TableRow} — the typed row the concern is designed for, so the extractor
 * reads a typed property instead of an untyped array offset.
 */
final class SortingHost
{
    use WithCollectionSorting;

    /**
     * @param  Collection<int, TableRow>  $items
     * @return Collection<int, TableRow>
     */
    public function order(Collection $items): Collection
    {
        return $this->sortedBy($items, ['commits' => fn (TableRow $row): int => $row->commits]);
    }
}
