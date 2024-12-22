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

// Variabel untuk status penyimpanan
$status = "";

// Menangani input data 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = ($_POST['username']);
    $email = ($_POST['email']);
    $password = ($_POST['password']);
    $confirmPassword = ($_POST['confirmPassword']);

    // Validasi password
    if ($password !== $confirmPassword) {
        $status = "password_mismatch";
    } elseif (empty($username) || empty($email) || empty($password)) {
        $status = "empty_fields";
    } else {
        // Hashing password untuk keamanan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Query menggunakan prepared statement
        $stmt = $conn->prepare("INSERT INTO data_member (username, email, password) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Simpan informasi pengguna ke session
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                // Simpan informasi ke cookie
                setcookie('username', $username, time() + (86400 * 7), "/"); // Berlaku 7 hari
                setcookie('email', $email, time() + (86400 * 7), "/"); // Berlaku 7 hari

                $status = "success";
            } else {
                $status = "error";
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
    <title>Halaman Sign Up Member</title>
    <link rel="stylesheet" href="Sign.css">
    <script>
        // Menampilkan pesan status
        function showAlert(status) {
            if (status === "success") {
                alert("Pendaftaran berhasil!");
            } else if (status === "password_mismatch") {
                alert("Password dan Konfirmasi Password tidak sesuai!");
            } else if (status === "empty_fields") {
                alert("Semua field harus diisi!");
            } else if (status === "error") {
                alert("Terjadi kesalahan saat menyimpan data.");
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
            <h2>Sign Up For New Member</h2>
            <form method="POST" action="Sign.php">
                <div class="form-group">
                    <!-- Input Username -->
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                    <!-- Input Email -->
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <!-- Input Password -->
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <!-- Input Confirm Password -->
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                </div>

                <!-- Checkbox  -->
                <div class="form-checkbox">
                    <input type="checkbox" id="agree" name="agree" required>
                    <label for="agree" class="terms">I agree with the <a class="rule" href="#">terms of service</a></label>
                </div>

                <!-- Submit Button -->
                <button type="submit">Register</button>
            </form>
            <p>Already have an account?</p>
            <a href="Login.php">Login Here</a>
        </div>
    </div>
</body>
</html>
