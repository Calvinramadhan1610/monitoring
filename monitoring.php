<?php
// Aktifkan pelaporan error untuk debugging (nonaktifkan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring_pekerjaan";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("<div style='color: red; text-align: center;'>Connection failed: " . $conn->connect_error . "</div>");
}

// Ambil data customer dari tabel r_customer
$sql_customers = "SELECT id, customer FROM r_customer";
$result_customers = $conn->query($sql_customers);

// Inisialisasi variabel pesan
$message = "";

// Proses hapus data
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM t_log WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $message = "<div class='success'>Data berhasil dihapus!</div>";
        } else {
            $message = "<div class='error'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer = intval($_POST['customer']);
    $uraian = $conn->real_escape_string($_POST['uraian']);
    $user_id = intval($_POST['user_id']);
    $tgl_input = date('Y-m-d'); // Tanggal input hari ini

    // Menangani tanggal complain
    $tgl_complain = null;
    if ($_POST['tgl_complain_format'] == 'custom') {
        $tgl_complain_start = $conn->real_escape_string($_POST['tgl_complain_start']);
        $tgl_complain_end = $conn->real_escape_string($_POST['tgl_complain_end']);
        // Custom logic to handle range can be added here if needed
    } else {
        $tgl_complain = $conn->real_escape_string($_POST['tgl_complain']);
    }

    // Menangani target selesai
    $target_selesai = null;
    if ($_POST['target_selesai_format'] == 'custom') {
        $target_selesai_start = $conn->real_escape_string($_POST['target_selesai_start']);
        $target_selesai_end = $conn->real_escape_string($_POST['target_selesai_end']);
        // Custom logic to handle range can be added here if needed
    } else {
        $target_selesai = $conn->real_escape_string($_POST['target_selesai']);
    }

    // Menangani tanggal selesai
    $tgl_selesai = null;
    if ($_POST['tgl_selesai'] == 'custom') {
        $tgl_selesai_start = $conn->real_escape_string($_POST['tgl_selesai_start']);
        $tgl_selesai_end = $conn->real_escape_string($_POST['tgl_selesai_end']);
        // Custom logic to handle range can be added here if needed
    } else {
        $tgl_selesai = !empty($_POST['tgl_selesai']) ? $conn->real_escape_string($_POST['tgl_selesai']) : null;
    }

    // Pengecekan status otomatis berdasarkan tgl_selesai dan target_selesai
    if ($tgl_selesai) {
        $status = (strtotime($tgl_selesai) <= strtotime($target_selesai)) ? 'Tepat Waktu' : 'Terlambat';
    } else {
        $status = 'Dalam Proses';
    }

    // Proses update data jika ada ID
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE t_log SET customer=?, tgl_complain=?, tgl_selesai=?, target_selesai=?, uraian=?, user_id=?, status=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("isssissi", $customer, $tgl_complain, $tgl_selesai, $target_selesai, $uraian, $user_id, $status, $id);
            if ($stmt->execute()) {
                $message = "<div class='success'>Data berhasil diubah!</div>";
            } else {
                $message = "<div class='error'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    } else {
        // Proses insert data baru
        $stmt = $conn->prepare("INSERT INTO t_log (customer, tgl_complain, tgl_selesai, target_selesai, uraian, user_id, tgl_input, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isssisss", $customer, $tgl_complain, $tgl_selesai, $target_selesai, $uraian, $user_id, $tgl_input, $status);
            if ($stmt->execute()) {
                $message = "<div class='success'>Data berhasil disimpan!</div>";
            } else {
                $message = "<div class='error'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='error'>Error: " . $conn->error . "</div>";
        }
    }
}

// Ambil data log dari database
$sql_logs = "SELECT t_log.*, r_customer.customer, r_user.programmer 
    FROM t_log 
    JOIN r_customer ON t_log.customer = r_customer.id 
    JOIN r_user ON t_log.user_id = r_user.id 
    ORDER BY t_log.id DESC";
$result_logs = $conn->query($sql_logs);

$sql_users = "SELECT id, programmer FROM r_user";
$result_users = $conn->query($sql_users);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Pekerjaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-heading {
            margin-top: 20px;
            color: #007bff;
        }
        .form-group label {
            font-weight: bold;
        }
        .data-table th, .data-table td {
            text-align: center;
        }
        .btn-primary, .btn-secondary {
            width: 100%;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .container {
            margin-top: 20px;
        }
    </style>
    <script>
    function changeDateInput(fieldId, format) {
        let inputField = document.getElementById(fieldId);
        let customField = document.getElementById('custom_' + fieldId);

        if (format === 'daily') {
            inputField.type = 'date';
            inputField.classList.remove('d-none');
            customField.classList.add('d-none');
        } else if (format === 'monthly') {
            inputField.type = 'month';
            inputField.classList.remove('d-none');
            customField.classList.add('d-none');
        } else if (format === 'custom') {
            inputField.classList.add('d-none');
            customField.classList.remove('d-none');
        } else {
            inputField.type = 'date';
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <header>
            <h1 class="main-heading text-center">Form Pengisian Laporan</h1>
        </header>
        
        <!-- Tampilkan pesan -->
        <?php if ($message): ?>
            <div class="alert <?php echo (strpos($message, 'berhasil') !== false) ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <?= $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Form Input Data Laporan -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="customer" class="form-label">Customer:</label>
                        <select class="form-select" id="customer" name="customer" required>
                            <option value="">Pilih Customer</option>
                            <?php
                            if ($result_customers->num_rows > 0) {
                                while ($customer = $result_customers->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($customer['id']) . "'>" . htmlspecialchars($customer['customer']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>Tidak ada customer tersedia</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="uraian" class="form-label">Uraian Pekerjaan:</label>
                        <textarea class="form-control" id="uraian" name="uraian" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="user_id" class="form-label">Programmer:</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Pilih Programmer</option>
                            <?php
                            if ($result_users->num_rows > 0) {
                                while ($user = $result_users->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($user['id']) . "'>" . htmlspecialchars($user['programmer']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>Tidak ada programmer tersedia</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Tanggal Complain -->
                    <div class="mb-3">
                        <label for="tgl_complain_format" class="form-label">Tanggal Complain:</label>
                        <select class="form-select" id="tgl_complain_format" name="tgl_complain_format" onchange="changeDateInput('tgl_complain', this.value)" required>
                            <option value="daily">Harian</option>
                            <option value="monthly">Bulanan</option>
                            <option value="custom">Custom</option>
                        </select>

                        <input type="date" class="form-control mt-2" id="tgl_complain" name="tgl_complain" required>
                        <div id="custom_tgl_complain" class="d-none mt-2">
                            <label for="tgl_complain_start" class="form-label">Dari:</label>
                            <input type="date" class="form-control" id="tgl_complain_start" name="tgl_complain_start">
                            <label for="tgl_complain_end" class="form-label">Sampai:</label>
                            <input type="date" class="form-control" id="tgl_complain_end" name="tgl_complain_end">
                        </div>
                    </div>

                    <!-- Target Selesai -->
                    <div class="mb-3">
                        <label for="target_selesai_format" class="form-label">Target Selesai:</label>
                        <select class="form-select" id="target_selesai_format" name="target_selesai_format" onchange="changeDateInput('target_selesai', this.value)" required>
                            <option value="daily">Harian</option>
                            <option value="monthly">Bulanan</option>
                            <option value="custom">Custom</option>
                        </select>

                        <input type="date" class="form-control mt-2" id="target_selesai" name="target_selesai" required>
                        <div id="custom_target_selesai" class="d-none mt-2">
                            <label for="target_selesai_start" class="form-label">Dari:</label>
                            <input type="date" class="form-control" id="target_selesai_start" name="target_selesai_start">
                            <label for="target_selesai_end" class="form-label">Sampai:</label>
                            <input type="date" class="form-control" id="target_selesai_end" name="target_selesai_end">
                        </div>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="mb-3">
                        <label for="tgl_selesai" class="form-label">Tanggal Selesai:</label>
                        <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai">
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>

        <!-- Tabel Data Laporan -->
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title">Data Laporan Pekerjaan</h3>
                <table class="table table-striped table-hover data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Programmer</th>
                            <th>Uraian</th>
                            <th>Tgl Complain</th>
                            <th>Target Selesai</th>
                            <th>Tgl Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    if ($result_logs->num_rows > 0) {
        $i = 1;
        while ($log = $result_logs->fetch_assoc()) {
            // Tampilkan 'Belum Selesai' jika tgl_selesai null atau kosong
            $tgl_selesai = !empty($log['tgl_selesai']) ? htmlspecialchars($log['tgl_selesai']) : 'Belum Selesai';
            
            echo "<tr>
                <td>{$i}</td>
                <td>" . htmlspecialchars($log['customer']) . "</td>
                <td>" . htmlspecialchars($log['programmer']) . "</td>
                <td>" . htmlspecialchars($log['uraian']) . "</td>
                <td>" . htmlspecialchars($log['tgl_complain']) . "</td>
                <td>" . htmlspecialchars($log['target_selesai']) . "</td>
                <td>" . $tgl_selesai . "</td>
                <td>" . htmlspecialchars($log['status']) . "</td>
                <td>
                    <a href='?delete_id=" . intval($log['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>
                    <a href='edit.php?id=" . intval($log['id']) . "' class='btn btn-secondary btn-sm'>Edit</a>
                </td>
            </tr>";
            $i++;
        }
    } else {
        echo "<tr><td colspan='9' class='text-center'>Tidak ada data yang tersedia</td></tr>";
    }
    ?>
</tbody>

                </table>
                <section class="link-section text-center mt-4 d-flex justify-content-center gap-3">
                    <a href="diagram.php" class="btn btn-secondary">Lihat Diagram Pie</a>
                    <a href="dashboard.php" class="btn btn-home btn-secondary">Home</a>
                </section>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>