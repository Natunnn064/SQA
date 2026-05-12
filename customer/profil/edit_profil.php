<?php
session_start();
include "../db.php";

// 🔥 WAJIB LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: ../autentikasi/login.php?redirect=../profil/edit_profil.php");
    exit;
}

$id_user = intval($_SESSION['id_user']);

// ambil data user
$user = $conn->query("SELECT * FROM users WHERE id_user = $id_user")->fetch_assoc();

// proses update
if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $tempat = $_POST['tempat_lahir'];
    $tanggal = $_POST['tanggal_lahir'];

    // upload foto
    $foto = $user['foto'];

    if ($_FILES['foto']['name']) {
        $namaFile = time() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "../upload/profile/" . $namaFile);
        $foto = $namaFile;
    }

    $conn->query("
        UPDATE users SET
        nama='$nama',
        alamat='$alamat',
        no_hp='$no_hp',
        tempat_lahir='$tempat',
        tanggal_lahir='$tanggal',
        foto='$foto'
        WHERE id_user=$id_user
    ");

    header("Location: ../profil/profil.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil</title>

<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>

<style>
:root {
    --primary: #00A445;
    --secondary: #009968;
    --accent: #009785;
    --light: #f4f8f7;
    --text: #333;
}

/* GLOBAL */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: var(--light);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.navbar {
    background: linear-gradient(90deg, var(--primary), var(--accent));
    color: white;
    display: flex;
    justify-content: space-between;
    padding: 20px 40px;
    align-items: center;
}

.navbar .menu {
    display: flex;
    gap: 25px;
}

.navbar a {
    color: white;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.25s ease-in-out;
}

.navbar a:hover {
    transform: scale(1.1);
    opacity: 0.9;
}

.navbar svg {
    width: 25px;
    height: 25px;
    stroke: white;
    cursor: pointer;
    transition: all 0.25s ease-in-out;
    transform-origin: center;
}

.navbar svg:hover {
    transform: scale(1.25);
    stroke: #eafff7;
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 20px; /* 🔥 ini bikin jarak antar icon lebih lega */
}

.nav-right a {
    display: flex;
    align-items: center;
}

.profile-wrapper {
    position: relative;
}

.profile-icon img {
    width: 30px;
    height: 30px;
    border-radius: 50%; /* 🔥 bikin lingkaran */
    object-fit: cover;
    border: 2px solid white;
    cursor: pointer;
    transition: 0.3s;
}

.profile-icon img:hover {
    transform: scale(1.1);
}

/* DROPDOWN */
.dropdown-menu {
    position: absolute;
    top: 45px;
    right: 0;
    background: white;
    color: #333;
    border-radius: 8px;
    width: 160px;
    display: none;
    flex-direction: column;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    z-index: 100;
}

.dropdown-menu a {
    padding: 10px;
    text-decoration: none;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dropdown-menu a:hover {
    background: #d1d1d1;
}

/* SHOW */
.dropdown-menu.show {
    display: flex;
}

.dropdown-menu svg {
    stroke: #000; /* hitam */
    width: 18px;
    height: 18px;
}


/* CONTAINER */
.container {
    max-width: 800px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

/* FOTO */
.profile-pic {
    text-align: center;
    margin-bottom: 20px;
}

.profile-pic img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary);
}

/* FORM */
input, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* BUTTON */
.btn {
    background: var(--primary);
    color: white;
    padding: 12px;
    border: none;
    width: 100%;
    border-radius: 6px;
    cursor: pointer;
}

/* FOOTER */
.footer {
    background: linear-gradient(90deg, var(--primary), var(--accent));
    color: white;
    padding: 30px;
    margin-top: 40px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div><strong>Jamu Madura</strong></div>

    <div class="menu">
        <a href="../produk/index.php">Beranda</a>
        <a href="../pesanan/pesanan.php">Pesanan Saya</a>
    </div>

    <div class="nav-right">
        <a href="../keranjang/keranjang.php">
            <i data-feather="shopping-cart"></i>
        </a>

        <div class="profile-wrapper">
            <div class="profile-icon" onclick="toggleDropdown()">
                <?php if(!empty($user['foto'])): ?>
                    <img src="../upload/profile/<?= $user['foto']; ?>">
                <?php else: ?>
                    <div style="
                        width:30px;
                        height:30px;
                        border-radius:50%;
                        background:white;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    ">
                        <i data-feather="user" style="stroke: var(--primary); width:18px;"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="../profil/profil.php">
                    <i data-feather="user"></i>
                    Profil Saya
                </a>
                <a href="../autentikasi/logout.php">
                    <i data-feather="log-out"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div style="margin-bottom:15px;">
        <a href="../profil/profil.php" style="display:flex; align-items:center; gap:5px; color:var(--primary); text-decoration:none;">
            <i data-feather="arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
    <form method="POST" enctype="multipart/form-data">

        <div class="profile-pic">
            <img src="../upload/profile/<?= $user['foto'] ?? 'default.png'; ?>">
            <br><br>
            <input type="file" name="foto">
        </div>

        <label>Nama</label>
        <input type="text" name="nama" value="<?= $user['nama']; ?>">

        <label>Alamat</label>
        <textarea name="alamat"><?= $user['alamat']; ?></textarea>

        <label>No Handphone</label>
        <input type="text" name="no_hp" value="<?= $user['no_hp']; ?>">

        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" value="<?= $user['tempat_lahir']; ?>">

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" value="<?= $user['tanggal_lahir']; ?>">

        <button type="submit" name="simpan" class="btn">
            Simpan Perubahan
        </button>

    </form>

</div>

<div class="footer">
    <p>© Jamu Madura</p>
</div>

<script>
function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}

window.onclick = function(e) {
    if (!e.target.closest('.profile-wrapper')) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
}

feather.replace();
</script>

</body>
</html>