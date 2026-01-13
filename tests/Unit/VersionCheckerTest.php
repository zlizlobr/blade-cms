<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Module\Services\VersionChecker;
use PHPUnit\Framework\TestCase;

class VersionCheckerTest extends TestCase
{
    private VersionChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new VersionChecker;
    }

    /** @test */
    public function it_parses_version_correctly(): void
    {
        $result = $this->checker->parseVersion('1.2.3');

        $this->assertEquals(['major' => 1, 'minor' => 2, 'patch' => 3], $result);
    }

    /** @test */
    public function it_parses_version_with_missing_parts(): void
    {
        $result = $this->checker->parseVersion('2.5');

        $this->assertEquals(['major' => 2, 'minor' => 5, 'patch' => 0], $result);
    }

    /** @test */
    public function it_strips_prerelease_metadata(): void
    {
        $result = $this->checker->parseVersion('1.0.0-beta.1+build.123');

        $this->assertEquals(['major' => 1, 'minor' => 0, 'patch' => 0], $result);
    }

    /** @test */
    public function it_satisfies_exact_version_constraint(): void
    {
        $this->assertTrue($this->checker->satisfies('1.0.0', '1.0.0'));
        $this->assertTrue($this->checker->satisfies('2.3.4', '=2.3.4'));
        $this->assertFalse($this->checker->satisfies('1.0.1', '1.0.0'));
    }

    /** @test */
    public function it_satisfies_caret_constraint(): void
    {
        // ^1.2.3 allows >=1.2.3 and <2.0.0
        $this->assertTrue($this->checker->satisfies('1.2.3', '^1.2.3'));
        $this->assertTrue($this->checker->satisfies('1.2.4', '^1.2.3'));
        $this->assertTrue($this->checker->satisfies('1.9.9', '^1.2.3'));
        $this->assertFalse($this->checker->satisfies('1.2.2', '^1.2.3'));
        $this->assertFalse($this->checker->satisfies('2.0.0', '^1.2.3'));
    }

    /** @test */
    public function it_satisfies_tilde_constraint(): void
    {
        // ~1.2.3 allows >=1.2.3 and <1.3.0
        $this->assertTrue($this->checker->satisfies('1.2.3', '~1.2.3'));
        $this->assertTrue($this->checker->satisfies('1.2.4', '~1.2.3'));
        $this->assertTrue($this->checker->satisfies('1.2.9', '~1.2.3'));
        $this->assertFalse($this->checker->satisfies('1.2.2', '~1.2.3'));
        $this->assertFalse($this->checker->satisfies('1.3.0', '~1.2.3'));
    }

    /** @test */
    public function it_satisfies_greater_than_or_equal_constraint(): void
    {
        $this->assertTrue($this->checker->satisfies('2.0.0', '>=2.0.0'));
        $this->assertTrue($this->checker->satisfies('2.0.1', '>=2.0.0'));
        $this->assertTrue($this->checker->satisfies('3.0.0', '>=2.0.0'));
        $this->assertFalse($this->checker->satisfies('1.9.9', '>=2.0.0'));
    }

    /** @test */
    public function it_satisfies_less_than_or_equal_constraint(): void
    {
        $this->assertTrue($this->checker->satisfies('1.0.0', '<=2.0.0'));
        $this->assertTrue($this->checker->satisfies('2.0.0', '<=2.0.0'));
        $this->assertFalse($this->checker->satisfies('2.0.1', '<=2.0.0'));
    }

    /** @test */
    public function it_satisfies_greater_than_constraint(): void
    {
        $this->assertTrue($this->checker->satisfies('2.0.1', '>2.0.0'));
        $this->assertFalse($this->checker->satisfies('2.0.0', '>2.0.0'));
        $this->assertFalse($this->checker->satisfies('1.9.9', '>2.0.0'));
    }

    /** @test */
    public function it_satisfies_less_than_constraint(): void
    {
        $this->assertTrue($this->checker->satisfies('1.9.9', '<2.0.0'));
        $this->assertFalse($this->checker->satisfies('2.0.0', '<2.0.0'));
        $this->assertFalse($this->checker->satisfies('2.0.1', '<2.0.0'));
    }

    /** @test */
    public function it_checks_core_compatibility(): void
    {
        $this->assertTrue($this->checker->isCompatibleWithCore('^1.0', '1.5.0'));
        $this->assertTrue($this->checker->isCompatibleWithCore('~2.3', '2.3.5'));
        $this->assertFalse($this->checker->isCompatibleWithCore('^1.0', '2.0.0'));
    }

    /** @test */
    public function it_returns_true_for_empty_compatibility(): void
    {
        $this->assertTrue($this->checker->isCompatibleWithCore('', '1.0.0'));
        $this->assertTrue($this->checker->isCompatibleWithCore(null, '2.0.0'));
    }
}
