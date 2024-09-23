<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring_pekerjaan";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data berdasarkan ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM t_log WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "Data tidak ditemukan!";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// Ambil nilai ENUM dari kolom status
$sql_enum = "SHOW COLUMNS FROM t_log LIKE 'status'";
$result_enum = $conn->query($sql_enum);
$row_enum = $result_enum->fetch_assoc();

// Ekstrak nilai ENUM
$enum_values = str_replace("'", "", substr($row_enum['Type'], 5, (strlen($row_enum['Type']) - 6)));
$enum_values_array = explode(',', $enum_values);

// Ambil data customer dari tabel r_customer
$sql_customers = "SELECT id, customer FROM r_customer";
$result_customers = $conn->query($sql_customers);

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = intval($_POST['customer_id']); // Customer ID ditambahkan
    $tgl_complain = $conn->real_escape_string($_POST['tgl_complain']);
    $target_selesai = $conn->real_escape_string($_POST['target_selesai']);
    $uraian = $conn->real_escape_string($_POST['uraian']);
    $user_id = intval($_POST['user_id']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql_update = "UPDATE t_log SET customer = ?, tgl_complain = ?, target_selesai = ?, uraian = ?, user_id = ?, status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("isssisi", $customer_id, $tgl_complain, $target_selesai, $uraian, $user_id, $status, $id);

    if ($stmt_update->execute()) {
        header("Location: monitoring.php?message=Data berhasil diubah");
        exit();
    } else {
        echo "Error: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Laporan Pekerjaan</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="customer_id" class="form-label">Customer:</label>
                <select id="customer_id" name="customer_id" class="form-select" required>
                    <option value="">Pilih Customer</option>
                    <?php
                    if ($result_customers->num_rows > 0) {
                        while ($customer = $result_customers->fetch_assoc()) {
                            $selected = ($customer['id'] == $row['customer']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($customer['id']) . "' $selected>" . htmlspecialchars($customer['customer']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada customer tersedia</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="tgl_complain" class="form-label">Tanggal Complain:</label>
                <input type="date" id="tgl_complain" name="tgl_complain" class="form-control" value="<?php echo htmlspecialchars($row['tgl_complain']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="target_selesai" class="form-label">Target Selesai:</label>
                <input type="date" id="target_selesai" name="target_selesai" class="form-control" value="<?php echo htmlspecialchars($row['target_selesai']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="uraian" class="form-label">Uraian Pekerjaan:</label>
                <textarea id="uraian" name="uraian" class="form-control" rows="4" required><?php echo htmlspecialchars($row['uraian']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">Programmer ID:</label>
                <input type="number" id="user_id" name="user_id" class="form-control" value="<?php echo htmlspecialchars($row['user_id']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select id="status" name="status" class="form-select" required>
                    <?php
                    foreach ($enum_values_array as $value) {
                        $selected = ($value == $row['status']) ? 'selected' : '';
                        echo "<option value='$value' $selected>$value</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>