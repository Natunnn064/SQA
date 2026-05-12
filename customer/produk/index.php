<?php
session_start();
include "../db.php";

// ambil user login
$id_user = $_SESSION['id_user'] ?? null;

$user = null;
if($id_user){
    $user = $conn->query("SELECT * FROM users WHERE id_user = $id_user")->fetch_assoc();
}

// Ambil kategori
$kategori = $conn->query("SELECT * FROM kategori");

// filter kategori
$filter_kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

if ($filter_kategori > 0) {
    $produk = $conn->query("
        SELECT 
            p.id_produk,
            p.nama AS nama_produk,
            p.harga,
            p.stok,
            p.gambar, 
            k.nama AS nama_kategori
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.id_kategori = $filter_kategori
    ");
} else {
    $produk = $conn->query("
        SELECT 
            p.id_produk,
            p.nama AS nama_produk,
            p.harga,
            p.stok,
            p.gambar,
            k.nama AS nama_kategori
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jamu Madura</title>

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

/* * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: var(--light);
} */

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

/* HERO */
.hero {
    text-align: center;
    padding: 50px 20px;
    background: #e8f4f2;
}

.search-box {
    max-width: 600px;
    margin: 20px auto;
    display: flex;
    border: 2px solid var(--primary);
    border-radius: 30px;
    overflow: hidden;
    background: white;
}

.search-box input {
    flex: 1;
    padding: 12px;
    border: none;
    outline: none;
}

.search-box button {
    background: var(--primary);
    border: none;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.search-icon {
    stroke: white;
    stroke-width: 1.5;
}

/* FILTER */
.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 30px;
    background: white;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.filter-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-left select {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.filter-right svg {
    width: 20px;
    stroke: var(--primary);
}

/* PRODUCTS */
.products {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    padding: 0 30px 40px;
}

.kategori {
    color: var(--primary);
    border: 1px solid var(--primary);
    padding: 1px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #ddd;
    transition: all 0.25s ease-in-out;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.card-link {
    text-decoration: none;
    color: inherit;
}

.card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

.price {
    color: var(--primary);
    font-weight: bold;
    font-size: 20px;
}

.btn {
    background: var(--primary);
    color: white;
    border: none;
    padding: 10px 15px;
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

<!-- HERO -->
<div class="hero">
    <h2>Cari Jamu dengan Keluhan Anda!</h2>

    <div class="search-box">
        <input type="text" placeholder="Contoh: badan pegal">
        <button>
            <i data-feather="search" class="search-icon"></i>
        </button>
    </div>
</div>

<!-- FILTER -->
<div class="filter-bar">
    <div class="filter-left">
        <i data-feather="filter"></i>
        <select onchange="filterKategori(this.value)">
            <option value="">Semua Kategori</option>
            <?php while($k = $kategori->fetch_assoc()): ?>
                <option value="<?= $k['id_kategori']; ?>"
                    <?= (isset($_GET['kategori']) && $_GET['kategori'] == $k['id_kategori']) ? 'selected' : '' ?>>
                    <?= $k['nama']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select>
            <option>Urutkan</option>
            <option>Harga Terendah</option>
            <option>Harga Tertinggi</option>
            <option>Rating Tertinggi</option>
        </select>
    </div>

    <div class="filter-right">
        <i data-feather="grid"></i>
    </div>
</div>

<!-- PRODUCTS -->
<div class="products">

<?php if ($produk->num_rows > 0): ?>
    
    <?php while($p = $produk->fetch_assoc()): ?>
        <a href="../produk/detail_produk.php?id=<?= $p['id_produk']; ?>" class="card-link">   
            <div class="card">
                <img src="../upload/<?= $p['gambar']; ?>">
                <small class="kategori"><?= $p['nama_kategori']; ?></small>
                <h3><?= $p['nama_produk']; ?></h3>
                <p>⭐ <?= $p['rating'] ?? '0'; ?></p>
                <p>Stok: <?= $p['stok']; ?></p>
                <div class="price">Rp <?= number_format($p['harga'],0,',','.'); ?></div><br>
            </div>
        </a>
    <?php endwhile; ?>

<?php else: ?>

    <p style="grid-column: 1/-1; text-align:center; padding:40px; color:#666;">
        Produk tidak ditemukan.
    </p>

<?php endif; ?>

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
function filterKategori(id) {
    if (id === "") {
        window.location.href = "index.php";
    } else {
        window.location.href = "index.php?kategori=" + id;
    }
}

function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}

// klik luar = tutup dropdown
window.onclick = function(e) {
    if (!e.target.closest('.profile-wrapper')) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
}

feather.replace();
</script>

</body>
</html>

