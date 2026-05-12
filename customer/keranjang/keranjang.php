<?php
session_start();
include "../db.php";

// 🔥 WAJIB LOGIN
if (!isset($_SESSION['id_user'])) {
    header("Location: ../autentikasi/login.php?redirect=../keranjang/keranjang.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// ambil user
$user = $conn->query("SELECT * FROM users WHERE id_user = $id_user")->fetch_assoc();


// =======================
// 🔥 TAMBAH KE KERANJANG (DB)
// =======================
if(isset($_GET['action']) && $_GET['action'] == 'add'){
    $id_produk = intval($_GET['id']);
    $jumlah = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

    if($jumlah <= 0){
        $jumlah = 1;
    }

    // cek apakah produk sudah ada
    $cek = $conn->query("
        SELECT * FROM keranjang 
        WHERE id_user = $id_user AND id_produk = $id_produk
    ");

    if($cek->num_rows > 0){
        // update jumlah
        $conn->query("
            UPDATE keranjang 
            SET jumlah = jumlah + $jumlah
            WHERE id_user = $id_user AND id_produk = $id_produk
        ");
    } else {
        // insert baru
        $conn->query("
            INSERT INTO keranjang (id_user, id_produk, jumlah)
            VALUES ($id_user, $id_produk, $jumlah)
        ");
    }

    header("Location: keranjang.php");
    exit;
}


// =======================
// 🔥 HAPUS ITEM
// =======================
if(isset($_GET['action']) && $_GET['action'] == 'delete'){
    $id_produk = intval($_GET['id']);

    $conn->query("
        DELETE FROM keranjang 
        WHERE id_user = $id_user AND id_produk = $id_produk
    ");

    header("Location: keranjang.php");
    exit;
}


// =======================
// 🔥 AMBIL DATA DARI DB (BUKAN SESSION)
// =======================
$items = [];

$query = $conn->query("
    SELECT 
        k.id_produk,
        k.jumlah,
        p.nama,
        p.harga,
        p.gambar,
        p.stok
    FROM keranjang k
    JOIN produk p ON k.id_produk = p.id_produk
    WHERE k.id_user = $id_user
");

while($row = $query->fetch_assoc()){
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keranjang</title>

<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>

<style>
:root {
    --primary: #00A445;
    --secondary: #009968;
    --accent: #009785;
    --light: #f4f8f7;
    --text: #333;
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

.header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    margin-top: -30px; /* 🔥 naikkan posisi */
}

.back-btn {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.back-btn svg {
    width: 24px;
    height: 24px;
    stroke: var(--primary);
    transition: 0.2s;
}

.back-btn:hover svg {
    transform: scale(1.2);
}

.order-card {
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

/* ITEM */
.item {
    position: relative;
    display: flex;
    gap: 20px;
    background: white;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    margin-bottom: 15px;
    align-items: center;
}

.item img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}

.btn-delete {
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    border: 1px solid red;
    color: red;
    background: transparent;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-delete:hover {
    background: red;
    color: white;
}

.qty {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
}

.qty button {
    width: 30px;
    height: 30px;
    padding: 0;
    font-size: 16px;
    border-radius: 6px;
    border: 1px solid #ccc;
    background: #f8f8f8;
    cursor: pointer;
}

.qty input {
    width: 50px;
    height: 30px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 6px;
}

/* CHECKOUT */
.checkout-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-top: 20px;
}

.checkout-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-checkout {
    background: var(--primary);
    color: white;
    padding: 10px 20px;
    border: none;
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
        <a href="../pesanan/pesanan.php">Pesanan Saya</a>
    </div>

    <div class="nav-right">
        <a href="keranjang.php">
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

    <div class="header">
        <span onclick="history.back()" class="back-btn">
            <i data-feather="arrow-left"></i>
        </span>
        <h2>Keranjang Saya</h2>
    </div>

<?php if(!empty($items)): ?>
    
<?php foreach($items as $item): ?>
<div class="item">

    <input type="checkbox" name="pilih[]" class="item-check"
           data-price="<?= $item['harga']; ?>"
           data-id="<?= $item['id_produk']; ?>">

    <img src="../upload/<?= $item['gambar']; ?>">

    <div style="flex:1;">
        <h3><?= $item['nama']; ?></h3>
        <p>Rp <?= number_format($item['harga']); ?></p>
        <p><strong>Stok tersedia:</strong> <?= $item['stok']; ?></p>

        <div class="qty">
            <button onclick="kurang(<?= $item['id_produk']; ?>)">-</button>
            <input type="number" id="jumlah<?= $item['id_produk']; ?>" value="<?= $item['jumlah']; ?>" max="<?= $item['stok']; ?>">
            <button onclick="tambah(<?= $item['id_produk']; ?>)">+</button>
        </div>
    </div>

    <button class="btn-delete"
        onclick="window.location.href='keranjang.php?action=delete&id=<?= $item['id_produk']; ?>'">
        Hapus
    </button>

</div>
<?php endforeach; ?>

<div class="checkout-box">
    <div class="checkout-row">
        <div>
            <input type="checkbox" id="checkAll"> Pilih Semua
        </div>

        <div>
            <h3>Total: Rp <span id="total">0</span></h3>
            <button type="button" class="btn-checkout" onclick="checkout()">Checkout</button>
        </div>
    </div>
</div>

<?php else: ?>

    <div class="order-card" style="justify-content:center; text-align:center;">
        <div>
            <i data-feather="shopping-cart" style="width:40px; height:40px; margin-bottom:10px; opacity:0.5;"></i>
            <p style="font-size:16px; color:#777;">
                Belum ada produk yang ditambahkan ke keranjang.
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

window.onclick = function(e) {
    if (!e.target.closest('.profile-wrapper')) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
}

function tambah(id){
    let el = document.getElementById("jumlah"+id);
    let stok = parseInt(el.getAttribute("max"));

    if(parseInt(el.value) < stok){
        el.value++;
    } else {
        alert("Stok tidak cukup!");
    }
    updateTotal();
}

function kurang(id){
    let el = document.getElementById("jumlah"+id);
    if(el.value > 1) el.value--;
    updateTotal();
}

function updateTotal(){
    let total = 0;

    document.querySelectorAll(".item-check:checked").forEach(cb => {
        let id = cb.dataset.id;
        let harga = parseInt(cb.dataset.price);
        let jumlah = document.getElementById("jumlah"+id).value;

        total += harga * jumlah;
    });

    document.getElementById("total").innerText = total.toLocaleString();
}

document.querySelectorAll(".item-check").forEach(cb => {
    cb.addEventListener("change", updateTotal);
});

let checkAll = document.getElementById("checkAll");
if(checkAll){
    checkAll.addEventListener("change", function(){
        let checked = this.checked;
        document.querySelectorAll(".item-check").forEach(cb => cb.checked = checked);
        updateTotal();
    });
}

function checkout(){
    let selected = [];

    document.querySelectorAll(".item-check").forEach(cb => {
        if(cb.checked){
            let id = cb.dataset.id;
            let jumlah = parseInt(document.getElementById("jumlah"+id).value);

            selected.push(`id[]=${id}&jumlah[]=${jumlah}`);
        }
    });

    if(selected.length === 0){
        alert("Pilih produk dulu!");
        return;
    }

    window.location.href = "../pembayaran/checkout.php?" + selected.join("&");
}

feather.replace();
</script>

</body>
</html>