# Contributing

`LaravelCodeCommons` is a small library: the canonical home for conventions shared across its
consuming Laravel apps. Keep it dependency-light and framework-version aligned with those
consumers (Laravel 13 / PHP 8.3).

## The gate

Every change must pass, as one gate (CI runs all three):

```bash
composer lint      # ./vendor/bin/pint --test
composer analyse   # ./vendor/bin/phpstan analyse --memory-limit=1G   (level 10)
composer test      # ./vendor/bin/phpunit
```

New behaviour ships with a test. A `mixed` boundary belongs in `Support\Json`, not scattered casts —
that is what keeps the analysis floor at level 10.

## Adding a piece

Only extract something into this package once it is genuinely shared (it already exists, near-identical,
in two or more consuming apps) and is either dependency-light or can be tested here behind a Testbench
harness. UI pieces that need the Livewire runtime or Flux Pro credentials go behind such a harness so
the package stays installable and gate-green without them.

## Branching & releases — Git Flow

Daily work is a `feature/*` branch off `develop`, merged back `--no-ff`. `main` is the tagged
production line; every commit on it is a tagged release. Cut a release on a `release/*` branch off
`develop`: bump nothing in code, move `CHANGELOG.md [Unreleased]` under a new `X.Y.Z` heading, run
the gate, merge into `main` **and** back into `develop`, tag `vX.Y.Z`, and
`git push origin main develop --tags`. A library ships no `composer.lock`.

## Consuming a new version

Downstream apps require this via a VCS `repositories` entry until it is on Packagist. When adopting a
new minor, run the consumer's own gate — the shared code is behavioural, so its tests are the safety net.
