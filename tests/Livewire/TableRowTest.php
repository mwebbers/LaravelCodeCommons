<?php

namespace Mwebbers\LaravelCodeCommons\Tests\Livewire;

use Mwebbers\LaravelCodeCommons\Livewire\TableRow;
use PHPUnit\Framework\TestCase;

final class TableRowTest extends TestCase
{
    public function test_it_exposes_its_typed_fields(): void
    {
        $row = new TableRow(name: 'Ada Lovelace', role: 'Pioneer', commits: 42);

        $this->assertSame('Ada Lovelace', $row->name);
        $this->assertSame('Pioneer', $row->role);
        $this->assertSame(42, $row->commits);
    }
}
