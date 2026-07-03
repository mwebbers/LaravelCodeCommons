<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Illuminate\Contracts\View\View;
use Mwebbers\LaravelCodeCommons\Livewire\Concerns\LazyTableSkeleton;

/**
 * A host exposing {@see LazyTableSkeleton} with overridden columns/rows, so the placeholder view
 * and its data can be asserted. It is not a Livewire component — the skeleton contract is plain.
 */
class SkeletonHost
{
    use LazyTableSkeleton;

    public function placeholderView(): View
    {
        return $this->placeholder();
    }

    /** @return list<string> */
    protected function skeletonColumns(): array
    {
        return ['Name', 'Role', 'Commits'];
    }

    protected function skeletonRows(): int
    {
        return 8;
    }
}
