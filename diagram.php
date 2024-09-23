<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoring_pekerjaan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data for Pie Chart
$query = "SELECT status, COUNT(*) AS count FROM t_log GROUP BY status";
$result = $conn->query($query);

$pieData = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pieData[] = $row;
    }
}

// Fetch data for tables
$queryUsers = "SELECT * FROM r_user";
$queryCustomers = "SELECT * FROM r_customer";
$queryLogs = "SELECT * FROM t_log";

$users = $conn->query($queryUsers);
$customers = $conn->query($queryCustomers);
$logs = $conn->query($queryLogs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Monitoring</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa; /* Warna latar belakang yang terang dan profesional */
        }

        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow untuk efek card */
        }

        #pieChart {
            max-width: 300px;
            max-height: 300px;
            margin: 0 auto;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 1rem;
            margin-top: 3rem;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Main Content Container -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Monitoring Pekerjaan</h1>

        <!-- Pie Chart -->
        <div class="text-center mb-4">
            <canvas id="pieChart"></canvas>
        </div>

        <!-- User Table -->
        <h2 class="mb-3">Daftar Programmer</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Programmer</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users && $users->num_rows > 0): ?>
                    <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['programmer'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="text-center">No data found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Customer Table -->
        <h2 class="mb-3">Daftar Customer</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($customers && $customers->num_rows > 0): ?>
                    <?php while($row = $customers->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['customer'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="text-center">No data found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Log Table -->
        <h2 class="mb-3">Log Pekerjaan</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Tanggal Complain</th>
                    <th>Target Selesai</th>
                    <th>Uraian</th>
                    <th>ID Programer</th>
                    <th>Tanggal Input</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs && $logs->num_rows > 0): ?>
                    <?php while($row = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['tgl_complain'] ?></td>
                            <td><?= $row['target_selesai'] ?></td>
                            <td><?= $row['uraian'] ?></td>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= $row['tgl_input'] ?></td>
                            <td><?= $row['status'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No data found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="text-center">
    <a href="export_spreadsheet.php" class="btn btn-success mt-3">Cetak ke Spreadsheet</a>
    <a href="export_pdf.php" class="btn btn-danger mt-3">Cetak ke PDF</a>
</div>

        <!-- Tombol kembali -->
        <div class="text-center">
            <a href="monitoring.php" class="btn btn-primary mt-3">Kembali ke Menu Monitoring</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center">
        &copy; 2024 Monitoring DSI
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Setup Chart.js with PHP data
        var ctx = document.getElementById('pieChart').getContext('2d');
        var pieData = <?= json_encode($pieData) ?>;

        // Parse labels and data
        var labels = pieData.map(data => data.status);
        var counts = pieData.map(data => data.count);

        // Check if data exists
        if (labels.length > 0 && counts.length > 0) {
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: ['#36a2eb', '#ff6384', '#ffcd56', '#4bc0c0'],
                    }]
                },
                options: {
                    responsive: true
                }
            });
        } else {
            // Fallback if no data
            document.getElementById('pieChart').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>