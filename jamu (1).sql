-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 29, 2026 at 01:58 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jamu`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail_pesanan` int NOT NULL,
  `id_pesanan` int DEFAULT NULL,
  `id_produk` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail_pesanan`, `id_pesanan`, `id_produk`, `jumlah`, `harga`) VALUES
(6, 7, 7, 1, 13500.00),
(7, 8, 9, 1, 16000.00),
(8, 9, 9, 1, 16000.00),
(9, 10, 8, 1, 17000.00);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama`) VALUES
(1, 'Pil'),
(2, 'Cair'),
(3, 'Serbuk'),
(4, 'Serbuk dan Pil'),
(5, 'Cair dan Serbuk'),
(6, 'Selai'),
(7, 'Krim'),
(8, 'Kapsul');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id_keranjang` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_produk` int DEFAULT NULL,
  `jumlah` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`id_keranjang`, `id_user`, `id_produk`, `jumlah`) VALUES
(10, 1, 6, 1),
(14, 1, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_pesanan` int DEFAULT NULL,
  `metode_pembayaran` enum('transfer bank','e-wallet','cod') DEFAULT NULL,
  `status` enum('pending','dibayar','gagal') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `alamat` text,
  `total` int DEFAULT NULL,
  `status` enum('diproses','dikirim','selesai') DEFAULT 'diproses',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `metode_pembayaran` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_user`, `alamat`, `total`, `status`, `created_at`, `metode_pembayaran`) VALUES
(7, 1, 'Jl.Gersik Putih Barat, Kalianget Timur, Sumenep.', 13500, 'selesai', '2026-04-29 06:31:28', 'Transfer Bank'),
(8, 1, 'Jl.Gersik Putih Barat, Kalianget Timur, Sumenep.', 16000, 'dikirim', '2026-04-29 06:50:54', 'Transfer Bank'),
(9, 1, 'Jl.Gersik Putih Barat, Kalianget Timur, Sumenep.', 16000, 'diproses', '2026-04-29 06:51:35', 'Transfer Bank'),
(10, 1, 'Jl.Gersik Putih Barat, Kalianget Timur, Sumenep.', 17000, 'diproses', '2026-04-29 09:19:59', 'Transfer Bank');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int NOT NULL,
  `id_kategori` int DEFAULT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `detail_produk` text,
  `harga` decimal(10,2) DEFAULT NULL,
  `stok` int DEFAULT '0',
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `nama`, `detail_produk`, `harga`, `stok`, `gambar`, `created_at`) VALUES
(6, 1, 'Empot-empot legit', 'Khasiat:\r\nMengurangi lendir berlebih, mengatasi keputihan serta menimbulkan sensasi denyut saat berhubungan.\r\n\r\nKandungan:\r\nkunci, kunyit, pinang muda parabes.\r\n\r\nAturan minum:\r\n-\r\n\r\nEfek samping:\r\nTidak ada.\r\n\r\nJenis:\r\nPil.\r\n\r\nProdusen:\r\nMadura Sari\r\n\r\nLokasi pemasaran:\r\nJl Pahlawan 21 Sampang.\r\n\r\nLokasi produksi:\r\nJl Pahlawan 21 Sampang.\r\n\r\nKabupaten:\r\nSampang.\r\n\r\nPerizinan:\r\nKEMENKES.', 16000.00, 35, 'empot_empot_legit.jpg', '2026-04-27 08:06:12'),
(7, 1, 'Galian singset', 'Khasiat:\r\nmengurangi lemak dalam tubuh, menambah nafsu makan dan melancarkan BAB.\r\n\r\nKandungan:\r\nkunyit, daun sirih, delima putih.\r\n\r\nAturan minum:\r\n-\r\n\r\nEfek samping:\r\nTidak ada.\r\n\r\nJenis:\r\nPil.\r\n\r\nProdusen:\r\nMadura sari.\r\n\r\nLokasi pemasaran:\r\nJl Pahlawan 21 Sampang.\r\n\r\nLokasi produksi:\r\nJl Pahlawan 21 Sampang.\r\n\r\nKabupaten:\r\nSampang.\r\n\r\nPerizinan:\r\nKEMENKES.', 13500.00, 39, '69ef1b3e15b87.jpg', '2026-04-27 08:15:58'),
(8, 1, 'Galian rapat wangi', 'Khasiat:\r\nmengurangi bau badan, mengurangi bau tidak sedap serta membuat rapet dan kesed area kewanitaan.\r\n\r\nKandungan:\r\nkunci, kunyit, pinang muda parabes.\r\n\r\nAturan minum:\r\n-\r\n\r\nEfek samping:\r\ntidak ada.\r\n\r\nJenis:\r\nPil.\r\n\r\nProdusen:\r\nMadura Sari.\r\n\r\nLokasi pemasaran:\r\nJl Pahlawan 21 Sampang.\r\n\r\nLokasi produksi:\r\nJl Pahlawan 21 Sampang.\r\n\r\nKabupaten:\r\nSampang.\r\n\r\nPerizinan:\r\nKEMENKES.', 17000.00, 49, '69ef1d0ba380c.jpg', '2026-04-27 08:23:39'),
(9, 1, 'Jamu kecantikan', 'Khasiat:\r\nperawatan khusus remaja putri, mengurangi bau badan dan mengatasi keputihan.\r\n\r\nKandungan:\r\ndaun sirih, kunyit, kulit manggis, kunci, pinang muda.\r\n\r\nAturan minum:\r\n-\r\n\r\nEfek samping:\r\nTidak ada.\r\n\r\nJenis:\r\nPil.\r\n\r\nProdusen:\r\nMadura sari.\r\n\r\nLokasi pemasaran:\r\nJl Pahlawan 21 Sampang.\r\n\r\nLokasi produksi:\r\nJl Pahlawan 21 Sampang.\r\n\r\nKabupaten:\r\nSampang.\r\n\r\nPerizinan:\r\nKEMENKES.', 16000.00, 39, '69ef1f3a4863d.jpg', '2026-04-27 08:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `query_searching`
--

CREATE TABLE `query_searching` (
  `id_search` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `keyword` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id_ulasan` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `id_produk` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `komentar` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `jwt_token` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `alamat` text,
  `no_hp` varchar(20) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `role`, `jwt_token`, `created_at`, `updated_at`, `foto`, `alamat`, `no_hp`, `tempat_lahir`, `tanggal_lahir`) VALUES
(1, 'Natun', 'hasanatun@gmail.com', '$2y$12$f.es2OWhzHMRMZkzqMEIy.ztrZT3XDLdpx6HL6DLbZxTLlusF6JdS', 'customer', NULL, '2026-04-26 03:10:44', '2026-04-26 03:10:44', '1777311775_69ef1d0ba380c.jpg', 'Jl.Gersik Putih Barat, Kalianget Timur, Sumenep.', '085712398498', 'Sumenep', '2004-03-09'),
(2, 'Elma', 'awfiniawaw@gmail.com', '$2y$10$MANZoEl3Fxg1SXM.V2Aqs.jEYSbBxDtFKIMhIYdyzeruN4hnyycBK', 'customer', NULL, '2026-04-29 07:46:23', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail_pesanan`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `query_searching`
--
ALTER TABLE `query_searching`
  ADD PRIMARY KEY (`id_search`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id_ulasan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `query_searching`
--
ALTER TABLE `query_searching`
  MODIFY `id_search` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id_ulasan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `query_searching`
--
ALTER TABLE `query_searching`
  ADD CONSTRAINT `query_searching_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
