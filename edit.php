<?php
require 'db.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];

// Cek apakah ada task_id di URL dan ambil data task untuk diedit
if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];
    
    // Ambil task berdasarkan ID dan pastikan task milik user yang sedang login
    $getTaskQuery = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $getTaskQuery->execute([$taskId, $currentUserId]);
    $task = $getTaskQuery->fetch();

    // Jika task tidak ditemukan atau bukan milik user, redirect ke halaman utama
    if (!$task) {
        header("Location: index.php");
        exit;
    }
} else {
    // Redirect jika task_id tidak ada di URL
    header("Location: index.php");
    exit;
}

// Update task jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_title'], $_POST['due_date'])) {
    $updatedTaskTitle = $_POST['task_title'];
    $updatedDueDate = $_POST['due_date'];
    
    // Query untuk mengupdate task
    $updateTaskQuery = $pdo->prepare("UPDATE tasks SET title = ?, due_date = ? WHERE id = ? AND user_id = ?");
    $updateTaskQuery->execute([$updatedTaskTitle, $updatedDueDate, $taskId, $currentUserId]);
    
    // Redirect setelah berhasil update
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Edit Task</h2>
    
    <form method="POST" action="">
        <input type="text" name="task_title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
        <input type="date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>">
        <button type="submit">Update</button>
    </form>
    
    <a href="index.php">Back to To-Do List</a>
</div>
</body>
</html>
