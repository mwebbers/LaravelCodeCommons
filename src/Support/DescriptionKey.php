<?php

namespace Mwebbers\LaravelCodeCommons\Support;

/**
 * The key of a WeFact subscription description: the domain or hostname a product is for, in one spelling. Pure, so
 * the same description always gives the same keys.
 *
 * Since 2026-11 MiWebb's descriptions have one form: the key right after the label on the first line,
 * `<b>Webhosting Standaard:</b> example.nl`, several separated by commas, specifications on the lines below or behind
 * ` - `. The older form put it in parentheses, `Webhosting: Standaard (example.nl)`, and is still read: the last
 * parentheses on the first line, so a product name holding its own (`Complianz (GDPR/CCPA)`) does not get in the way.
 *
 * All or nothing: a key list with one part that is no hostname (`*.*`, `n.n.b.`, `GSM`, a path) gives no keys, because
 * half a list is a guess.
 */
final class DescriptionKey
{
    /** Labels of letters, digits and hyphens; the last at least two letters or punycode, so `n.n.b` is no hostname. */
    private const HOSTNAME = '/^(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2,}|xn--[a-z0-9-]*[a-z0-9])$/';

    /**
     * Every key on the first line, in order and without duplicates; an empty list when it names none.
     *
     * @return list<string>
     */
    public static function keys(string $description): array
    {
        $line = self::firstLine($description);

        $colon = strpos($line, ':');
        if ($colon !== false) {
            // What follows the label, up to a ` - ` that starts the specifications.
            $afterLabel = preg_split('/\s+-\s+/', substr($line, $colon + 1), 2);
            $keys = self::list(is_array($afterLabel) ? $afterLabel[0] : '');
            if ($keys !== []) {
                return $keys;
            }
        }

        if (preg_match_all('/\(([^()]*)\)/', $line, $parentheses) > 0) {
            return self::list((string) end($parentheses[1]));
        }

        return [];
    }

    /** The one key of a description, or null when it names none or several. */
    public static function key(string $description): ?string
    {
        $keys = self::keys($description);

        return count($keys) === 1 ? $keys[0] : null;
    }

    /**
     * Trimmed, without a trailing dot, IDN to punycode and lowercase; null when what is left is no hostname. Without
     * the intl extension a name that is not plain ASCII is no key, never a guess.
     */
    public static function normalize(string $value): ?string
    {
        $host = rtrim(trim($value), '.');
        if ($host === '') {
            return null;
        }

        if (preg_match('/[^\x20-\x7e]/', $host) === 1) {
            $ascii = function_exists('idn_to_ascii')
                ? idn_to_ascii($host, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46)
                : false;
            if (! is_string($ascii)) {
                return null;
            }
            $host = $ascii;
        }

        $host = strtolower($host);

        return preg_match(self::HOSTNAME, $host) === 1 ? $host : null;
    }

    /** @return list<string> */
    private static function list(string $candidates): array
    {
        $keys = [];
        foreach (explode(',', $candidates) as $candidate) {
            $key = self::normalize(rtrim(trim($candidate), '/'));
            if ($key === null) {
                return [];
            }
            if (! in_array($key, $keys, true)) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /** The first non-empty line as text: tags out, entities and non-breaking spaces decoded, spaces collapsed. */
    private static function firstLine(string $description): string
    {
        $text = html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\u{00A0}", "\r\n", "\r"], [' ', "\n", "\n"], $text);

        foreach (explode("\n", $text) as $line) {
            $line = trim((string) preg_replace('/[ \t]+/', ' ', $line));
            if ($line !== '') {
                return $line;
            }
        }

        return '';
    }
}
