<?php
require 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Ambil data user dari database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Cek password dengan password_verify
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styless.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Gaya untuk input dan ikon */
        .password-container {
            position: relative;
            margin-bottom: 10px;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px; /* Memberikan ruang untuk ikon */
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login">
            <form id="loginForm" method="POST">
                <h1>Login</h1>
                <hr>
                
                <?php if (!empty($error)) { ?>
                    <p style="color: red;"><?= $error; ?></p>
                <?php } ?>

                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required>
                
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                    <!-- Icon toggle untuk password -->
                    <i class="fa-solid fa-eye toggle-password" id="eyeIcon"></i>
                </div>
                
                <button type="submit">Login</button>
                <p>
                    <a href="#">Lupa Password?</a>
                </p>
                <p>Belum punya akun? <a href="register.php">Register</a></p>
            </form>
        </div>
        <div class="right">
            <img src="tdls.jpeg" alt="">
        </div>
    </div>
    <script>
        const togglePassword = document.getElementById('eyeIcon');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle the eye / eye slash icon
            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
<script src="logic.js"></script>