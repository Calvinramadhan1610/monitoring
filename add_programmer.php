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

// Inisialisasi variabel pesan
$message = "";

// Proses insert data programmer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $programmer_name = $conn->real_escape_string($_POST['programmer_name']);
    $programmer_email = $conn->real_escape_string($_POST['programmer_email']);

    // Insert data ke tabel r_user
    $stmt = $conn->prepare("INSERT INTO r_user (programmer_name, programmer_email) VALUES (?, ?)");
    $stmt->bind_param("ss", $programmer_name, $programmer_email);

    if ($stmt->execute()) {
        $message = "<div class='success'>Programmer berhasil ditambahkan!</div>";
    } else {
        $message = "<div class='error'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Programmer</title>
</head>
<body>
    <h2>Tambah Programmer Baru</h2>

    <!-- Tampilkan pesan -->
    <?php echo $message; ?>

    <!-- Form Input Programmer Baru -->
    <form action="" method="POST">
        <label for="programmer_name">Nama Programmer:</label>
        <input type="text" id="programmer_name" name="programmer_name" required><br>

        <label for="programmer_email">Email Programmer:</label>
        <input type="email" id="programmer_email" name="programmer_email" required><br>

        <button type="submit">Tambah Programmer</button>
    </form>
</body>
</html>
