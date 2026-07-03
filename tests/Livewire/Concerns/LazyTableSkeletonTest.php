<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire\Concerns;

use Mwebbers\LaravelCodeCommons\Tests\TestCase;

final class LazyTableSkeletonTest extends TestCase
{
    public function test_the_placeholder_renders_the_default_view_with_the_hosts_columns_and_rows(): void
    {
        $view = (new SkeletonHost)->placeholderView();

        $this->assertSame('livewire.placeholders.table', $view->name());
        $this->assertSame(['Name', 'Role', 'Commits'], $view->getData()['columns']);
        $this->assertSame(8, $view->getData()['rows']);
    }

    public function test_the_rendered_skeleton_carries_the_real_column_headers(): void
    {
        $html = (new SkeletonHost)->placeholderView()->render();

        // The skeleton uses the live table's real headers so its columns auto-size (no layout shift).
        $this->assertStringContainsString('Name', $html);
        $this->assertStringContainsString('Commits', $html);
    }
}
