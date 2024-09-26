<?php
require 'vendor/autoload.php'; // Load PhpSpreadsheet melalui Composer
use PhpOffice\PhpSpreadsheet\IOFactory;

// Inisiasi koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring_pekerjaan";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fungsi untuk import data dari file Excel, hanya baris yang ada isinya yang akan diimport
function importExcel($filePath, $conn) {
    $spreadsheet = IOFactory::load($filePath);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    $importedRows = 0;

    // Lewati baris pertama jika merupakan header
    foreach ($sheetData as $key => $row) {
        if ($key === 1) {
            continue; // Lewati header
        }

        // Ambil data dari kolom yang sesuai dan cek apakah kolom memiliki data
        $tgl_complain = isset($row['A']) ? trim($row['A']) : '';
        $target_selesai = isset($row['B']) ? trim($row['B']) : '';
        $uraian = isset($row['C']) ? trim($row['C']) : '';
        $user_id = isset($row['D']) ? trim($row['D']) : '';
        $tgl_input = isset($row['E']) ? trim($row['E']) : '';
        $status = isset($row['F']) ? trim($row['F']) : '';

        // Hanya import jika semua kolom penting memiliki nilai (tidak kosong)
        if (!empty($tgl_complain) && !empty($target_selesai) && !empty($uraian) && !empty($user_id) && !empty($tgl_input) && !empty($status)) {
            // Insert ke database
            $query = "INSERT INTO t_log (tgl_complain, target_selesai, uraian, user_id, tgl_input, status) 
                      VALUES ('$tgl_complain', '$target_selesai', '$uraian', '$user_id', '$tgl_input', '$status')";

            if ($conn->query($query) === TRUE) {
                $importedRows++; // Hitung jumlah baris yang berhasil diimport
            } else {
                // Handle jika terjadi kesalahan saat query ke database
                error_log("Error importing row: " . $conn->error);
            }
        }
    }

    return $importedRows;
}

// Jika file diunggah
$importedRows = 0;
$message = '';
if (isset($_FILES['excelFile'])) {
    $file = $_FILES['excelFile']['tmp_name'];

    // Import data dari file Excel
    $totalImported = importExcel($file, $conn);
    $message = "<div class='alert alert-success mt-4'>Data berhasil diimport: $totalImported baris.</div>";
}

// Menutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel - Laporan Monitoring Pekerjaan</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-weight: bold;
            color: #007bff;
        }
        .custom-file-label {
            cursor: pointer;
        }
        button {
            background-color: #007bff;
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        footer {
            margin-top: 50px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4"><i class="fas fa-file-import"></i> Import Data Excel Rekap Pekerjaan Karyawan</h2>
        
        <!-- Display success message after import -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Form upload file Excel -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="excelFile">Pilih file Excel:</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="excelFile" id="excelFile" accept=".xlsx, .xls" required>
                    <label class="custom-file-label" for="excelFile">Pilih file...</label>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload dan Import</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap and jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mengganti label file saat file di-upload
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        });
    </script>
    <!-- Tombol kembali -->
    <div class="text-center">
            <a href="dashboard.php" class="btn btn-primary mt-3">Home</a>
        </div>
    </div>

</body>
</html>
