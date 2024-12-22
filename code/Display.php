<?php
// Memulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "member";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mendapatkan data dari tabel
$sql = "SELECT username, email FROM data_member";
$result = $conn->query($sql);

// Mendapatkan informasi browser dan IP pengguna
$browser = $_SERVER['HTTP_USER_AGENT'];
$ip_address = $_SERVER['REMOTE_ADDR'];

// Menutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Database Member</title>
    <link rel="stylesheet" href="Display.css">
    <script>
        // Simpan informasi ke browser storage
        localStorage.setItem('username', '<?php echo $_SESSION['username'] ?? ""; ?>');
        localStorage.setItem('email', '<?php echo $_SESSION['email'] ?? ""; ?>');

        // Mendapatkan informasi dari browser storage
        console.log('Username dari localStorage:', localStorage.getItem('username'));
        console.log('Email dari localStorage:', localStorage.getItem('email'));

        // Hapus informasi dari browser storage
        function clearStorage() {
            localStorage.removeItem('username');
            localStorage.removeItem('email');
        }
    </script>
</head>
<body>
    <div class="background">
        <div class="container">
        <h2>Database Member</h2>
            <!-- Tabel untuk menampilkan data username dan email -->
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">Tidak ada data tersedia</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Informasi browser dan IP pengguna -->
            <div class="info">
                <p><strong>Browser:</strong> <?php echo htmlspecialchars($browser); ?></p>
                <p><strong>IP Address:</strong> <?php echo htmlspecialchars($ip_address); ?></p>
            </div>

            <!-- Tombol untuk kembali ke halaman login.php -->
            <div id="error-message" class="error">
                <a href="Login.php">
                    <button type="button">Leave</button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
