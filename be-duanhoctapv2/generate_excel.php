<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Tên');
$sheet->setCellValue('B1', 'Email');
$sheet->setCellValue('C1', 'Mật khẩu');
$sheet->setCellValue('D1', 'Số điện thoại');
$sheet->setCellValue('E1', 'Địa chỉ');
$sheet->setCellValue('F1', 'Vai trò');

$sheet->setCellValue('A2', 'Test Import 1');
$sheet->setCellValue('B2', 'import1@test.com');
$sheet->setCellValue('C2', 'password');
$sheet->setCellValue('D2', '0911223344');
$sheet->setCellValue('E2', 'Hà Nội');
$sheet->setCellValue('F2', 'Student');

$sheet->setCellValue('A3', 'Test Import 2');
$sheet->setCellValue('B3', 'import2@test.com');
$sheet->setCellValue('C3', 'password');
$sheet->setCellValue('D3', '0988776655');
$sheet->setCellValue('E3', 'HCM');
$sheet->setCellValue('F3', 'Parent');

$writer = new Xlsx($spreadsheet);
$writer->save('public/test_import_users.xlsx');
echo "Dã tao public/test_import_users.xlsx\n";
