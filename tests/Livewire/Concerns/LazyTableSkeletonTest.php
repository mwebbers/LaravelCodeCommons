<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Mwebbers\LaravelCodeCommons\Tests\TestCase;

final class LazyTableSkeletonTest extends TestCase
{
    public function test_the_placeholder_renders_the_default_view_with_the_hosts_columns_and_rows(): void
    {
        $view = (new SkeletonHost)->placeholderView();

        // The columns arrive untouched — normalizing the two entry forms (bare label,
        // `label => alignment` pair) is the app-provided view's job, not the concern's.
        $this->assertSame('livewire.placeholders.table', $view->name());
        $this->assertSame(['Name', 'Role', 'Commits' => 'end'], $view->getData()['columns']);
        $this->assertSame(8, $view->getData()['rows']);
    }

    public function test_the_rendered_skeleton_carries_the_real_column_headers(): void
    {
        $html = (new SkeletonHost)->placeholderView()->render();

        // The skeleton uses the live table's real headers so its columns auto-size (no layout
        // shift). In a `label => alignment` pair the KEY is the header: 'Commits' renders
        // end-aligned, exactly where the live table puts it — never its alignment value.
        $this->assertStringContainsString('<span data-align="start">Name</span>', $html);
        $this->assertStringContainsString('<span data-align="end">Commits</span>', $html);
    }
}
