<?php
require 'koneksi.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = $_GET['id'];


$query_delete_subtask = "DELETE FROM subtask WHERE task_id = ?";
$stmt = $conn->prepare($query_delete_subtask);
$stmt->bind_param("i", $task_id);
$stmt->execute();


$query_delete_task = "DELETE FROM task WHERE id = ?";
$stmt = $conn->prepare($query_delete_task);
$stmt->bind_param("i", $task_id);
$stmt->execute();


header("Location: dashboard.php");
exit();
?>