<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Support;

use Mwebbers\LaravelCodeCommons\Support\DescriptionKey;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** The descriptions are the shapes MiWebb's WeFact holds (read 2026-11-05/06), the old form and the new. */
final class DescriptionKeyTest extends TestCase
{
    /** @return array<string, array{string, list<string>}> */
    public static function named(): array
    {
        return [
            'after the label' => ['<b>Webhosting Standaard:</b> example.nl', ['example.nl']],
            'after the label, specifications below' => ["<b>VPS 8C:</b> vps9.miwebb.com\r\n- 8 High Performance CPU Cores", ['vps9.miwebb.com']],
            'after the label, specifications behind a dash' => ['Webhosting Standaard: example.nl - 30 GB SSD - E-mail', ['example.nl']],
            'a list' => ['<b>WordPress onderhoud:</b> mikasasports.eu, b2b.mikasasports.eu', ['mikasasports.eu', 'b2b.mikasasports.eu']],
            'a list with a duplicate' => ['WordPress onderhoud: a.nl, A.nl', ['a.nl']],
            'bold around the key' => ['Webhosting Pro: <b>Shop.Example.NL</b>', ['shop.example.nl']],
            'a non-breaking space' => ['Webhosting Standaard:&nbsp;example.nl', ['example.nl']],
            'a trailing dot and slash' => ['Domein: example.nl./', ['example.nl']],
            'www kept' => ['Webhosting Standaard: www.example.nl', ['www.example.nl']],
            'a product name with its own parentheses' => ['WordPress Complianz Privacy Suite (GDPR/CCPA) premium plugin licentie: chromecars.de', ['chromecars.de']],
            'a punycode TLD' => ['Domein: example.xn--p1ai', ['example.xn--p1ai']],
            'a blank first line' => ["\r\n<b>Domein:</b> example.nl", ['example.nl']],
            'the old form' => ['<b>Webhosting: </b>Standaard (example.nl)', ['example.nl']],
            'the old form, the last parentheses' => ['WordPress: Complianz Privacy Suite (GDPR/CCPA) premium plugin licentie (chromecars.de)', ['chromecars.de']],
            'the old server form' => ['<b>VPS</b>: 8C (vps9.miwebb.com)', ['vps9.miwebb.com']],
            'the old list form' => ['Webhosting: Groot (a.nl, b.nl)', ['a.nl', 'b.nl']],
        ];
    }

    /** @param list<string> $keys */
    #[DataProvider('named')]
    public function test_keys_are_read_from_either_form(string $description, array $keys): void
    {
        $this->assertSame($keys, DescriptionKey::keys($description));
    }

    /** @return array<string, array{string}> */
    public static function unnamed(): array
    {
        return [
            'the placeholder' => ['<b>Webhosting Standaard:</b> *.*'],
            'the server placeholder' => ['VPS 2C: vps*.miwebb.com'],
            'the placeholder in parentheses' => ['<b>Webhosting: </b>Groot (*.*)'],
            'no key at all' => ['Webhosting: Standaard'],
            'not a hostname' => ['Webhosting Standaard: n.n.b.'],
            'half a list' => ['Maatwerk software onderhoud: portal.knafdigital.nl, api.knafdigital.nl, GSM'],
            'a path' => ['WordPress Clonable business plugin licentie: bunzlfoodservice.nl/en'],
            'free text after the key' => ['SaaS: Advanced search: advalvas.vu.nl Advanced search geeft betere resultaten'],
            'a key only on a later line' => ["Webhosting Standaard:\r\nexample.nl"],
            'parentheses that are no key' => ['Clonable: XML-feed renewal (dagelijks)'],
            'empty' => [''],
        ];
    }

    #[DataProvider('unnamed')]
    public function test_anything_else_names_no_key(string $description): void
    {
        $this->assertSame([], DescriptionKey::keys($description));
        $this->assertNull(DescriptionKey::key($description));
    }

    public function test_key_is_the_one_key_or_none(): void
    {
        $this->assertSame('example.nl', DescriptionKey::key('Webhosting Standaard: example.nl'));
        $this->assertNull(DescriptionKey::key('Webhosting Standaard: a.nl, b.nl'), 'several keys are not one');
    }

    public function test_normalize_gives_one_spelling(): void
    {
        $this->assertSame('example.nl', DescriptionKey::normalize('  Example.NL. '));
        $this->assertSame('xn--bcher-kva.example', DescriptionKey::normalize('Bücher.Example'));
        $this->assertNull(DescriptionKey::normalize('n.n.b.'));
        $this->assertNull(DescriptionKey::normalize('*.*'));
        $this->assertNull(DescriptionKey::normalize('localhost'));
        $this->assertNull(DescriptionKey::normalize(''));
    }
}
