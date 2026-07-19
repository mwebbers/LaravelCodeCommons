<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Fixtures;

/**
 * The typed row the sorting tests sort over: a typed property read keeps the extractors clean
 * under PHPStan level 10 (a generic `callable(TValue)` does not narrow an array-shape closure
 * param, so shaped-array rows would need scattered casts). A real app sorts its own models/DTOs.
 */
final readonly class Row
{
    public function __construct(
        public string $name,
        public int $commits,
    ) {}
}
