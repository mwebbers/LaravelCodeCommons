# LaravelCodeCommons

Shared Laravel/Livewire conventions, packaged once instead of hand-copied per project. These
pieces used to live as near-identical copies across consuming apps and drifted; this package is
the single canonical home.

Requires **PHP 8.3+** and `illuminate/support ^13.8` (Laravel 13). MIT-licensed.

## Install

```bash
composer require mwebbers/laravel-code-commons
```

The package is on Packagist and a tagged release is picked up automatically, so no VCS
`repositories` entry is needed — consumers that still carry one can drop it.

## Versioning

From **1.0.0** this package follows [semantic versioning](https://semver.org/): everything under
`src/` is public API, and breaking changes to it land only in a new major — preceded where
practical by an `@deprecated` tag in the last minor. New features arrive in minors, fixes in
patches. Consumers require `^1.0`.

## What's in it

### `Support\Json` — typed `mixed` narrowing

The single sanctioned place a JSON/`config()` `mixed` becomes a real type, which is what lets a
consuming app keep PHPStan at **level 10** without scattered casts. Every accessor degrades to a
default instead of crashing on a wrong-typed or missing value.

```php
use Mwebbers\LaravelCodeCommons\Support\Json;

Json::str($node['title'] ?? null, '(untitled)');   // string, scalars stringified
Json::int($config['limit'] ?? null, 50);           // int, numeric strings coerced
Json::rows($response->json());                      // list<array> — non-array rows dropped
```

### `Livewire\Concerns\WithCollectionSorting` — a stable sortable-table concern

Click-to-sort for a Livewire table over an in-memory collection. Owns only the sort *state* and
the toggle; the host supplies a `column => extractor` map. The sort is **stable in both
directions** — equal-key rows keep their natural order whether ascending or descending (it uses
`sortByDesc`, never a `reverse()` of the ascending sort, which would flip ties). Uses only
Illuminate collections, so the logic is testable without a Livewire runtime.

```php
use Mwebbers\LaravelCodeCommons\Livewire\Concerns\WithCollectionSorting;

class Tickets extends Component
{
    use WithCollectionSorting;

    public function render()
    {
        $rows = $this->sortedBy($this->tickets(), [
            'title'    => fn ($t) => $t->title,
            'deadline' => fn ($t) => $t->deadline?->getTimestamp() ?? PHP_INT_MAX,
        ]);
        // ...
    }
}
```

### `Livewire\Concerns\WithCollectionPagination` — paginate an in-memory collection

Composes the sort concern with Livewire's pagination: it paginates an already-derived collection at
`perPage()` rows, renders one page at a time, and **resets to page 1 when the sort changes**. An
**out-of-range `?page=` clamps to the last real page** — the URL is user-controlled input, and the
data exists (just not 99 pages of it), so an empty table would be misleading. For a query builder
use Livewire's native `->paginate()`; this is for a set you already hold in memory.

### `Livewire\Concerns\LazyTableSkeleton` — a `#[Lazy]` table's placeholder

Pairs with `#[Lazy]` to render a skeleton while the table body hydrates in a second request. It uses
the page's **real column headers** so the skeleton auto-sizes like the live table (no layout shift).
The placeholder **view is app-provided** — ship a `livewire.placeholders.table` Blade view (it
carries your design system, e.g. Flux), or override `skeletonView()` to point at your own; a host
overrides `skeletonColumns()` / `skeletonRows()` to match its table. A `skeletonColumns()` entry is
either a bare label or a `'label' => alignment` pair (`start`/`center`/`end`), so a column the live
table end-aligns starts where it will end up — the view normalizes both forms. One caveat: a purely
numeric label (`'2024'`) cannot carry an alignment pair, because PHP coerces such array keys to int.

### `Testing\ScopeCoverage` — the SCOPE↔test traceability checker

A coverage gate, extracted so every consuming app runs the **same** checker instead of a
hand-copied `ScopeCoverageTest`. It reads the feature IDs from a `SCOPE.md` `## Features` section
and the `#[Group('F-00X')]` references across a tests directory, and reports either side's gaps.

An app's `ScopeCoverageTest` becomes a thin wrapper:

```php
use Mwebbers\LaravelCodeCommons\Testing\ScopeCoverage;
use PHPUnit\Framework\TestCase;

final class ScopeCoverageTest extends TestCase
{
    public function test_scope_and_tests_stay_in_sync(): void
    {
        $coverage = new ScopeCoverage(dirname(__DIR__, 2).'/SCOPE.md', dirname(__DIR__, 2).'/tests');
        $result = $coverage->mismatch(__FILE__);

        $this->assertSame([], $result['missing'], 'SCOPE features with no test: '.implode(', ', $result['missing']));
        $this->assertSame([], $result['stale'], 'Test groups for unknown features: '.implode(', ', $result['stale']));
    }
}
```

## Roadmap

Planned for later minors: a `DuskTestCase` base for browser smoke tests. See `CHANGELOG.md`.

## Development

```bash
composer install
composer lint      # pint --test
composer analyse   # phpstan level 10
composer test      # phpunit
```
