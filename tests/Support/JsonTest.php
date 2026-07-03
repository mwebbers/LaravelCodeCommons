<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Support;

use Mwebbers\LaravelCodeCommons\Support\Json;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    public function test_obj_narrows_to_an_array_or_empty(): void
    {
        $this->assertSame(['a' => 1], Json::obj(['a' => 1]));
        $this->assertSame([], Json::obj('not an array'));
        $this->assertSame([], Json::obj(null));
    }

    public function test_obj_or_null_returns_null_for_non_arrays(): void
    {
        $this->assertSame(['a' => 1], Json::objOrNull(['a' => 1]));
        $this->assertNull(Json::objOrNull('x'));
    }

    public function test_rows_keeps_only_array_elements(): void
    {
        $this->assertSame([['id' => 1], ['id' => 2]], Json::rows([['id' => 1], 'skip', ['id' => 2], 7]));
        $this->assertSame([], Json::rows('not a list'));
    }

    public function test_str_stringifies_scalars_and_falls_back(): void
    {
        $this->assertSame('hello', Json::str('hello'));
        $this->assertSame('42', Json::str(42));
        $this->assertSame('3.5', Json::str(3.5));
        $this->assertSame('', Json::str(['array']));
        $this->assertSame('fallback', Json::str(null, 'fallback'));
    }

    public function test_nullable_str_does_not_stringify(): void
    {
        $this->assertSame('x', Json::nullableStr('x'));
        $this->assertNull(Json::nullableStr(42));
        $this->assertNull(Json::nullableStr(null));
    }

    public function test_int_coerces_numeric_and_falls_back(): void
    {
        $this->assertSame(5, Json::int(5));
        $this->assertSame(3, Json::int(3.9));
        $this->assertSame(7, Json::int('7'));
        $this->assertSame(0, Json::int('not numeric'));
        $this->assertSame(42, Json::int(null, 42));
    }

    public function test_float_coerces_numeric_and_falls_back(): void
    {
        $this->assertSame(5.0, Json::float(5));
        $this->assertSame(2.5, Json::float('2.5'));
        $this->assertSame(1.5, Json::float('x', 1.5));
    }

    public function test_bool_uses_truthiness(): void
    {
        $this->assertTrue(Json::bool(1));
        $this->assertTrue(Json::bool('1'));
        $this->assertFalse(Json::bool(0));
        $this->assertFalse(Json::bool(''));
    }

    public function test_strings_keeps_only_string_elements(): void
    {
        $this->assertSame(['a', 'b'], Json::strings(['a', 1, 'b', ['nested']]));
        $this->assertSame([], Json::strings('not an array'));
    }
}
