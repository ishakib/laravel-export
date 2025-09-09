<?php

namespace Tests\Feature;

use Tests\TestCase;

class MakeExportCommandTest extends TestCase
{
	public function test_csv_exporter_exists_and_is_instantiable(): void
    {
		$class = 'Vendor\\Export\\CsvExporter';
		$this->assertTrue(class_exists($class), 'CsvExporter class does not exist');
		$instance = new \Vendor\Export\CsvExporter();
		$this->assertInstanceOf($class, $instance);
	}

	public function test_pdf_exporter_exists_and_is_instantiable()
	{
		$class = 'Vendor\\Export\\PdfExporter';
		$this->assertTrue(class_exists($class), 'PdfExporter class does not exist');
		$instance = new \Vendor\Export\PdfExporter();
		$this->assertInstanceOf($class, $instance);
	}

	public function test_export_service_exists_and_is_instantiable()
	{
		$class = 'Vendor\\Export\\ExportService';
		$this->assertTrue(class_exists($class), 'ExportService class does not exist');
		$exporter = new class implements \Vendor\Export\ExporterInterface {
			public function export(iterable $data, array $columns, string $path, string $format): string {
				return '';
			}
		};
		$service = new class($exporter) extends \Vendor\Export\ExportService {
			protected function getResourceName(): string { return 'test'; }
			protected function getData(array $columns): array { return []; }
		};
		$this->assertInstanceOf($class, $service);
	}

    /**
     * @return void
     */
    public function test_export_service_interface_methods_exist(): void
    {
		$interface = new \ReflectionClass('Vendor\\Export\\ExportServiceInterface');
		$this->assertTrue($interface->hasMethod('getData'));
		$this->assertTrue($interface->hasMethod('transformRow'));
	}

	public function test_user_export_service_transform_row(): void
    {
		$exporter = new class implements \Vendor\Export\ExporterInterface {
			public function export(iterable $data, array $columns, string $path, string $format): string { return ''; }
		};
		$service = new \Vendor\Export\UserExportService($exporter);
		$item = (object)['id' => 1, 'name' => 'Test'];
		$columns = ['id', 'name'];
		$row = $service->transformRow($item, $columns);
		$this->assertEquals(['id' => 1, 'name' => 'Test'], $row);
	}

	public function test_abstract_export_service_validate_columns()
	{
		$exporter = new class implements \Vendor\Export\ExporterInterface {
			public function export(iterable $data, array $columns, string $path, string $format): string { return ''; }
		};
		$service = new class($exporter) extends \Vendor\Export\AbstractExportService {
			public function getData(array $columns): iterable { return []; }
			public function transformRow($item, array $columns): array { return []; }
		};
        $invalid = array_values($service->validateColumns(['a', 'b'], ['a']));
        $this->assertEquals([0 => 'b'], $invalid);
	}

	public function test_abstract_export_service_generate_filename()
	{
		$exporter = new class implements \Vendor\Export\ExporterInterface {
			public function export(iterable $data, array $columns, string $path, string $format): string { return ''; }
		};
		$service = new class($exporter) extends \Vendor\Export\AbstractExportService {
			public function getData(array $columns): iterable { return []; }
			public function transformRow($item, array $columns): array { return []; }
		};
		$filename = $service->generateFilename('type', 'csv');
		$this->assertStringContainsString('type-', $filename);
		$this->assertStringEndsWith('.csv', $filename);
	}

	public function test_abstract_export_service_export_calls_exporter()
	{
		$exporter = new class implements \Vendor\Export\ExporterInterface {
			public $called = false;
			public function export(iterable $data, array $columns, string $path, string $format): string {
				$this->called = true;
				return 'ok';
			}
		};
		$service = new class($exporter) extends \Vendor\Export\AbstractExportService {
			public function getData(array $columns): iterable { return []; }
			public function transformRow($item, array $columns): array { return []; }
		};
		$result = $service->export([], [], '', 'csv');
		$this->assertTrue($exporter->called);
		$this->assertEquals('ok', $result);
	}

	public function test_user_export_request_rules_and_authorize()
	{
	// Skip this test: requires Laravel FormRequest
	$this->markTestSkipped('Laravel FormRequest dependency not available in this environment.');
	}

	public function test_export_path_helper_methods()
	{
	// Skip this test: requires Laravel storage_path helper
	$this->markTestSkipped('Laravel storage_path helper not available in this environment.');
	}

	public function test_role_permission_export_service_transform_row()
	{
		$exporter = new class implements \Vendor\Export\ExporterInterface {
			public function export(iterable $data, array $columns, string $path, string $format): string { return ''; }
		};
		$service = new \Vendor\Export\RolePermissionExportService($exporter);
		$permissionsMock = new class {
			public function pluck() {
				return new class {
					public function implode() {
						return 'create, edit';
					}
				};
			}
		};
		$item = (object)[
			'id' => 1,
			'name' => 'Admin',
			'permissions' => $permissionsMock
		];
		$columns = ['id', 'name', 'permissions'];
		$row = $service->transformRow($item, $columns);
		$this->assertEquals(['id' => 1, 'name' => 'Admin', 'permissions' => 'create, edit'], $row);
	}

	public function test_export_service_provider_register_and_boot()
	{
		$provider = new \Vendor\Export\ExportServiceProvider(null);
		$this->assertTrue(method_exists($provider, 'register'));
		$this->assertTrue(method_exists($provider, 'boot'));
	}

	public function test_export_job_can_be_constructed()
	{
	// Skip this test: requires App\Models\Export class
	$this->markTestSkipped('App\\Models\\Export class not available in this environment.');
	}
}