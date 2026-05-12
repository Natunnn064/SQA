<?php
session_start();
include "../db.php";

// 🔥 WAJIB LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: ../autentikasi/login.php?redirect=../pesanan/pesanan.php");
    exit;
}

$id_user = intval($_SESSION['id_user']);

$user = $conn->query("
    SELECT * FROM users WHERE id_user = $id_user
")->fetch_assoc();

// ambil data pesanan
$pesanan = $conn->query("
    SELECT 
        ps.id_pesanan,
        ps.created_at,
        ps.status,
        ps.total,
        d.jumlah,
        d.harga,
        p.nama,
        p.gambar
    FROM pesanan ps
    JOIN detail_pesanan d ON ps.id_pesanan = d.id_pesanan
    JOIN produk p ON d.id_produk = p.id_produk
    WHERE ps.id_user = $id_user
    ORDER BY ps.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan Saya</title>

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
    width: 95%;
    max-width: 1400px; /* biar tetap rapi di layar besar */
    margin: 40px auto;
}

h2 {
    margin-bottom: 10px;
    margin-top: -10px; /* 🔥 bikin naik sedikit */
}

/* CARD PESANAN */
.order-card {
    position: relative; /* 🔥 penting untuk posisi absolute */
    display: flex;
    gap: 20px;
    background: white;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    margin-bottom: 15px;
    align-items: center;
}

.order-card img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}

.status {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
    font-weight: 600;
}

.status.proses {
    background: #f1c40f; /* kuning */
    color: #333;
}

.status.dikirim {
    background: #2ecc71; /* hijau */
}

.status.selesai {
    background: #3498db; /* biru */
}

.status.batal {
    background: #e74c3c; /* merah (opsional) */
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

<!-- CONTENT -->
<div class="container">
    <h2>Riwayat Pesanan</h2><br>

<?php if($pesanan->num_rows > 0): ?>

    <?php while($p = $pesanan->fetch_assoc()): ?>
    <div class="order-card">

        <!-- STATUS DI POJOK -->
        <?php
        $status = strtolower(trim($p['status']));

        // mapping biar aman
        if ($status == 'diproses') $status = 'proses';
        if ($status == 'kirim') $status = 'dikirim';
        if ($status == 'selesai') $status = 'selesai';
        ?>

        <span class="status <?= $status ?>">
            <?= ucfirst($p['status']); ?>
        </span>

        <img src="../upload/<?= $p['gambar']; ?>">

        <div style="flex:1;">
            <h3><?= $p['nama']; ?></h3>
            <p>Jumlah: <?= $p['jumlah']; ?></p>
            <p>Total: Rp <?= number_format($p['total']); ?></p>
            <p>Tanggal: <?= $p['created_at']; ?></p>
        </div>

    </div>
    <?php endwhile; ?>

<?php else: ?>

    <div class="order-card" style="justify-content:center; text-align:center;">
        <div>
            <i data-feather="shopping-bag" style="width:40px; height:40px; margin-bottom:10px; opacity:0.5;"></i>
            <p style="font-size:16px; color:#777;">
                Belum ada pesanan yang dibuat.
            </p>
        </div>
    </div>

<?php endif; ?>

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

document.addEventListener("click", function(e){
    if (!e.target.closest('.profile-wrapper')) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
});

feather.replace();
</script>

</body>
</html>