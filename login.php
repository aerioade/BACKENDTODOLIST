<?php
require 'db.php';
session_start();

// Proses login jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Query untuk mencari user berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$inputUsername]);
    $user = $stmt->fetch();

    // Cek apakah password yang dimasukkan sesuai
    if ($user && password_verify($inputPassword, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php"); // Redirect ke halaman index setelah login sukses
        exit;
    } else {
        $loginError = "Login failed! Invalid username or password."; // Menampilkan error jika login gagal
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - To-Do List</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
    <h2>Login to Your Account</h2>

    <!-- Form login -->
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>

        <?php if (isset($loginError)): ?>
            <p class="error-message"><?php echo $loginError; ?></p>
        <?php endif; ?>
    </form>

    <!-- Tombol menuju halaman Register -->
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
