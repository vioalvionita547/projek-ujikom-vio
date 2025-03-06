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

// Jika form ditambahkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $subtasks = isset($_POST['subtasks']) ? $_POST['subtasks'] : []; // Cek apakah subtasks ada

    // Masukkan ke tabel task
    $query = "INSERT INTO task (title, deadline, priority, created_at, status, user_id) 
              VALUES ('$title', '$deadline', '$priority', NOW(), 'pending', '$user_id')";
    
    if (mysqli_query($conn, $query)) {
        $task_id = mysqli_insert_id($conn);
        
        // Masukkan subtask ke dalam tabel, dengan menambahkan user_id
        foreach ($subtasks as $subtask) {
            $subtask = mysqli_real_escape_string($conn, $subtask);
            $query_subtask = "INSERT INTO subtask (task_id, description, user_id) VALUES ('$task_id', '$subtask', '$user_id')";
            mysqli_query($conn, $query_subtask);
        }
    }
}

// Update status task yang deadline-nya sudah lewat menjadi 'completed'
$update_query = "UPDATE task SET status = 'completed' 
                 WHERE deadline < CURDATE() AND status != 'completed' AND user_id = '$user_id'";
mysqli_query($conn, $update_query);

// Pencarian task
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query utama untuk mengambil data task dan subtask
$query = "SELECT task.id, task.title, task.deadline, task.priority, task.status, task.created_at, 
          GROUP_CONCAT(subtask.description SEPARATOR ', ') AS subtasks
          FROM task 
          LEFT JOIN subtask ON task.id = subtask.task_id
          WHERE task.user_id = '$user_id'"; 

if (!empty($search)) {
    $query .= " AND (task.title LIKE '%$search%' 
                OR task.id IN (SELECT task_id FROM subtask WHERE description LIKE '%$search%'))";
}

$query .= " GROUP BY task.id ORDER BY task.id DESC";

$result = mysqli_query($conn, $query) or die("Query Error: " . mysqli_error($conn));

$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="todo-container">
        <header>
            <h1><i class="fa-solid fa-list"></i> To Do List</h1>
            <a href="logout.php" class="logout-button">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </header>

        <!-- Form Tambah Task -->
        <form id="todo-form" method="POST">
            <p>Judul</p>
            <div class="form-control">
                <input type="text" name="title" placeholder="Tambahkan judul" required>
            </div>
            <p>Priority</p>
            <div class="form-control">
                <select name="priority" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <p>Deadline</p>
            <div class="form-control">
                <input type="datetime-local" name="deadline" required>
            </div>
            <p>Subtask</p>
            <div class="form-control">
                <input type="text" name="subtasks[]" placeholder="Tambahkan subtask">
                <button type="button" onclick="tambahSubtask()">Tambah Subtask</button>
            </div>
            <div id="subtask-container"></div> <!-- Tempat input subtask ditambahkan -->
            <button type="submit"><i class="fa-solid fa-plus"></i> Tambah Task</button>
        </form>

        <!-- Form Pencarian Task -->
        <form method="GET">
            <input type="text" name="search" placeholder="Cari task..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fa-solid fa-search"></i> Cari</button>
        </form>

        <!-- List Task -->
        <div class="todo-list">
            <?php if (!empty($tasks)) {
                foreach ($tasks as $row) : 
                    $is_completed = (strtotime($row['deadline']) < time()) || ($row['status'] == 'completed');
            ?>
                <div class="todo <?= $is_completed ? 'completed' : '' ?>">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><strong>Status:</strong> <?= ucfirst($row['status']) ?></p>
                    <p><strong>Priority:</strong> <?= ucfirst($row['priority']) ?></p>
                    <p><strong>Deadline:</strong> <?= $row['deadline'] ?></p>
                    <p><strong>Created At:</strong> <?= $row['created_at'] ?></p>
                    <p><strong>Subtasks:</strong> <?= htmlspecialchars($row['subtasks']) ?></p>

                    <?php if ($row['status'] == 'pending') : ?>
                        <button class="complete-todo" onclick="selesaikanTask(<?= $row['id'] ?>)">
                            <i class="fa-solid fa-check"></i> Selesaikan
                        </button>
                    <?php endif; ?>

                    <button class="edit-todo" onclick="window.location.href='edit_task.php?id=<?= $row['id'] ?>'">
                        <i class="fa-solid fa-pen"></i> Edit
                    </button>
                    <button class="remove-todo" onclick="hapusTask(<?= $row['id'] ?>)">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            <?php endforeach; 
            } else { ?>
                <p>Tidak ada task ditemukan.</p>
            <?php } ?>
        </div>
    </div>

    <script>
    function tambahSubtask() {
        const subtaskContainer = document.getElementById('subtask-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'subtasks[]';
        input.placeholder = 'Tambahkan subtask';
        subtaskContainer.appendChild(input);  // Tambahkan input baru ke dalam container
    }

    function selesaikanTask(id) {
        if (confirm("Yakin ingin menyelesaikan task ini?")) {
            window.location.href = "selesaikan_task.php?id=" + id;
        }
    }

    function hapusTask(id) {
        if (confirm("Yakin ingin menghapus task ini?")) {
            window.location.href = "hapus_task.php?id=" + id;
        }
    }
    </script>
</body>
</html>
