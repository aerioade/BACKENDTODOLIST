<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = password_hash($_POST['password'], PASSWORD_BCRYPT); // Password hash untuk keamanan

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$inputUsername, $inputPassword]);

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - To-Do List</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
    <h2>Create a New Account</h2>

    <!-- Form registrasi -->
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <!-- Tombol menuju halaman Login -->
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
