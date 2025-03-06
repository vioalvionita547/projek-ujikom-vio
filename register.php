<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi password
    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Siapkan pernyataan
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        
        // Ikat parameter
        $stmt->bind_param("ss", $username, $hashedPassword); // "ss" menunjukkan bahwa kedua parameter adalah string

        // Eksekusi pernyataan
        if ($stmt->execute()) {
            header("Location: login.php");
            exit(); 
        } else {
            echo "Registration failed. Please try again.";
        }

        // Tutup pernyataan
        $stmt->close();
    }
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styleregister.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Gaya untuk input dan ikon */
        .password-container {
            position: relative;
            margin-bottom: 15px;
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
    <form method="POST">
        Username: <input type="text" name="username" required><br>
        Password: 
        <div class="password-container">
            <input type="password" id="password" name="password" required>
            <span class="toggle-password" id="togglePassword">
                <i class="fa-solid fa-eye" id="eyeIcon"></i>
            </span>
        </div>
        <h6>Kata sandi harus terdiri dari minimal 8 karakter!</h6>
        <button type="submit">Register</button>
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </form>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle the eye / eye slash icon
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>