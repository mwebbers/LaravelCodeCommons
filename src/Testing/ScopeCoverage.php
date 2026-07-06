<?php

namespace Mwebbers\LaravelCodeCommons\Testing;

/**
 * A SCOPE<->test traceability checker, extracted so every consuming app runs the SAME gate
 * instead of a hand-copied `ScopeCoverageTest`. It reads every feature ID from the "## Features"
 * section of a `SCOPE.md` and every ID referenced by a `#[Group("F-00X")]` attribute (or
 * `@group F-00X`) across a tests directory, and reports either side's IDs the other lacks:
 *
 *   - a feature in SCOPE.md with no test        -> behaviour is not guaranteed;
 *   - a test group pointing at an unknown ID     -> a stale / typo'd reference.
 *
 * It is **presence-only** (a single trivial tagged test satisfies it), so the behavioural lock is
 * the composite of this check, the coverage minimum, PHPStan, and the review step of the loop. An
 * app's `ScopeCoverageTest` is a thin PHPUnit wrapper that asserts on {@see mismatch()}.
 */
final class ScopeCoverage
{
    private const FEATURE_PATTERN = '/\bF-\d{3,}\b/';

    private const GROUP_PATTERN = '/(?:#\[Group\(|@group\s+)[\'"]?(F-\d{3,})/';

    public function __construct(
        private string $scopeFile,
        private string $testsDir,
    ) {}

    /**
     * Feature IDs defined in the "## Features" section of SCOPE.md.
     *
     * @return list<string>
     */
    public function featuresInScope(): array
    {
        $text = (string) file_get_contents($this->scopeFile);

        if (! str_contains($text, '## Features') || ! str_contains($text, '## Out of scope')) {
            throw new \RuntimeException(
                "SCOPE file {$this->scopeFile} must contain both a '## Features' and an '## Out of scope' heading."
            );
        }

        $section = explode('## Out of scope', explode('## Features', $text, 2)[1], 2)[0];
        preg_match_all(self::FEATURE_PATTERN, $section, $matches);

        return array_values(array_unique($matches[0]));
    }

    /**
     * Feature IDs referenced by a `#[Group(...)]`/`@group` in any `.php` file under the tests
     * directory. Pass the calling test's own path as $excludeFile so its documentation of these
     * very patterns is not counted as a reference.
     *
     * @return list<string>
     */
    public function featuresInTests(?string $excludeFile = null): array
    {
        $ids = [];
        $exclude = $excludeFile === null ? null : basename($excludeFile);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->testsDir, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            if (! $file instanceof \SplFileInfo) {
                continue;
            }
            if ($file->getExtension() !== 'php' || $file->getFilename() === $exclude) {
                continue;
            }

            preg_match_all(self::GROUP_PATTERN, (string) file_get_contents($file->getPathname()), $matches);
            foreach ($matches[1] as $id) {
                $ids[$id] = true;
            }
        }

        return array_keys($ids);
    }

    /**
     * The two-sided mismatch between SCOPE.md and the tests, each sorted: `missing` = features
     * with no test, `stale` = test groups pointing at an unknown feature. Both empty = in sync.
     *
     * @return array{missing: list<string>, stale: list<string>}
     */
    public function mismatch(?string $excludeFile = null): array
    {
        $scope = $this->featuresInScope();
        $tested = $this->featuresInTests($excludeFile);

        $missing = array_values(array_diff($scope, $tested));
        sort($missing);

        $stale = array_values(array_diff($tested, $scope));
        sort($stale);

        return ['missing' => $missing, 'stale' => $stale];
    }
}
