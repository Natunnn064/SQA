<?php
session_start();
include "../db.php";

// 🔥 WAJIB LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: ../autentikasi/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ambil data user
$user = $conn->query("
    SELECT * FROM users WHERE id_user = $id_user
")->fetch_assoc();

$id = intval($_GET['id'] ?? 0);

$data = $conn->query("
    SELECT * FROM pesanan WHERE id_pesanan = $id
")->fetch_assoc();

if(!$data){
    echo "Pesanan tidak ditemukan";
    exit;
}

list($jenis, $detail) = explode(" - ", $data['metode_pembayaran'] . " - ");

$va = "88".$data['id_pesanan'].rand(1000,9999);
?>

<!DOCTYPE html>
<html>
<head>
<title>Pembayaran</title>

<script src="https://unpkg.com/feather-icons"></script>

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
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI';
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
    transition: all 0.25s ease-in-out; /* 🔥 penting */
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
    gap: 20px;
}

.profile-wrapper {
    position: relative;
}

.profile-icon img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
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
    color: #333;
    text-decoration: none;
    display: flex;
    gap: 8px;
    align-items: center;
}

.dropdown-menu a:hover {
    background: #d1d1d1;
}

.dropdown-menu.show {
    display: flex;
}

.dropdown-menu svg {
    stroke: black;
}

.container {
    width: 95%;
    max-width: 1200px; /* opsional biar ga terlalu lebar di layar besar */
    margin: 60px auto;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #ddd;
}

.total {
    font-size: 26px;
    color: #00A445;
    font-weight: bold;
    margin: 15px 0;
}

.va-box {
    background: #f1f1f1;
    padding: 15px;
    border-radius: 10px;
    margin-top: 15px;
    font-size: 18px;
    letter-spacing: 2px;
}

.label {
    color: #777;
    font-size: 14px;
}

.btn {
    margin-top: 25px;
    width: 100%;
    padding: 14px;
    background: #00A445;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
}

.btn:hover {
    background: #009968;
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
<div class="card">

    <div class="label">Total Pembayaran</div>
    <div class="total">
        Rp <?= number_format($data['total'],0,',','.'); ?>
    </div>

    <!-- 🔥 TRANSFER BANK -->
    <?php if($jenis == 'transfer'): ?>
        <div class="label">Transfer ke Virtual Account (<?= strtoupper($detail); ?>)</div>
        <div class="va-box"><?= $va; ?></div>

    <!-- 🔥 E-WALLET -->
    <?php elseif($jenis == 'ewallet'): ?>
        <div class="label">Nomor Tujuan <?= strtoupper($detail); ?></div>
        <div class="va-box">08<?= rand(111111111,999999999); ?></div>

    <!-- 🔥 COD -->
    <?php elseif($jenis == 'cod'): ?>
        <div class="va-box">
            Bayar di tempat saat barang diterima
        </div>
    <?php endif; ?>

    <button class="btn" onclick="window.location='../pesanan/pesanan.php'">
        OK
    </button>

</div>
</div>

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