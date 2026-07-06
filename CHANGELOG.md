# Changelog

All notable changes to this package are documented here, newest first. The format follows
[Keep a Changelog](https://keepachangelog.com/); versions are `X.Y.Z`.

**Release gate:** the version in `composer.json` (once tagged), the topmost heading here, and the
annotated git tag must match.

## [Unreleased]

### Planned

- A `DuskTestCase` base for browser smoke tests.
- Publish to Packagist so consumers can drop the VCS `repositories` entry.

## [0.3.1]

### Changed

- Documentation and package metadata rewritten to be self-contained: descriptions no longer
  reference specific consuming apps, sibling packages, or their maintainer.

## [0.3.0]

### Changed

- Infection mutation testing over the package source (`composer infection`, plus a scheduled/manual
  `mutation` workflow). It is a periodic signal, not the blocking gate — the measured baseline is
  100% mutation code coverage at 74% Covered Code MSI (thresholds set to 70).
- **`Livewire\Concerns\LazyTableSkeleton`** — `skeletonColumns()` accepts `label => alignment`
  pairs (`'start'|'center'|'end'`) alongside bare labels: `array<int|string, string>` instead of
  `list<string>`, so a column the live table end-aligns starts exactly where it will end up. The
  app-provided view normalizes both forms; the concern passes the array through untouched. Caveat:
  a purely numeric label (`'2024'`) cannot carry an alignment pair — PHP coerces such keys to int.

### Fixed

- **`Livewire\Concerns\WithCollectionPagination`** — an out-of-range `?page=` now clamps to the
  last real page instead of rendering an empty table (misleading: the data exists, just not 99
  pages of it). The `?page=` param is user-controlled input, so `paginate()` bounds it on both
  sides.

## [0.2.0]

### Added

- **`Livewire\Concerns\WithCollectionPagination`** — paginates an in-memory collection at `perPage()`
  rows, composing `WithCollectionSorting`, and resets to page 1 when the sort changes.
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
  that keeps a consumer at PHPStan level 10. Extracted from duplicated per-app copies.
- **`Livewire\Concerns\WithCollectionSorting`** — a model-agnostic click-to-sort concern over an
  in-memory collection, **stable in both directions** (equal-key rows keep their natural order;
  descending uses `sortByDesc`, not a `reverse()` of the ascending sort). Plus **`Livewire\TableRow`**,
  the typed reference row shape to sort over.
- **`Testing\ScopeCoverage`** — a SCOPE↔test traceability checker, so every app runs
  one shared implementation instead of a hand-copied `ScopeCoverageTest`.
- Own gate: PHPUnit, Pint, PHPStan level 10, GitHub Actions CI. MIT license.
