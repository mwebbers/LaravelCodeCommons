# Changelog

All notable changes to this package are documented here, newest first. The format follows
[Keep a Changelog](https://keepachangelog.com/); versions are `X.Y.Z`.

**Release gate:** the version in `composer.json` (once tagged), the topmost heading here, and the
annotated git tag must match.

## [Unreleased]

### Planned

- A `DuskTestCase` base for the family's browser smoke tests.
- Publish to Packagist so consumers can drop the VCS `repositories` entry.

## [0.2.0]

### Added

- **`Livewire\Concerns\WithCollectionPagination`** — paginates an in-memory collection at `perPage()`
  rows, composing `WithCollectionSorting`, and resets to page 1 when the sort changes. Generalized
  from Cadence's `PaginatesTickets`.
- **`Livewire\Concerns\LazyTableSkeleton`** — a `#[Lazy]` table's placeholder that renders the page's
  real column headers (so its columns auto-size like the live table). The placeholder view is
  app-provided; the new `skeletonView()` method makes the view name overridable.
- A **Testbench harness** (`orchestra/testbench`) so the Livewire-runtime concerns are tested for
  real (page state, page-reset-on-sort, the resolved placeholder view). The package now requires
  `livewire/livewire`; the dependency-light pieces (Json, ScopeCoverage, WithCollectionSorting,
  TableRow) still test without booting an app.

## [0.1.0]

### Added

- **`Support\Json`** — typed `mixed`-narrowing accessors (`obj`/`objOrNull`/`rows`/`str`/
  `nullableStr`/`int`/`float`/`bool`/`strings`), the single sanctioned JSON/`config()` boundary
  that keeps a consumer at PHPStan level 10. Extracted from the family's duplicated copies.
- **`Livewire\Concerns\WithCollectionSorting`** — a model-agnostic click-to-sort concern over an
  in-memory collection, **stable in both directions** (equal-key rows keep their natural order;
  descending uses `sortByDesc`, not a `reverse()` of the ascending sort). Plus **`Livewire\TableRow`**,
  the typed reference row shape to sort over.
- **`Testing\ScopeCoverage`** — the family's SCOPE↔test traceability checker, so every app runs
  one shared implementation instead of a hand-copied `ScopeCoverageTest`.
- Own gate: PHPUnit, Pint, PHPStan level 10, GitHub Actions CI. MIT license.
