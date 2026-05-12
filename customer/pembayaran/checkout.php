<?php
session_start();
include "../db.php";

// 🔥 WAJIB LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: ../autentikasi/login.php?redirect=../keranjang/keranjang.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ambil data user
$user = $conn->query("
    SELECT * FROM users WHERE id_user = $id_user
")->fetch_assoc();


// =======================
// 🔥 PROSES CHECKOUT
// =======================
if(isset($_POST['checkout'])){

    if(empty($user['alamat'])){
        echo "<script>
            alert('Alamat masih kosong! Tidak bisa checkout.');
            window.history.back();
        </script>";
        exit;
    }

    $total = intval($_POST['total']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    // ambil metode utama
    $metode = $_POST['metode'] ?? '';
    $detail = '';

    // ambil detail berdasarkan metode
    if($metode == 'transfer'){
        $detail = $_POST['bank'] ?? '';
    } elseif($metode == 'ewallet'){
        $detail = $_POST['ewallet'] ?? '';
    }

    // 🔥 VALIDASI TAMBAHAN
    if($metode == 'transfer' && empty($detail)){
        echo "<script>
            alert('Pilih bank terlebih dahulu!');
            window.history.back();
        </script>";
        exit;
    }

    if($metode == 'ewallet' && empty($detail)){
        echo "<script>
            alert('Pilih e-wallet terlebih dahulu!');
            window.history.back();
        </script>";
        exit;
    }

    // gabungkan metode + detail
    $metode_final = $conn->real_escape_string($metode . ' - ' . $detail);

    $produk_ids = $_POST['produk_id'] ?? [];
    $jumlahs = $_POST['jumlah'] ?? [];
    $hargas = $_POST['harga'] ?? [];

    // 🔥 mulai transaction
    $conn->begin_transaction();

    try {

        // =======================
        // 🔥 VALIDASI STOK
        // =======================
        for($i = 0; $i < count($produk_ids); $i++){
            $pid = intval($produk_ids[$i]);
            $jumlah = intval($jumlahs[$i]);

            $cek = $conn->query("
                SELECT stok FROM produk WHERE id_produk = $pid
            ")->fetch_assoc();

            if(!$cek || $cek['stok'] < $jumlah){
                throw new Exception("Stok tidak cukup!");
            }
        }

        // =======================
        // 🔥 INSERT PESANAN
        // =======================
        $conn->query("
            INSERT INTO pesanan (id_user, total, alamat, metode_pembayaran)
            VALUES ($id_user, $total, '$alamat', '$metode_final')
        ");

        $id_pesanan = $conn->insert_id;

        // =======================
        // 🔥 LOOP PRODUK
        // =======================
        for($i = 0; $i < count($produk_ids); $i++){
            $pid = intval($produk_ids[$i]);
            $jumlah = intval($jumlahs[$i]);
            $harga = intval($hargas[$i]);

            // insert detail
            $conn->query("
                INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga)
                VALUES ($id_pesanan, $pid, $jumlah, $harga)
            ");

            // 🔥 update stok (REAL-TIME)
            $conn->query("
                UPDATE produk 
                SET stok = stok - $jumlah
                WHERE id_produk = $pid
            ");

            // 🔥 hapus dari keranjang
            $conn->query("
                DELETE FROM keranjang 
                WHERE id_user = $id_user AND id_produk = $pid
            ");
        }

        // 🔥 commit
        $conn->commit();

        echo "<script>
            alert('Pembayaran berhasil');
            window.location='detail_pembayaran.php?id=".$id_pesanan."';
        </script>";

    } catch (Exception $e) {

        // 🔥 rollback kalau error
        $conn->rollback();

        echo "<script>
            alert('Checkout gagal: ".$e->getMessage()."');
            window.history.back();
        </script>";
    }

    exit;
}


// =======================
// 🔥 AMBIL DATA PRODUK (GET)
// =======================
$ids = $_GET['id'] ?? [];
$jumlahs = $_GET['jumlah'] ?? [];

if(!is_array($ids) || !is_array($jumlahs)){
    echo "Data tidak valid";
    exit;
}

$items = [];
$total = 0;

for($i = 0; $i < count($ids); $i++){
    $id = intval($ids[$i]);
    $jumlah = intval($jumlahs[$i]);

    if($id <= 0 || $jumlah <= 0) continue;

    $data = $conn->query("
        SELECT * FROM produk WHERE id_produk = $id
    ")->fetch_assoc();

    if($data){
        $data['jumlah'] = $jumlah;
        $items[] = $data;
        $total += $data['harga'] * $jumlah;
    }
}

if(empty($items)){
    echo "Tidak ada produk dipilih";
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>

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

/* CONTAINER */
.container {
    max-width: 1300px; /* 🔥 tambah lebar */
    width: 95%;        /* biar responsif */
    margin: 40px auto;
    display: grid;
    gap: 20px;
}

/* BOX */
.box {
    background: white;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

/* PRODUK */
.product {
    display: flex;
    gap: 20px;
}

.product img {
    width: 100px;
    border-radius: 10px;
}

/* TOTAL */
.total {
    font-size: 20px;
    font-weight: bold;
    color: var(--primary);
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

/* tombol kembali */
.btn-back {
    background: transparent;
    border: 1px solid var(--primary);
    color: var(--primary);
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: 0.3s;
}

.btn-back:hover {
    background: var(--primary);
    color: white;
}

/* tombol buat pesanan */
.btn-order {
    background: var(--primary);
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;

    width: auto; /* 🔥 ini penting biar ga full */
}

.btn-order:hover {
    background: var(--accent);
}

.checkout-action {
    max-width: 1300px;
    width: 95%;
    margin: 0 auto 40px;

    display: flex;
    justify-content: space-between; /* 🔥 kiri & kanan */
    align-items: center;
}

.checkout-action .btn {
    width: 100%;
    font-size: 15px;
    padding: 12px;
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

    <!-- ALAMAT -->
    <div class="box">
        <h3>Alamat Pengiriman</h3>
        <div>
            <strong><?= $user['nama']; ?></strong><br>
            <small>+62 <?= $user['no_hp'] ?? '-' ?></small><br>

            <?php if(empty($user['alamat'])): ?>
                <div style="
                    background:#ffe5e5;
                    color:#d60000;
                    padding:10px;
                    border-radius:6px;
                    margin-top:5px;
                ">
                    Alamat kosong, silahkan lengkapi profil terlebih dahulu!
                </div>
            <?php else: ?>
                <?= htmlspecialchars($user['alamat']); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- PRODUK -->
    <div class="box">
        <h3>Produk Dibeli</h3>

        <?php foreach($items as $item): ?>
        <div class="product">
            <img src="../upload/<?= $item['gambar']; ?>">

            <div>
                <h4><?= $item['nama']; ?></h4>
                <p>Jumlah: <?= $item['jumlah']; ?></p>
                <p>Harga: Rp <?= number_format($item['harga'],0,',','.'); ?></p>
            </div>
        </div>
        <hr>
        <?php endforeach; ?>
    </div>

    <!-- RINCIAN -->
    <div class="box">
        <h3>Rincian Pembayaran</h3>

        <p>Subtotal: Rp <?= number_format($total,0,',','.'); ?></p>
        <p>Ongkir: Rp 10.000</p>

        <hr>

        <p class="total">
            Total: Rp <?= number_format($total + 10000,0,',','.'); ?>
        </p>
    </div>

    <!-- METODE PEMBAYARAN -->
    <div class="box">
        <h3>Metode Pembayaran</h3>

        <!-- METODE UTAMA -->
        <select id="metodeSelect" style="width:100%; padding:10px;">
            <option value="">Pilih Metode</option>
            <option value="transfer">Transfer Bank</option>
            <option value="cod">COD</option>
            <option value="ewallet">E-Wallet</option>
        </select>

        <!-- BANK -->
        <div id="bankBox" style="display:none; margin-top:10px;">
            <select id="bankSelect" style="width:100%; padding:10px;">
                <option value="">Pilih Bank</option>
                <option value="BCA">BCA</option>
                <option value="BRI">BRI</option>
                <option value="Mandiri">Mandiri</option>
            </select>
        </div>

        <!-- E-WALLET -->
        <div id="ewalletBox" style="display:none; margin-top:10px;">
            <select id="ewalletSelect" style="width:100%; padding:10px;">
                <option value="">Pilih E-Wallet</option>
                <option value="DANA">DANA</option>
                <option value="OVO">OVO</option>
                <option value="GoPay">GoPay</option>
            </select>
        </div>
    </div>

<div class="checkout-action">

    <button class="btn-back" onclick="history.back()">
        <i data-feather="arrow-left"></i> Kembali
    </button>

    <form method="POST" id="checkoutForm" style="display:flex; gap:10px;">

        <!-- METODE -->
        <input type="hidden" name="metode" id="metodeHidden">
        <input type="hidden" name="bank" id="bankHidden">
        <input type="hidden" name="ewallet" id="ewalletHidden">

        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="alamat" value="<?= htmlspecialchars($user['alamat']); ?>">

        <?php foreach($items as $item): ?>
            <input type="hidden" name="produk_id[]" value="<?= $item['id_produk']; ?>">
            <input type="hidden" name="jumlah[]" value="<?= $item['jumlah']; ?>">
            <input type="hidden" name="harga[]" value="<?= $item['harga']; ?>">
        <?php endforeach; ?>

        <button type="submit" name="checkout" class="btn-order"
            <?= empty($user['alamat']) ? 'disabled style="background:gray;"' : '' ?>>
            Buat Pesanan
        </button>
    </form>

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
const metodeSelect = document.getElementById("metodeSelect");
const bankBox = document.getElementById("bankBox");
const ewalletBox = document.getElementById("ewalletBox");

const metodeHidden = document.getElementById("metodeHidden");
const bankHidden = document.getElementById("bankHidden");
const ewalletHidden = document.getElementById("ewalletHidden");

metodeSelect.addEventListener("change", function () {
    let metode = this.value;

    // reset tampilan
    bankBox.style.display = "none";
    ewalletBox.style.display = "none";

    // reset hidden
    metodeHidden.value = metode;
    bankHidden.value = "";
    ewalletHidden.value = "";

    if (metode === "transfer") {
        bankBox.style.display = "block";
    } else if (metode === "ewallet") {
        ewalletBox.style.display = "block";
    }
});

// BANK
document.getElementById("bankSelect").addEventListener("change", function () {
    bankHidden.value = this.value;
});

// E-WALLET
document.getElementById("ewalletSelect").addEventListener("change", function () {
    ewalletHidden.value = this.value;
});

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