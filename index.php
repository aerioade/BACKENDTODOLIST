<?php
require 'db.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];
$message = '';

// Tambah task baru jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_title'])) {
    $taskTitle = $_POST['task_title'];
    $taskDueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null; // Set due_date jika ada, null jika kosong

    // Update query untuk menyimpan due_date
    $addTaskQuery = $pdo->prepare("INSERT INTO tasks (user_id, title, due_date) VALUES (?, ?, ?)");
    $addTaskQuery->execute([$currentUserId, $taskTitle, $taskDueDate]);
    $message = 'Task added successfully!'; // Set success message
    header("Location: index.php?message=" . urlencode($message)); // Redirect dengan pesan
    exit;
}

// Hapus task jika ada ID task yang diterima melalui GET
if (isset($_GET['delete_task_id'])) {
    $deleteTaskId = $_GET['delete_task_id'];
    $deleteTaskQuery = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $deleteTaskQuery->execute([$deleteTaskId, $currentUserId]);
    $message = 'Task deleted successfully!'; // Set success message
    header("Location: index.php?message=" . urlencode($message)); // Redirect dengan pesan
    exit;
}

// Ambil semua task untuk user yang sedang login
$getTasksQuery = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
$getTasksQuery->execute([$currentUserId]);
$userTasks = $getTasksQuery->fetchAll();

// Display success message if exists
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <h2>Your To-Do List</h2>
    
    <?php if ($message): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '<?php echo $message; ?>',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    <?php endif; ?>

    <ul>
        <?php foreach ($userTasks as $task): ?>
            <li>
                <span class="task-title"><?php echo htmlspecialchars($task['title']); ?></span>
                <span class="task-due-date" data-due-date="<?php echo $task['due_date']; ?>">
                    <?php echo $task['due_date'] ? htmlspecialchars($task['due_date']) : 'No due date'; ?>
                </span>
                <div class="task-actions">
                    <a href="edit.php?task_id=<?php echo $task['id']; ?>" class="edit-btn">Edit</a> 
                    <a href="?delete_task_id=<?php echo $task['id']; ?>" class="delete-btn">Delete</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="POST" action="">
        <input type="text" name="task_title" placeholder="Add new task" required>
        <input type="date" name="due_date" placeholder="Due Date">
        <button type="submit">Add</button>
    </form>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date();
        document.querySelectorAll('.task-due-date').forEach(dueDateElement => {
            const dueDate = new Date(dueDateElement.getAttribute('data-due-date'));
            if (dueDate < today) {
                dueDateElement.classList.add('overdue');
            }
        });

        // Handle task deletion confirmation using SweetAlert
        const deleteLinks = document.querySelectorAll('.delete-btn');
        deleteLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent the link from being followed

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = link.href; // Proceed with the deletion
                    }
                });
            });
        });
    });
</script>

</body>
</html>
