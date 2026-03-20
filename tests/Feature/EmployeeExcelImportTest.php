<?php

namespace Tests\Feature;

use App\Services\EmployeeExcelImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeExcelImportTest extends TestCase
{
    use RefreshDatabase;

    private EmployeeExcelImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EmployeeExcelImportService::class);
    }

    /**
     * @test
     */
    public function it_can_import_employees_from_excel_file()
    {
        // Test with the actual file
        $filePath = public_path('Registro Trattamenti.xlsx');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('Excel file not found in public directory');
        }

        $results = $this->service->importEmployees($filePath, 1);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('imported', $results);
        $this->assertArrayHasKey('skipped', $results);
        $this->assertArrayHasKey('errors', $results);
    }

    /**
     * @test
     */
    public function it_handles_missing_file_gracefully()
    {
        // Test with a non-existent file path
        $results = $this->service->importEmployees('/non/existent/file.xlsx', 1);

        $this->assertEquals(0, $results['imported']);
        $this->assertNotEmpty($results['errors']);
        $this->assertStringContainsString('does not exist', $results['errors'][0]);
    }
}
