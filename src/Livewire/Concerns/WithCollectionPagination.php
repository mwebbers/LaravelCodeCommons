<?php

namespace Mwebbers\LaravelCodeCommons\Livewire\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Mwebbers\LaravelCodeCommons\Support\Json;

/**
 * Paginate an in-memory collection in a Livewire table, composing click-to-sort
 * ({@see WithCollectionSorting}) with Livewire's pagination. Sorting re-orders the whole set, so it
 * jumps back to page 1; an out-of-range `?page=` clamps to the last real page. The host keeps the
 * FULL collection for its totals/counts and passes the sorted set to {@see paginate()}; only the
 * current page is rendered.
 *
 * Most apps paginate at the query builder (`->paginate()`); this collection-based variant is for an
 * already-in-memory or derived set. Pair it with {@see LazyTableSkeleton} for a lazy table.
 */
trait WithCollectionPagination
{
    use WithCollectionSorting {
        sort as private sortColumn;
    }
    use WithPagination;

    /** Sorting re-orders the whole set, so jump back to the first page. */
    public function sort(string $column): void
    {
        $this->sortColumn($column);
        $this->resetPage();
    }

    /** Rows shown per page. Override in the host to taste. */
    protected function perPage(): int
    {
        return 10;
    }

    /**
     * Turn an already-sorted collection into a paginator at {@see perPage()} rows.
     *
     * @template TValue
     *
     * @param  Collection<int, TValue>  $items
     * @return LengthAwarePaginator<int, TValue>
     */
    protected function paginate(Collection $items): LengthAwarePaginator
    {
        // getPage() reflects the user-controllable ?page= param (mixed) — narrow it through Json
        // (the one sanctioned place a mixed becomes an int) and clamp to a real page: at least 1,
        // at most the last page. An out-of-range ?page= thus shows the last real page instead of a
        // misleading empty table — the data exists, just not 99 pages of it.
        $lastPage = max(1, (int) ceil($items->count() / $this->perPage()));
        $page = min($lastPage, max(1, Json::int($this->getPage(), 1)));

        return new LengthAwarePaginator(
            $items->forPage($page, $this->perPage())->values(),
            $items->count(),
            $this->perPage(),
            $page,
            ['pageName' => 'page'],
        );
    }
}
