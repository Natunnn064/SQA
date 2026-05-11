<?php
$conn = new mysqli("localhost", "root", "", "jamu");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>