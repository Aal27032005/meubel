<?php
require_once 'config/auth.php';
require_once 'config/db.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Ambil semua data
try {
    $items = $pdo->query("SELECT * FROM furniture ORDER BY name ASC")->fetchAll();
} catch (\PDOException $e) {
    die("Gagal memuat data: " . $e->getMessage());
}

// Hitung total
$total_stock = 0;
$total_value = 0;
foreach ($items as $item) {
    $total_stock += $item['stock'];
    $total_value += ($item['price'] * $item['stock']);
}

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Inventaris Mebel');

// ── Judul ────────────────────────────────────────────────────────────────────
$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', 'JATIJAYA FURNITURE - LAPORAN INVENTARIS');
$sheet->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1c1c1c']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(30);

$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A2', 'Tanggal: ' . date('d F Y') . '  |  Waktu: ' . date('H:i:s'));
$sheet->getStyle('A2')->applyFromArray([
    'font'      => ['size' => 10, 'color' => ['rgb' => '666666']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);

// ── Header Tabel ─────────────────────────────────────────────────────────────
$headers = ['No', 'SKU', 'Nama Produk', 'Kategori', 'Material', 'Stok', 'Harga (Rp)', 'Total Nilai (Rp)'];
$cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
$row = 4;

foreach ($cols as $i => $col) {
    $sheet->setCellValue("{$col}{$row}", $headers[$i]);
}

$sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '343a40']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
]);
$sheet->getRowDimension($row)->setRowHeight(22);

// ── Data Rows ─────────────────────────────────────────────────────────────────
$row = 5;
$no = 1;
foreach ($items as $item) {
    $row_value = $item['price'] * $item['stock'];
    $is_low    = $item['stock'] <= 5;
    $bg_color  = ($no % 2 === 0) ? 'F8F9FA' : 'FFFFFF';

    $sheet->setCellValue("A{$row}", $no++);
    $sheet->setCellValue("B{$row}", $item['sku']);
    $sheet->setCellValue("C{$row}", $item['name']);
    $sheet->setCellValue("D{$row}", $item['category']);
    $sheet->setCellValue("E{$row}", $item['material']);
    $sheet->setCellValue("F{$row}", (int)$item['stock']);
    $sheet->setCellValue("G{$row}", (float)$item['price']);
    $sheet->setCellValue("H{$row}", (float)$row_value);

    // Format angka
    $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0');

    // Warna zebra
    $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg_color]],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
    ]);

    // Stok rendah → warna merah
    if ($is_low) {
        $sheet->getStyle("F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'DC3545']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE5E5']],
        ]);
    }

    // Alignment
    $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("F{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    $row++;
}

// ── Baris Ringkasan ───────────────────────────────────────────────────────────
$row++;
$sheet->mergeCells("A{$row}:E{$row}");
$sheet->setCellValue("A{$row}", 'TOTAL');
$sheet->setCellValue("F{$row}", $total_stock);
$sheet->setCellValue("H{$row}", $total_value);

$sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '198754']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '156846']]],
]);
$sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("G{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0');

// ── Keterangan ────────────────────────────────────────────────────────────────
$row += 2;
$sheet->mergeCells("A{$row}:H{$row}");
$sheet->setCellValue("A{$row}", '(*) Stok berwarna merah = stok rendah (≤ 5 unit), segera lakukan pengadaan.');
$sheet->getStyle("A{$row}")->applyFromArray([
    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '888888']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
]);

// ── Lebar Kolom ──────────────────────────────────────────────────────────────
$sheet->getColumnDimension('A')->setWidth(6);
$sheet->getColumnDimension('B')->setWidth(14);
$sheet->getColumnDimension('C')->setWidth(28);
$sheet->getColumnDimension('D')->setWidth(16);
$sheet->getColumnDimension('E')->setWidth(16);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(18);
$sheet->getColumnDimension('H')->setWidth(20);

// ── Output File ───────────────────────────────────────────────────────────────
$filename = 'Inventaris_Jatijaya_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
