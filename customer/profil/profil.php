<?php
session_start();
include "../db.php";

// 🔥 WAJIB LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: ../autentikasi/login.php?redirect=../profil/profil.php");
    exit;
}

$id_user = intval($_SESSION['id_user']);

// 🔥 AMBIL DATA USER (AMAN)
$query = $conn->query("SELECT * FROM users WHERE id_user = $id_user");

if (!$query || $query->num_rows == 0) {
    echo "User tidak ditemukan";
    exit;
}

$user = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Saya</title>

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
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

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

/* DATA */
.data {
    margin-bottom: 15px;
}

.label {
    font-weight: bold;
    color: var(--primary);
}

/* BUTTON */
.btn {
    display: inline-block;
    background: var(--primary);
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    margin-top: 15px;
}

/* FOOTER */
.footer {
    background: linear-gradient(90deg, var(--primary), var(--accent));
    color: white;
    margin-top: auto;
    padding: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

    <div class="profile-pic">
        <img src="../upload/profile/<?= $user['foto'] ?? 'default.png'; ?>">
    </div>

    <div class="data">
        <div class="label">Nama</div>
        <div><?= $user['nama']; ?></div>
    </div>

    <div class="data">
        <div class="label">Alamat</div>
        <div><?= $user['alamat']; ?></div>
    </div>

    <div class="data">
        <div class="label">No Handphone</div>
        <div><?= $user['no_hp']; ?></div>
    </div>

    <div class="data">
        <div class="label">Tempat, Tanggal Lahir</div>
        <div>
            <?= $user['tempat_lahir']; ?>, 
            <?= $user['tanggal_lahir']; ?>
        </div>
    </div>

    <a href="../profil/edit_profil.php" class="btn">
        Edit Profil
    </a>

</div>

<!-- FOOTER -->
<div class="footer">
    <div>
        <h4>Jamu Madura</h4>
        <p>Platform e-commerce jamu tradisional Madura dengan teknologi pencarian berbasis NLP.</p>
    </div>

    <div>
        <h4>Navigasi</h4>
        <p>Tentang Kami</p>
        <p>Kategori Produk</p>
        <p>Cara Pemesanan</p>
        <p>Hubungi Kami</p>
    </div>

    <div>
        <h4>Kontak</h4>
        <p>Email: jamumadura@gmail.com</p>
        <p>Telepon: +62 812-3456-7890</p>
        <p>Alamat: Bangkalan, Madura</p>
    </div>
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