# LaravelCodeCommons

Shared conventions for the **mwebbers Laravel family** (LaravelCodeStructure, LaravelRDW,
Cadence, …), packaged once instead of hand-copied per repo. The PHP sibling of the family's
Python `ClaudeCodeCommons`: the same file lived in three-to-four near-identical copies that
drifted; this is the single canonical home.

Requires **PHP 8.3+** and `illuminate/support ^13.8` (Laravel 13). MIT-licensed.

## Install

```bash
composer require mwebbers/laravel-code-commons
```

Until it is on Packagist, add the repository to the consuming app's `composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/mwebbers/LaravelCodeCommons" }
]
```

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

`Livewire\TableRow` is a tiny typed row DTO shipped as the reference shape to sort over (a generic
`callable(TValue)` does not narrow an array-shape closure param under level 10, so a typed object
keeps the example clean).

### `Livewire\Concerns\WithCollectionPagination` — paginate an in-memory collection

Composes the sort concern with Livewire's pagination: it paginates an already-derived collection at
`perPage()` rows, renders one page at a time, and **resets to page 1 when the sort changes**. For a
query builder use Livewire's native `->paginate()`; this is for a set you already hold in memory.

### `Livewire\Concerns\LazyTableSkeleton` — a `#[Lazy]` table's placeholder

Pairs with `#[Lazy]` to render a skeleton while the table body hydrates in a second request. It uses
the page's **real column headers** so the skeleton auto-sizes like the live table (no layout shift).
The placeholder **view is app-provided** — ship a `livewire.placeholders.table` Blade view (it
carries your design system, e.g. Flux), or override `skeletonView()` to point at your own; a host
overrides `skeletonColumns()` / `skeletonRows()` to match its table.

### `Testing\ScopeCoverage` — the SCOPE↔test traceability checker

The family's coverage gate, extracted so every app runs the **same** checker instead of a
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

Planned for later minors: a `DuskTestCase` base for the family's browser smoke tests, and publishing
to Packagist (so consumers can drop the VCS `repositories` entry). See `CHANGELOG.md`.

## Development

```bash
composer install
composer lint      # pint --test
composer analyse   # phpstan level 10
composer test      # phpunit
```
