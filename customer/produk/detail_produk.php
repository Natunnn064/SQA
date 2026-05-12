<?php
session_start();
include "../db.php";

$id_user = $_SESSION['id_user'] ?? null;

$user = null;
if($id_user){
    $user = $conn->query("SELECT * FROM users WHERE id_user = $id_user")->fetch_assoc();
}

// ambil id produk
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ambil data produk
$data = $conn->query("
    SELECT p.*, k.nama AS nama_kategori 
    FROM produk p
    JOIN kategori k ON p.id_kategori = k.id_kategori
    WHERE p.id_produk = $id
")->fetch_assoc();

if (!$data) {
    echo "Produk tidak ditemukan!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Produk</title>

<script src="https://unpkg.com/feather-icons"></script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

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

/* NAVBAR */
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

/* BUTTON */
.btn-outline {
    background: transparent;
    border: 1px solid white;
    color: white;
    padding: 7px 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

.btn-outline:hover {
    background: white;
    color: var(--primary);
}

/* DETAIL */
.container,
.desc-box {
    max-width: 1100px;
    width: 100%; /* 🔥 penting */
    margin-left: auto;
    margin-right: auto;
}

.container {
    margin: 40px auto 20px;
    background: white;
    border-radius: 12px;
    padding: 30px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    border: 1px solid #ddd;
}

.desc-box {
    background: white;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #ddd;
    margin-bottom: 40px;
}

.desc-box h3 {
    margin-bottom: 10px;
    color: var(--primary);
}

.desc-box p {
    line-height: 1.0;
    color: #444;
}

/* IMAGE */
.product-img img {
    width: 100%;
    border-radius: 10px;
}

/* INFO */
.product-info h2 {
    margin-bottom: 10px;
}

.kategori {
    color: var(--primary);
    border: 1px solid var(--primary);
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 12px;
    display: inline-block;
}

.price {
    font-size: 28px;
    color: var(--primary);
    font-weight: bold;
    margin: 15px 0;
}

.qty {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 20px 0;
}

.qty button {
    padding: 5px 12px;
    font-size: 16px;
    cursor: pointer;
}

.actions {
    display: flex;
    gap: 15px;
}

.btn-cart {
    border: 1px solid var(--primary);
    color: var(--primary);
    padding: 10px 15px;
    background: transparent;
    cursor: pointer;
    border-radius: 6px;
}

.btn-buy {
    background: var(--primary);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
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

        <?php if($id_user): ?>
            <a href="../pesanan/pesanan.php">Pesanan Saya</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">

        <!-- KERANJANG (WAJIB LOGIN) -->
        <a href="<?= $id_user ? '../keranjang/keranjang.php' : '../autentikasi/login.php' ?>">
            <i data-feather="shopping-cart"></i>
        </a>

        <!-- LOGIN / PROFIL -->
        <?php if(!$id_user): ?>
            <a href="../autentikasi/login.php">
                <button class="btn-outline">Masuk</button>
            </a>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</div>


<!-- DETAIL -->
<div class="container">

    <!-- KIRI (GAMBAR) -->
    <div class="product-img">
        <img src="../upload/<?= $data['gambar']; ?>">
    </div>

    <!-- KANAN (INFO SINGKAT) -->
    <div class="product-info">
        <small class="kategori"><?= $data['nama_kategori']; ?></small>

        <h2><?= $data['nama']; ?></h2>

        <p>⭐ <?= $data['rating'] ?? '0'; ?> / 5</p>

        <div class="price">
            Rp <?= number_format($data['harga'],0,',','.'); ?>
        </div>

        <p><strong>Stok:</strong> <?= $data['stok']; ?></p>

        <!-- QTY -->
        <div class="qty">
            <button onclick="kurang()">-</button>
            <span id="jumlah">1</span>
            <button onclick="tambah()">+</button>
        </div>

        <!-- ACTION -->
        <div class="actions">
            <button class="btn-cart" onclick="tambahKeranjang()">+ Keranjang</button>
            <button class="btn-buy" onclick="beliSekarang()">Beli Sekarang</button>
        </div>
    </div>

</div>

<!-- DESKRIPSI (PINDAH KE BAWAH) -->
<div class="desc-box">
    <h3>Deskripsi Produk</h3>
    <p><?= nl2br($data['detail_produk'] ?? 'Tidak ada deskripsi'); ?></p>
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
let jumlah = 1;

// 🔥 status login dari PHP
const isLogin = <?= $id_user ? 'true' : 'false'; ?>;

function tambah() {
    jumlah++;
    document.getElementById("jumlah").innerText = jumlah;
}

function kurang() {
    if (jumlah > 1) {
        jumlah--;
        document.getElementById("jumlah").innerText = jumlah;
    }
}

// ✅ BELI SEKARANG (cek login dulu)
function beliSekarang() {
    if (!isLogin) {
        window.location.href = "../autentikasi/login.php";
        return;
    }

    let id = <?= $data['id_produk']; ?>;
    let qty = jumlah;

    window.location.href = "../pembayaran/checkout.php?id[]=" + id + "&jumlah[]=" + qty;
}

// ✅ TAMBAH KERANJANG (cek login dulu)
function tambahKeranjang() {
    if (!isLogin) {
        window.location.href = "../autentikasi/login.php";
        return;
    }

    let id = <?= $data['id_produk']; ?>;
    let qty = jumlah;

    window.location.href = "../keranjang/keranjang.php?action=add&id=" + id + "&qty=" + qty;
}

// ✅ DROPDOWN PROFIL
function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}

// klik luar = tutup dropdown
window.onclick = function(e) {
    if (!e.target.closest('.profile-wrapper')) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
}

// render icon
feather.replace();
</script>

</body>
</html>