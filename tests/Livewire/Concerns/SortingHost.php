<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Illuminate\Support\Collection;
use Mwebbers\LaravelCodeCommons\Livewire\Concerns\WithCollectionSorting;
use Mwebbers\LaravelCodeCommons\Tests\Fixtures\Row;

/**
 * A minimal named host for {@see WithCollectionSorting} so the concern's sort logic can be
 * exercised without a Livewire runtime (and so static analysis has a concrete type to resolve).
 * It sorts over the typed {@see Row} fixture, so the extractor reads a typed property instead
 * of an untyped array offset.
 */
final class SortingHost
{
    use WithCollectionSorting;

    /**
     * @param  Collection<int, Row>  $items
     * @return Collection<int, Row>
     */
    public function order(Collection $items): Collection
    {
        return $this->sortedBy($items, ['commits' => fn (Row $row): int => $row->commits]);
    }
}
