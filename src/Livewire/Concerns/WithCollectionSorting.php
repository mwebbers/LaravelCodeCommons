<?php

namespace Mwebbers\LaravelCodeCommons\Livewire\Concerns;

use Illuminate\Support\Collection;

/**
 * Click-to-sort for a Livewire table backed by an in-memory collection. The host component
 * keeps the data; this concern owns only the sort *state* (which column, which direction) and
 * the toggle, so any table can reuse it without coupling to a model:
 *
 *   - a header calls `wire:click="sort('name')"` to toggle the column / direction;
 *   - `render()` passes the data through `sortedBy($items, $keys)`, where `$keys` maps each
 *     sortable column to a value-extractor — the only app-specific part.
 *
 * A null or unknown column leaves the collection in its natural order. The sort is **stable**
 * in both directions: rows with an equal key keep their natural order whether ascending or
 * descending (descending uses `sortByDesc`, never a `reverse()` of the ascending sort, which
 * would flip ties). The concern uses only Illuminate collections — no Livewire runtime — so it
 * is testable standalone, though it is designed to be `use`d by a Livewire component.
 */
trait WithCollectionSorting
{
    /** The column currently sorted on, or null for the collection's natural order. */
    public ?string $sortBy = null;

    public string $sortDirection = 'asc';

    /** Toggle the sort: the same column flips direction; a new column starts ascending. */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Re-order a collection by the active sort. The host supplies the sortable columns as a
     * `column => extractor` map; a null or unknown column leaves the collection in its natural
     * order. Always reindexed (`->values()`) so the result is a clean list for the view.
     *
     * @template TValue
     *
     * @param  Collection<int, TValue>  $items
     * @param  array<string, callable(TValue): (int|string)>  $keys
     * @return Collection<int, TValue>
     */
    protected function sortedBy(Collection $items, array $keys): Collection
    {
        $extractor = $this->sortBy === null ? null : ($keys[$this->sortBy] ?? null);
        if ($extractor === null) {
            return $items->values();
        }

        // Stable in both directions: sortByDesc for descending, NOT reverse() of the ascending
        // sort (which would flip equal-key rows out of their natural order when toggling).
        $sorted = $this->sortDirection === 'desc'
            ? $items->sortByDesc($extractor, SORT_REGULAR)
            : $items->sortBy($extractor, SORT_REGULAR);

        return $sorted->values();
    }
}
