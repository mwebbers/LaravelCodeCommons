<?php

namespace Mwebbers\LaravelCodeCommons\Support;

/**
 * Typed accessors for `mixed` values that enter an app untyped — decoded JSON from an
 * upstream API and `config()` reads. They narrow a `mixed` at the boundary into the
 * concrete type the caller needs, **defensively**: a wrong-typed or missing value yields
 * the default instead of crashing. This is the single sanctioned place a JSON/`config()`
 * `mixed` becomes a real type, which is what lets a consuming app keep its static-analysis
 * floor at PHPStan level 10 without scattered casts.
 */
final class Json
{
    /**
     * Narrow a value to an array (an empty array when it is anything else).
     *
     * @return array<array-key, mixed>
     */
    public static function obj(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * Narrow a value to an array, or null when it is not one.
     *
     * @return array<array-key, mixed>|null
     */
    public static function objOrNull(mixed $value): ?array
    {
        return is_array($value) ? $value : null;
    }

    /**
     * The array elements of a decoded JSON list — non-array elements are dropped, so a
     * malformed row never derails the iteration.
     *
     * @return list<array<array-key, mixed>>
     */
    public static function rows(mixed $value): array
    {
        $rows = [];

        if (is_array($value)) {
            foreach ($value as $row) {
                if (is_array($row)) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    /** A string value, with scalars stringified and anything else falling back to $default. */
    public static function str(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $default;
    }

    /** A string value, or null when it is not a string (no stringification). */
    public static function nullableStr(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    /** An int value: ints as-is, numeric strings/floats coerced, anything else $default. */
    public static function int(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    /** A float value: floats/ints as-is, numeric strings coerced, anything else $default. */
    public static function float(mixed $value, float $default = 0.0): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        return $default;
    }

    /** A bool value, using PHP's truthiness (handles JSON true/false, 1/0, "1"/""). */
    public static function bool(mixed $value): bool
    {
        return (bool) $value;
    }

    /**
     * The string elements of a value (a config array of strings, say), non-strings dropped
     * and keys discarded.
     *
     * @return list<string>
     */
    public static function strings(mixed $value): array
    {
        $strings = [];

        foreach (self::obj($value) as $item) {
            if (is_string($item)) {
                $strings[] = $item;
            }
        }

        return $strings;
    }
}
