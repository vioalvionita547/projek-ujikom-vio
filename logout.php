<?php
// Mulai sesi untuk menghapus data pengguna
session_start();

// Hapus data pengguna dari sesi
session_unset();
session_destroy();

// Arahkan kembali ke halaman login
header('Location: login.php');
exit;
?>
