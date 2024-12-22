<?php
// Memulai session
session_start();

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

// Variabel untuk status login
$status = "";

// Menangani input data login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input tidak kosong
    if (empty($username) || empty($password)) {
        $status = "empty_fields";
    } else {
        // Query untuk memeriksa username
        $stmt = $conn->prepare("SELECT email, password FROM data_member WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            // Jika username ditemukan
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($email, $hashedPassword);
                $stmt->fetch();

                // Verifikasi password
                if (password_verify($password, $hashedPassword)) {
                    // Simpan informasi ke session
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;

                    // Simpan informasi ke cookie
                    setcookie('username', $username, time() + (86400 * 7), "/");
                    setcookie('email', $email, time() + (86400 * 7), "/");

                    $status = "success";
                } else {
                    $status = "invalid_password";
                }
            } else {
                $status = "user_not_found";
            }

            $stmt->close();
        } else {
            $status = "error_preparing_statement";
        }
    }
}

// Menutup koneksi database
$conn->close();
?>


<!-- Tampilan HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <link rel="stylesheet" href="Login.css">
    <script>
        // Menampilkan pesan status
        function showAlert(status) {
            if (status === "success") {
                alert("Login berhasil! Selamat datang.");
                window.location.href = "Display.php"; // Ganti dengan halaman setelah login
            } else if (status === "empty_fields") {
                alert("Semua field harus diisi!");
            } else if (status === "invalid_password") {
                alert("Password salah. Silakan coba lagi!");
            } else if (status === "user_not_found") {
                alert("Username tidak ditemukan. Silakan daftar terlebih dahulu.");
            } else if (status === "error_preparing_statement") {
                alert("Kesalahan dalam menyiapkan query SQL.");
            }
        }
    </script>
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
<body onload="showAlert('<?php echo $status; ?>')">
    <div class="background">
        <div class="container">
            <h2>Login Member</h2>
            <form method="POST" action="Login.php">
                <div class="form-group">
                    <!-- Input Username -->
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                    <!-- Input Password -->
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit">Login</button>
            </form>
            <p>Belum memiliki akun?</p>
            <a href="Sign.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
