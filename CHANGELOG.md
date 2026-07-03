# Changelog

All notable changes to this package are documented here, newest first. The format follows
[Keep a Changelog](https://keepachangelog.com/); versions are `X.Y.Z`.

**Release gate:** the version in `composer.json` (once tagged), the topmost heading here, and the
annotated git tag must match.

## [Unreleased]

### Planned

- `Livewire\Concerns\WithCollectionPagination` and `LazyTableSkeleton` (need a Livewire/Testbench
  harness to test in a package context).
- A `DuskTestCase` base for the family's browser smoke tests.
- Publish to Packagist so consumers can drop the VCS `repositories` entry.

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
