<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Illuminate\Support\Collection;
use Mwebbers\LaravelCodeCommons\Tests\Fixtures\Row;
use PHPUnit\Framework\TestCase;

final class WithCollectionSortingTest extends TestCase
{
    /**
     * A, B, C share 1 commit in this natural order; D has 2.
     *
     * @return Collection<int, Row>
     */
    private function items(): Collection
    {
        return collect([
            new Row('A', 1),
            new Row('B', 1),
            new Row('C', 1),
            new Row('D', 2),
        ]);
    }

    public function test_toggle_cycles_column_and_direction(): void
    {
        $host = new SortingHost;

        $host->sort('commits');
        $this->assertSame('commits', $host->sortBy);
        $this->assertSame('asc', $host->sortDirection);

        $host->sort('commits'); // same column flips direction
        $this->assertSame('desc', $host->sortDirection);

        $host->sort('name'); // a new column starts ascending
        $this->assertSame('name', $host->sortBy);
        $this->assertSame('asc', $host->sortDirection);
    }

    public function test_a_null_or_unknown_column_keeps_the_natural_order(): void
    {
        $host = new SortingHost;

        // No sort set.
        $this->assertSame(['A', 'B', 'C', 'D'], $host->order($this->items())->pluck('name')->all());

        // A column with no extractor in the map.
        $host->sortBy = 'unmapped';
        $this->assertSame(['A', 'B', 'C', 'D'], $host->order($this->items())->pluck('name')->all());
    }

    public function test_ascending_sort_is_stable_for_equal_keys(): void
    {
        $host = new SortingHost;
        $host->sortBy = 'commits';
        $host->sortDirection = 'asc';

        $this->assertSame(['A', 'B', 'C', 'D'], $host->order($this->items())->pluck('name')->all());
    }

    public function test_descending_sort_keeps_equal_key_rows_in_their_natural_order(): void
    {
        $host = new SortingHost;
        $host->sortBy = 'commits';
        $host->sortDirection = 'desc';

        // D (2 commits) first, then the tied rows in NATURAL order A,B,C — not reversed to C,B,A
        // (the bug a plain reverse() of the ascending sort would produce).
        $this->assertSame(['D', 'A', 'B', 'C'], $host->order($this->items())->pluck('name')->all());
    }
}
