-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2025 at 06:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `jabatan` enum('kasir','admin') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `gender` enum('laki-laki','perempuan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `username`, `password`, `image`, `status`, `telepon`, `jabatan`, `alamat`, `gender`) VALUES
(1, 'ssnnaaoke@gmail.com', 'isna', '123', 'bgpink.jpg', 'Aktif', NULL, NULL, NULL, NULL),
(7, 'dvla@gmail.com', 'devilla', '098', 'bgpink.jpg', 'Tidak Aktif', NULL, NULL, NULL, NULL),
(8, 'friska@gmail.com', 'friskaOL0', '123', 'bgpink.jpg', 'Tidak Aktif', NULL, NULL, NULL, NULL),
(11, 'naomi@gmail.com', 'naomi', '210', 'bgpink.jpg', 'Tidak Aktif', '089685221524', 'kasir', 'jl.pulo timaha', 'perempuan'),
(12, 'cici@gmail.com', 'cici', '123', '', 'Tidak Aktif', '089685221524', 'admin', 'kp.pengarengan', 'perempuan');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`, `image`) VALUES
(1, 'INSTAN', 'kat_instan.png'),
(3, 'PASHMINA', 'kat_pashmina.png'),
(5, 'SEGI EMPAT', 'kat_segi4.png');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `transaction_amount` int(100) NOT NULL,
  `point` int(11) NOT NULL,
  `status` enum('active','non-active') NOT NULL,
  `last_transaction` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `name`, `email`, `phone`, `transaction_amount`, `point`, `status`, `last_transaction`) VALUES
(1, 'sakaa', 'saka@gmail.com', '0895379220008', 5, 230, 'active', '2025-07-28 05:52:24'),
(2, 'cici', 'cici@gmail.com', '089666222875', 6, 275, 'non-active', '2025-07-28 05:47:07');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`id`, `email`, `token`, `expires`) VALUES
(12, 'ssnnaaoke@gmail.com', 'f444ef7c19ac1bad18c67cd1759465e21c7c36b2be2f7e52f279e5fee561096cc296e85fce5ec19691e5312e897280bc6760', '2025-07-22 05:09:58');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `starting_price` decimal(10,0) NOT NULL,
  `selling_price` decimal(10,0) NOT NULL,
  `margin` decimal(10,0) NOT NULL,
  `fid_category` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `barcode`, `qty`, `starting_price`, `selling_price`, `margin`, `fid_category`, `image`, `description`) VALUES
(1, 'hijab instan (milo)', '11510', 5, 20000, 30000, 10000, 1, 'instan1.jpg', 'bahan halus dan lembut'),
(2, 'pashmina voal (coksu)', '90109', 6, 25000, 35000, 10000, 3, 'pas1.jpg', 'bahan halus dan lembut'),
(3, 'hijab bergo (navy)', '55916', 5, 20000, 30000, 10000, 1, 'instan2.png', 'bahan halus dan lembut'),
(4, 'pashmina ceruty (mocca)', '39625', 10, 25000, 35000, 10000, 3, 'pas2.jpg', 'bahan halus dan lembut'),
(5, 'hijab instan (green)', '41915', 6, 22000, 32000, 10000, 1, 'instan3.jpg', 'bahan halus dan lembut'),
(6, 'pashmina silk', '83577', 0, 30000, 45000, 15000, 3, 'pas3.png', 'bahan halus dan lembut'),
(7, 'hwgdehgfr', '97021', 4, 40000, 50000, 10000, 1, '', 'nvbfvhfv'),
(8, 'pao', '91751', 21, 20000, 30000, 10000, 1, '', 'e'),
(9, 'OPI', 'PRD-9', 88, 9000, 100000, 91000, 3, '', 'nvbfvhfv'),
(10, 'IPOI', 'PRD-10', 5, 50000, 80000, 30000, 3, '43.png', 'e'),
(11, 'pashmina jersey (coksu)', '00011', 22, 45000, 4500000, 4455000, 3, 'pas4.png', '0OR'),
(12, 'segiempat (hitam)', '00012', 14, 20000, 35000, 15000, 5, '45.jpg', 'bahan halus dan lembut');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id_transaksi` int(11) NOT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `tanggal_beli` date DEFAULT NULL,
  `admin` varchar(50) DEFAULT NULL,
  `harga` int(100) NOT NULL,
  `potongan` varchar(100) NOT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `uang_dibayar` int(11) DEFAULT NULL,
  `kembalian` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id_transaksi`, `nama_produk`, `tanggal_beli`, `admin`, `harga`, `potongan`, `total_harga`, `uang_dibayar`, `kembalian`, `phone`) VALUES
(1, 'hijab segiempat motif pink (1x)', '2025-05-04', 'isna', 0, '', 40500, 50000, 9500, NULL),
(2, 'hijab segiempat motif pink (1x), pashmina voal (coksu) (1x)', '2025-05-04', 'isna', 0, '', 85500, 100000, 14500, NULL),
(3, 'hijab segiempat motif pink (1x), pashmina voal (coksu) (1x)', '2025-05-04', 'isna', 0, '', 85500, 100000, 14500, NULL),
(4, 'hijab segiempat motif pink (5x)', '2025-05-04', 'isna', 0, '', 202500, 300000, 97500, NULL),
(5, 'hijab segiempat motif pink (2x)', '2025-05-04', 'isna', 0, '', 81000, 100000, 19000, NULL),
(6, 'pashmina voal (coksu) (1x)', '2025-05-04', 'isna', 0, '', 45000, 45000, 0, NULL),
(7, 'pashmina voal (coksu) (1x)', '2025-05-04', 'isna', 0, '', 45000, 45000, 0, NULL),
(8, 'hijab instan (milo) (2x)', '2025-05-05', 'isna', 0, '', 72000, 100000, 28000, NULL),
(9, 'hijab instan (milo) (4x), hijab segiempat motif pink (1x)', '2025-05-05', 'isna', 0, '', 205000, 250000, 45000, NULL),
(10, 'hijab segiempat motif pink (1x), pashmina voal (coksu) (1x)', '2025-05-05', 'isna', 0, '', 85500, 100000, 14500, NULL),
(11, '', '2025-05-05', 'isna', 0, '', 0, 10000, 10000, NULL),
(12, 'hijab instan (milo) (1x)', '2025-05-05', 'isna', 0, '', 40000, 50000, 10000, NULL),
(13, 'pashmina voal (coksu) (3x)', '2025-05-05', 'isnaa', 0, '', 135000, 150000, 15000, NULL),
(14, 'segiempat motif (purple) (2x)', '2025-05-05', 'isnaa', 0, '', 81000, 100000, 19000, NULL),
(15, 'segiempat motif (purple) (1x)', '2025-05-05', 'isnaa', 0, '', 40500, 50000, 9500, NULL),
(16, 'pashmina voal (coksu) (2x)', '2025-05-06', 'isnaa', 0, '', 90000, 100000, 10000, NULL),
(17, 'segiempat motif (hijau) (3x)', '2025-05-06', 'isnaa', 0, '', 108000, 200000, 92000, NULL),
(18, 'pashmina jersey (coksu) (1x)', '2025-05-08', 'isnaa', 0, '', 45000, 40000, -5000, NULL),
(19, 'segiempat motif (hijau) (2x)', '2025-05-11', 'isnaa', 0, '', 72000, 75000, 3000, NULL),
(20, 'hijab instan (milo) (5x)', '2025-05-11', 'isnaa', 0, '', 200000, 200000, 0, NULL),
(21, 'pashmina voal (coksu) (1x)', '2025-05-11', 'isnaa', 0, '', 50000, 100000, 50000, NULL),
(22, 'pashmina voal (coksu) (1x)', '2025-05-13', 'isnaa', 0, '', 45000, 50000, 5000, NULL),
(23, 'pashmina voal (coksu) (3x)', '2025-05-13', 'isnaa', 0, '', 135000, 150000, 15000, NULL),
(24, 'hijab instan (milo) (2x)', '2025-05-13', 'isnaa', 0, '', 72000, 100000, 28000, NULL),
(25, 'hijab segiempat motif pink (3x)', '2025-05-14', 'isnaa', 0, '', 135000, 150000, 15000, NULL),
(26, 'segiempat motif (hijau) (2x)', '2025-05-14', 'isnaa', 0, '', 72000, 100000, 28000, NULL),
(27, 'hijab segiempat motif pink (2x)', '2025-05-14', 'isnaa', 0, '', 81000, 100000, 19000, NULL),
(28, 'pashmina jersey (coksu) (1x)', '2025-05-14', 'mey', 0, '', 45000, 30000, -15000, NULL),
(29, 'pashmina jersey (coksu) (1x)', '2025-05-14', 'mey', 0, '', 45000, 50000, 5000, NULL),
(30, 'hijab instan (milo) (3x)', '2025-05-14', 'mey', 0, '', 108000, 200000, 92000, NULL),
(31, 'hijab instan (milo) (1x)', '2025-05-14', 'mey', 0, '', 36000, 50000, 14000, '089666222875'),
(32, 'segiempat motif (purple) (1x)', '2025-05-14', 'mey', 0, '', 40500, 50000, 9500, '0895379220008'),
(33, 'segiempat polos (pink) (1x)', '2025-05-15', 'isnaa', 0, '', 31500, 50000, 18500, '089666222875'),
(34, 'pashmina ceruty (mocca) (2x)', '2025-05-15', 'isnaa', 0, '', 63000, 100000, 37000, '0895379220008'),
(35, 'pashmina ceruty (mocca) (2x)', '2025-05-15', 'isnaa', 0, '', 70000, 80000, 10000, ''),
(36, 'pashmina ceruty (mocca) (2x)', '2025-05-15', 'isnaa', 0, '', 63000, 70000, 7000, '0895379220008'),
(37, 'segiempat motif (purple) (1x)', '2025-05-18', 'isnaa', 0, '', 40500, 50000, 9500, '089666222875'),
(38, 'pashmina voal (coksu) (1x)', '2025-05-18', 'isnaa', 0, '', 40500, 50000, 9500, '089666222875'),
(39, 'hijab instan (milo) (1x)', '2025-05-18', 'isnaa', 0, '', 31500, 40000, 8500, '089666222875'),
(40, 'hijab bergo (navy) (1x)', '2025-05-18', 'isnaa', 0, '', 31500, 45000, 13500, '089666222875'),
(41, 'hijab bergo (navy) (1x)', '2025-05-18', 'isnaa', 0, '', 31500, 100000, 68500, '089666222875'),
(42, 'hijab instan (milo) (1x)', '2025-05-18', 'isnaa', 0, '', 31500, 80000, 48500, '089666222875'),
(43, 'segiempat motif (purple) (1x), pashmina voal (coksu) (1x), hijab bergo (navy) (1x)', '2025-05-18', 'isnaa', 0, '', 112500, 150000, 37500, '089666222875'),
(44, 'pashmina voal (coksu) (2x)', '2025-05-18', 'isnaa', 0, '', 81000, 100000, 19000, '089666222875'),
(45, 'hijab instan (milo) (1x)', '2025-05-18', 'isnaa', 0, '', 31500, 50000, 18500, '0895379220008'),
(46, 'segiempat motif (purple) (1x)', '2025-05-19', 'isnaaaa', 0, '', 40500, 50000, 9500, '089666222875'),
(47, 'hijab instan (coklat) (2x)', '2025-05-19', 'isnaaaa', 0, '', 540000, 600000, 60000, '085718514933'),
(48, 'pashmina ceruty (mocca) (1x)', '2025-05-20', 'isnainy', 0, '', 31500, 50000, 18500, '089666222875'),
(49, 'hijab instan (milo) (1x)', '2025-05-20', 'isnainy', 0, '', 31500, 50000, 18500, '0895379220008'),
(50, 'pashmina voal (coksu) (2x)', '2025-05-25', 'isnainy', 0, '', 72000, 100000, 28000, '0895379220008'),
(51, 'hijab bergo (navy) (1x)', '2025-05-25', 'isnainy', 0, '', 27000, 50000, 23000, '0895379220008'),
(52, 'hijab bergo (navy) (1x)', '2025-05-26', 'isnainy', 0, '', 27000, 50000, 23000, '089666222875'),
(53, 'pashmina silk (5x)', '2025-05-26', 'isnainy', 0, '', 200000, 200000, 0, ''),
(54, 'pashmina ceruty (mocca) (1x)', '2025-05-27', 'isnainy', 0, '', 30000, 40000, 10000, '0895379220008'),
(55, 'pashmina silk (2x)', '2025-05-31', 'isnainy', 0, '', 70000, 100000, 30000, ''),
(56, 'pashmina voal (coksu) (1x)', '2025-06-04', 'isnainy', 0, '', 35000, 50000, 15000, ''),
(57, 'pashmina silk (1x), hijab instan (milo) (3x)', '2025-07-15', 'cici', 0, '', 135000, 1000000, 865000, ''),
(58, 'hijab instan (milo) (1x)', '2025-07-18', 'isna', 0, '', 30000, 100000, 70000, ''),
(59, 'hijab instan (milo) (1x)', '2025-07-18', 'isna', 0, '', 27000, 50000, 23000, '089666222875'),
(60, 'pashmina voal (coksu) (2x), pashmina silk (1x)', '2025-07-18', 'isna', 0, '', 103500, 150000, 46500, '089666222875'),
(61, 'segiempat (hitam) (2x)', '2025-07-22', 'isna', 0, '', 63000, 100000, 37000, '0895379220008'),
(62, 'hijab bergo (navy) (2x), segiempat (hitam) (2x)', '2025-07-22', 'isna', 0, '', 117000, 120000, 3000, '089666222875'),
(63, 'hijab bergo (navy) (1x)', '2025-07-22', 'isna', 0, '', 27000, 50000, 23000, '089666222875'),
(64, 'pashmina jersey (coksu) (1x)', '2025-07-23', 'isna', 0, '450000', 4050000, 4100000, 50000, '0895379220008'),
(65, 'IPOI (2x)', '2025-07-23', 'isna', 0, '16000', 144000, 200000, 56000, '0895379220008'),
(66, 'hijab bergo (navy) (1x)', '2025-07-23', 'isna', 0, '3000', 27000, 30000, 3000, '0895379220008'),
(67, 'hijab instan (green) (1x), hijab bergo (navy) (1x), pashmina voal (coksu) (1x)', '2025-07-24', 'isna', 0, '9700', 87300, 100000, 12700, '089666222875'),
(68, 'segiempat (hitam) (1x)', '2025-07-24', 'isna', 0, '3500', 31500, 50000, 18500, '089666222875'),
(69, 'OPI (2x)', '2025-07-28', 'isna', 0, '20000', 180000, 200000, 20000, '089666222875'),
(70, 'IPOI (1x)', '2025-07-28', 'isna', 0, '0', 80000, 100000, 20000, ''),
(71, 'pao (1x)', '2025-07-28', 'isna', 0, '0', 30000, 50000, 20000, ''),
(72, 'segiempat (hitam) (1x)', '2025-07-28', 'cici', 35000, '3500', 31500, 50000, 18500, '089666222875'),
(73, 'hijab instan (green) (3x)', '2025-07-28', 'cici', 96000, '9600', 86400, 100000, 13600, '0895379220008');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_ibfk_1` (`fid_category`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`email`) REFERENCES `admin` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`fid_category`) REFERENCES `category` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
