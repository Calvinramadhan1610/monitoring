<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet melalui Composer
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Inisiasi koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring_pekerjaan";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch log data dari tabel
$queryLogs = "SELECT * FROM t_log";
$logs = $conn->query($queryLogs);

// Buat objek spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul laporan
$sheet->setCellValue('A1', 'Laporan Monitoring Pekerjaan');
$sheet->mergeCells('A1:F1'); // Merge sel agar judul lebih lebar
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14); // Set font besar dan tebal

// Menampilkan tanggal saat ekspor
$date = date('d-m-Y');
$sheet->setCellValue('A2', 'Tanggal Export: ' . $date);
$sheet->mergeCells('A2:F2'); // Merge sel untuk tanggal
$sheet->getStyle('A2')->getFont()->setItalic(true); // Set format italic

// Set header tabel
$sheet->setCellValue('A4', 'Tanggal Complain');
$sheet->setCellValue('B4', 'Target Selesai');
$sheet->setCellValue('C4', 'Uraian');
$sheet->setCellValue('D4', 'ID Programmer');
$sheet->setCellValue('E4', 'Tanggal Input');
$sheet->setCellValue('F4', 'Status');

// Bold untuk header
$sheet->getStyle('A4:F4')->getFont()->setBold(true);

// Isi data dari database ke tabel
$row = 5; // Baris setelah header
if ($logs && $logs->num_rows > 0) {
    while ($log = $logs->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $log['tgl_complain']);
        $sheet->setCellValue('B' . $row, $log['target_selesai']);
        $sheet->setCellValue('C' . $row, $log['uraian']);
        $sheet->setCellValue('D' . $row, $log['user_id']);
        $sheet->setCellValue('E' . $row, $log['tgl_input']);
        $sheet->setCellValue('F' . $row, $log['status']);
        $row++;
    }
}

// Auto-resize kolom agar teks terlihat rapi
foreach(range('A','F') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Simpan file spreadsheet
$writer = new Xlsx($spreadsheet);
$fileName = 'Laporan_Pekerjaan_' . $date . '.xlsx'; // Menambahkan tanggal di nama file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');

// Menutup koneksi database
$conn->close();
exit;
?>
