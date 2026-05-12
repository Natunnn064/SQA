<?php
session_start();
include "../db.php";

$error = "";

if(isset($_POST['login'])){
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    // ambil user dari database
    $query = $conn->query("SELECT * FROM users WHERE nama = '$nama'");
    $user = $query->fetch_assoc();

    if($user){
        // kalau pakai password biasa
        if(password_verify($password, $user['password'])){
            
            // 🔥 SET SESSION
            $_SESSION['id_user'] = $user['id_user'];

            header("Location: ../produk/index.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Jamu Madura</title>

<style>
:root {
    --primary: #00A445;
    --secondary: #009968;
    --accent: #009785;
    --light: #f4f8f7;
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* CARD */
.container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 100%;
    max-width: 350px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* INPUT */
.input-group {
    margin-bottom: 15px;
}

input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    outline: none;
}

input:focus {
    border-color: var(--primary);
}

/* BUTTON */
.btn {
    width: 100%;
    padding: 10px;
    background: var(--primary);
    border: none;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

.btn:hover {
    background: var(--accent);
}

/* ERROR */
.error {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}

/* LINK */
.text {
    text-align: center;
    margin-top: 15px;
}

.text a {
    color: blue;
    text-decoration: none;
    font-weight: 500;
}

.text a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="container">
    <h2>Masuk</h2>

    <?php if($error): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="nama" placeholder="Nama" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button class="btn" name="login">Masuk</button>
    </form>

    <div class="text">
        Belum punya akun? <a href="register.php">Daftar</a>
    </div>
</div>

</body>
</html>