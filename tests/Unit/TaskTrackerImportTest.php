<?php

namespace Tests\Unit;

use App\Services\TaskTrackerImport;
use PHPUnit\Framework\TestCase;

class TaskTrackerImportTest extends TestCase
{
    /** @test */
    public function it_reads_the_tracker_header_below_a_title_row()
    {
        $rows = (new TaskTrackerImport())->read(
            dirname(__DIR__).'/Fixtures/task-tracker.csv',
            'csv'
        );

        $this->assertCount(2, $rows);
        $this->assertSame('BUG-001', $rows[0]['bug_cr']);
        $this->assertSame('Supplier Registry / Suppliers Tab', $rows[0]['screen_feature']);
        $this->assertSame('2026-03-23', $rows[0]['date_reported']);
        $this->assertSame('Done', $rows[0]['status']);
        $this->assertSame('2026-03-23', $rows[1]['date_reported']);
    }
}
