<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Livewire\Livewire;
use Mwebbers\LaravelCodeCommons\Tests\TestCase;

final class WithCollectionPaginationTest extends TestCase
{
    public function test_it_renders_one_page_of_rows_at_a_time(): void
    {
        Livewire::test(PaginationHost::class)
            ->assertSet('ids', [1, 2]) // page 1 at perPage 2
            ->assertSee('id-1')
            ->assertSee('id-2')
            ->assertDontSee('id-3');
    }

    public function test_going_to_the_next_page_shows_the_next_rows(): void
    {
        Livewire::test(PaginationHost::class)
            ->call('gotoPage', 2)
            ->assertSet('ids', [3, 4])
            ->call('gotoPage', 3)
            ->assertSet('ids', [5]); // the short last page
    }

    public function test_an_out_of_range_page_clamps_to_the_last_real_page(): void
    {
        // ?page= is user-controlled input; the data exists, just not 99 pages of it — so the
        // paginator shows the last real page instead of a misleading empty table.
        Livewire::test(PaginationHost::class)
            ->call('gotoPage', 99)
            ->assertSet('ids', [5])
            ->assertSee('id-5');
    }

    public function test_sorting_resets_to_the_first_page(): void
    {
        Livewire::test(PaginationHost::class)
            ->call('gotoPage', 3)
            ->assertSet('ids', [5])
            ->call('sort', 'anything') // re-orders the whole set, so back to page 1
            ->assertSet('ids', [1, 2]);
    }

    public function test_sort_still_toggles_the_direction_it_composes(): void
    {
        Livewire::test(PaginationHost::class)
            ->call('sort', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortDirection', 'asc')
            ->call('sort', 'name')
            ->assertSet('sortDirection', 'desc');
    }
}
