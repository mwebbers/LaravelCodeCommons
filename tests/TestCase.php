<?php

namespace Mwebbers\LaravelCodeCommons\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\View;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Base for the tests that need a booted Laravel app + the Livewire runtime (the pagination and
 * skeleton concerns). The dependency-light tests (Json, ScopeCoverage, WithCollectionSorting,
 * TableRow) extend PHPUnit's TestCase directly and do not pay for this.
 */
abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // The app-provided skeleton view (LazyTableSkeleton) — a consumer ships its own; here a
        // minimal fixture stands in so placeholder() resolves.
        View::addLocation(__DIR__.'/Fixtures/views');
    }

    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [LivewireServiceProvider::class];
    }
}
