<?php
require 'koneksi.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil user_id dari sesi
$user_id = $_SESSION['user_id'];

// Ambil ID task dari parameter URL
$task_id = $_GET['id'];

// Cek apakah task ini milik pengguna yang sedang login
$query = "SELECT * FROM task WHERE id = '$task_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $query);

// Jika task tidak ditemukan atau bukan milik pengguna yang sedang login
if (mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit();
}

// Ambil data task untuk ditampilkan di form edit
$task = mysqli_fetch_assoc($result);

// Cek apakah form sudah disubmit untuk update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    // Validasi tanggal deadline
    $deadline = date('Y-m-d H:i:s', strtotime($deadline));  // pastikan format tanggal benar

    // Update task di database
    $query_update = "UPDATE task SET title = '$title', deadline = '$deadline', 
                     priority = '$priority', status = '$status' 
                     WHERE id = '$task_id' AND user_id = '$user_id'";

    if (mysqli_query($conn, $query_update)) {
        // Menghapus subtasks yang lama
        $delete_subtasks_query = "DELETE FROM subtask WHERE task_id = '$task_id' AND user_id = '$user_id'";
        if (!mysqli_query($conn, $delete_subtasks_query)) {
            echo "Error deleting subtasks: " . mysqli_error($conn);
            exit();
        }

        // Menambahkan atau memperbarui subtasks
        if (isset($_POST['subtasks'])) {
            foreach ($_POST['subtasks'] as $subtask_description) {
                $subtask_description = mysqli_real_escape_string($conn, $subtask_description);
                if (!empty($subtask_description)) {
                    $query_subtask = "INSERT INTO subtask (task_id, description, user_id) 
                                      VALUES ('$task_id', '$subtask_description', '$user_id')";
                    if (!mysqli_query($conn, $query_subtask)) {
                        echo "Error inserting subtask: " . mysqli_error($conn);
                        exit();
                    }
                }
            }
        }

        // Redirect ke halaman utama setelah sukses update
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Gagal memperbarui task. Coba lagi. Error: " . mysqli_error($conn);
    }
}

// Ambil subtask terkait task ini untuk ditampilkan di form
$query_subtasks = "SELECT * FROM subtask WHERE task_id = '$task_id' AND user_id = '$user_id'";
$result_subtasks = mysqli_query($conn, $query_subtasks);
$subtasks = mysqli_fetch_all($result_subtasks, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="todo-container">
        <header>
            <h1><i class="fa-solid fa-pen"></i> Edit Task</h1>
            <a href="index.php" class="back-button">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </header>

        <?php if (isset($error_message)) { ?>
            <div class="error-message">
                <?= $error_message; ?>
            </div>
        <?php } ?>

        <!-- Form Edit Task -->
        <form id="todo-form" method="POST">
            <p>Judul</p>
            <div class="form-control">
                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
            </div>

            <p>Priority</p>
            <div class="form-control">
                <select name="priority" required>
                    <option value="low" <?= $task['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= $task['priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= $task['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                </select>
            </div>

            <p>Deadline</p>
            <div class="form-control">
                <input type="datetime-local" name="deadline" value="<?= $task['deadline'] ?>" required>
            </div>

            <p>Status</p>
            <div class="form-control">
                <select name="status" required>
                    <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>

            <p>Subtasks</p>
            <div class="form-control">
                <div id="subtask-container">
                    <?php foreach ($subtasks as $subtask) { ?>
                        <input type="text" name="subtasks[]" value="<?= htmlspecialchars($subtask['description']) ?>" placeholder="Subtask">
                    <?php } ?>
                </div>
                <button type="button" onclick="tambahSubtask()">Tambah Subtask</button>
            </div>

            <button type="submit"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>

    <script>
        function tambahSubtask() {
            const subtaskContainer = document.getElementById('subtask-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'subtasks[]';
            input.placeholder = 'Tambahkan subtask';
            subtaskContainer.appendChild(input);
        }
    </script>
</body>
</html>
