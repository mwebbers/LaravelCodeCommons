<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Livewire\Component;
use Mwebbers\LaravelCodeCommons\Livewire\Concerns\WithCollectionPagination;
use Mwebbers\LaravelCodeCommons\Support\Json;

/**
 * A real Livewire component exercising {@see WithCollectionPagination} over a fixed 5-row set at 2
 * rows per page, so the pagination lifecycle (page state, page reset on sort) runs for real. The
 * current page's ids are exposed in the inline template for assertions.
 *
 * @property list<int> $ids
 */
class PaginationHost extends Component
{
    use WithCollectionPagination;

    /** @var list<int> */
    public array $ids = [];

    protected function perPage(): int
    {
        return 2;
    }

    public function render(): string
    {
        // Five rows in natural order; the sort map is empty, so sort('...') only resets the page.
        $items = collect(range(1, 5))->map(fn (int $i): array => ['id' => $i]);

        $this->ids = array_values(
            $this->paginate($this->sortedBy($items, []))
                ->pluck('id')
                ->map(fn (mixed $id): int => Json::int($id))
                ->all()
        );

        return <<<'HTML'
        <div>
            @foreach ($ids as $id)
                <span>id-{{ $id }}</span>
            @endforeach
        </div>
        HTML;
    }
}
