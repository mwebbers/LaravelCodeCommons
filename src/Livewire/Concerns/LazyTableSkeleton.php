<?php

namespace Mwebbers\LaravelCodeCommons\Livewire\Concerns;

use Illuminate\Contracts\View\View;

/**
 * A `#[Lazy]` table page renders this skeleton placeholder while its body hydrates in a second
 * request. The skeleton uses the page's REAL column headers (so its columns auto-size like the live
 * table — minimal layout shift, no custom width CSS) and a row count near the page's typical height.
 *
 * The placeholder **view is app-provided** (it carries the app's own markup / design system, e.g.
 * Flux), so a consumer must ship a `livewire.placeholders.table` Blade view — or override
 * {@see skeletonView()} to point at its own — that renders the `columns`/`rows` it receives. A host
 * overrides {@see skeletonColumns()} / {@see skeletonRows()} to match its own table.
 */
trait LazyTableSkeleton
{
    public function placeholder(): View
    {
        return view($this->skeletonView(), [
            'columns' => $this->skeletonColumns(),
            'rows' => $this->skeletonRows(),
        ]);
    }

    /** The Blade view the placeholder renders. Override to point at the app's own skeleton view. */
    protected function skeletonView(): string
    {
        return 'livewire.placeholders.table';
    }

    /**
     * The live table's column headers, in order, so the skeleton's columns size like the real ones.
     * A host overrides this to match its table.
     *
     * @return list<string>
     */
    protected function skeletonColumns(): array
    {
        return ['#'];
    }

    /** Roughly the page's typical row count, so the skeleton's height ≈ the live table. */
    protected function skeletonRows(): int
    {
        return 5;
    }
}
