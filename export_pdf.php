<?php
require 'vendor/autoload.php'; // Load mPDF melalui Composer
use Mpdf\Mpdf;

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

// Inisialisasi mPDF
$mpdf = new Mpdf();

// Menambahkan judul laporan
$html = '<h2 style="text-align: center;">Laporan Monitoring Pekerjaan</h2>';

// Menambahkan tanggal saat export
$date = date('d-m-Y');
$html .= '<p style="text-align: center;">Tanggal Export: ' . $date . '</p>';

// Menambahkan tabel header
$html .= '
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>Tanggal Complain</th>
            <th>Target Selesai</th>
            <th>Uraian</th>
            <th>ID Programmer</th>
            <th>Tanggal Input</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>';

// Isi data dari database
if ($logs && $logs->num_rows > 0) {
    while ($log = $logs->fetch_assoc()) {
        $html .= '
        <tr>
            <td>' . $log['tgl_complain'] . '</td>
            <td>' . $log['target_selesai'] . '</td>
            <td>' . $log['uraian'] . '</td>
            <td>' . $log['user_id'] . '</td>
            <td>' . $log['tgl_input'] . '</td>
            <td>' . $log['status'] . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align: center;">Data tidak ditemukan</td></tr>';
}

// Menutup tabel
$html .= '</tbody></table>';

// Tambahkan HTML ke mPDF
$mpdf->WriteHTML($html);

// Output file PDF
$fileName = 'Laporan_Pekerjaan_' . $date . '.pdf';
$mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE); // Menampilkan langsung di browser

// Menutup koneksi database
$conn->close();
exit;
?>
