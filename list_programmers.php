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

// Query untuk mengambil daftar programmer
$sql = "SELECT * FROM r_user";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Programmer</title>
</head>
<body>
    <h2>Daftar Programmer</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Programmer</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td>" . htmlspecialchars($row['programmer_name']) . "</td>
                            <td>" . htmlspecialchars($row['programmer_email']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Tidak ada data programmer.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
