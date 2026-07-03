<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Testing;

use Mwebbers\LaravelCodeCommons\Testing\ScopeCoverage;
use PHPUnit\Framework\TestCase;

final class ScopeCoverageTest extends TestCase
{
    private function fixture(string $set): ScopeCoverage
    {
        $base = dirname(__DIR__).'/Fixtures/'.$set;

        return new ScopeCoverage($base.'/SCOPE.md', $base.'/tests');
    }

    public function test_a_matched_scope_and_test_set_has_no_mismatch(): void
    {
        $result = $this->fixture('matched')->mismatch();

        $this->assertSame(['missing' => [], 'stale' => []], $result);
    }

    public function test_it_reads_every_defined_feature_id(): void
    {
        $this->assertSame(['F-001', 'F-002'], $this->fixture('matched')->featuresInScope());
    }

    public function test_it_reports_a_feature_with_no_test_as_missing(): void
    {
        $result = $this->fixture('mismatched')->mismatch();

        $this->assertSame(['F-003'], $result['missing']);
    }

    public function test_it_reports_a_test_group_for_an_unknown_feature_as_stale(): void
    {
        $result = $this->fixture('mismatched')->mismatch();

        $this->assertSame(['F-999'], $result['stale']);
    }

    public function test_a_scope_file_without_the_required_headings_is_rejected(): void
    {
        $coverage = new ScopeCoverage(__FILE__, dirname(__DIR__));

        $this->expectException(\RuntimeException::class);
        $coverage->featuresInScope();
    }
}
