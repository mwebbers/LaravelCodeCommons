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

    /**
     * Both entry forms at once: bare labels and a `label => alignment` pair, like a live
     * table whose numeric column is end-aligned.
     *
     * @return array<int|string, string>
     */
    protected function skeletonColumns(): array
    {
        return ['Name', 'Role', 'Commits' => 'end'];
    }

    protected function skeletonRows(): int
    {
        return 8;
    }
}
