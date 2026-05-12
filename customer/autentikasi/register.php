<?php
include "../db.php";

if(isset($_POST['register'])){

    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];
    $role = $_POST['role'];

    // cek password sama
    if($password !== $konfirmasi){
        echo "<script>alert('Password tidak sama');</script>";
    } else {

        // enkripsi password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // cek email sudah ada
        $cek = $conn->query("SELECT * FROM users WHERE email='$email'");
        if($cek->num_rows > 0){
            echo "<script>alert('Email sudah terdaftar');</script>";
        } else {

            // simpan ke database
            $conn->query("
                INSERT INTO users (nama, email, password, role)
                VALUES ('$nama', '$email', '$hash', '$role')
            ");

            echo "<script>
                alert('Registrasi berhasil');
                window.location='login.php';
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Jamu Madura</title>

<style>
:root {
    --primary: #00A445;
    --secondary: #009968;
    --accent: #009785;
    --light: #f4f8f7;
}

* {
    box-sizing: border-box; /* 🔥 ini kunci biar semua sejajar */
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* CARD */
.container {
    background: white;
    padding: 35px 30px;
    border-radius: 12px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* INPUT */
.input-group {
    margin-bottom: 18px; /* 🔥 lebih rapi & konsisten */
}

input, select {
    width: 100%;
    padding: 12px; /* 🔥 samakan semua */
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
    font-size: 14px;
}

input:focus, select:focus {
    border-color: var(--primary);
}

/* BUTTON */
.btn {
    width: 100%;
    padding: 12px; /* 🔥 sama dengan input */
    background: var(--primary);
    border: none;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 15px;
}

.btn:hover {
    background: var(--accent);
}
</style>
</head>

<body>

<div class="container">
    <h2>Daftar</h2>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="nama" placeholder="Nama" required>
        </div>

        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="input-group">
            <input type="password" name="konfirmasi" placeholder="Konfirmasi Password" required>
        </div>

        <div class="input-group">
            <select name="role" required>
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="customer">Customer</option>
            </select>
        </div>

        <button type="submit" name="register" class="btn">Daftar</button>
    </form>
</div>

<script>
function register() {
    // simulasi setelah register langsung ke login
    window.location.href = "login.php";
}
</script>

</body>
</html>