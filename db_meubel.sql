-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2026 at 10:17 AM
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
-- Database: `db_meubel`
--

-- --------------------------------------------------------

--
-- Table structure for table `furniture`
--

CREATE TABLE `furniture` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL,
  `material` varchar(100) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `craftsmanship` varchar(255) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `care_instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `furniture`
--

INSERT INTO `furniture` (`id`, `sku`, `name`, `category`, `material`, `price`, `stock`, `image_path`, `image_url`, `description`, `craftsmanship`, `dimensions`, `care_instructions`, `created_at`, `updated_at`) VALUES
(2, 'SC-8812', 'Kursi Santai Saka', 'Ruang Tamu', 'Kayu Mahoni', 17500000.00, 2, NULL, 'https://lh3.googleusercontent.com/aida-public/AB6AXuDbE0p_YDD9DBeVrL_z3pf0Sma9oYtitNW-ay69kj_ktIOdJSRfW-JxI1Nhxj8uRkZ3EI9f0dv4jNO3u2d2DYmm_fWFiTrECteEx3OICWzE1EENfpX2IxRW7vUquhdGo93NE9g1hl8OwSK-UakTDVMEXpG7EKP2GGTC2SmJc6zLkiapYoiewVA0seDovuC0YWCUznhkF0cClN2AzKPGDc4L_4gsqQ2FXZTYpCSrHnQjV4dAqOWZ0f40', 'Kursi santai premium dengan rangka kayu mahoni solid berukiran halus dan pelapis kain beludru berwarna hijau zamrud. Desain ergonomis memberikan kenyamanan ekstra.', 'Pahatan Tangan Halus', '85 x 80 x 95 cm', 'Gunakan penyedot debu dengan ujung sikat lembut untuk permukaan kain beludru. Bersihkan noda ringan dengan lap lembab.', '2026-07-15 08:26:39', '2026-07-15 08:26:39'),
(3, 'UP-1005', 'Lampu Gantung Ubud', 'Pencahayaan', 'Rotan', 4500000.00, 45, NULL, 'https://lh3.googleusercontent.com/aida-public/AB6AXuDepGWzEfsgOPVaGH9CQ8tNstCV0ceqcZ9jDkEKyIhghel8Jyarv9a47rd13FCbtynJwJ-Z8JCQr7jm--ZxjyOOZsJdCkc1lwo3-cSXnE8T1w9JVS2tLJeQeb67mRLRPE6zy3Nvcbl0p0q4WJvmUhY5r57UlTAKrytJfj5RTZZc1GCAaWIHkEt8fYe8bEo8tBbqw8CsxZKpWe4VVgvXI1jxxWWAdLISubbFh1Kud1qWGU0vOfT8RqYf', 'Lampu gantung rotan dengan tenunan tangan yang rumit dan bernilai estetis tinggi. Memancarkan pola bayangan geometris hangat yang menenangkan di dinding.', 'Anyaman Tangan Tradisional', 'Diameter 50 cm, Tinggi 60 cm', 'Bersihkan berkala dari debu menggunakan kuas kering atau kemoceng. Hindarkan dari kelembaban tinggi agar rotan tidak berjamur.', '2026-07-15 08:26:39', '2026-07-15 08:26:39'),
(4, 'NC-1204', 'Kredensa Nusa', 'Ruang Tamu', 'Kayu Jati', 58000000.00, 8, NULL, 'https://lh3.googleusercontent.com/aida-public/AB6AXuB1rgn-owRDBW8t_5Oj9TTEGhTk_gAriD-Irryis8aRWMxm-4V1bVDMM1Ft5gHhHhLCamf5bt1bTZSZfI4Ni546cDFK4yI9hY0Kx8msR_Qfeg4BrecMN_DTdcLArwPvofV94CDmbeKwig5h2TnOx8sBdYZNZg9AFAHnuSiORagSUn1EUwJTo4cSw9ET9qIgskOPrHLBe6DNhgBSnDc41Sr3bLdBkF8NBOYKh72eH6Kw-wNReiCDPP6A', 'Kredensa kayu jati dengan pintu geser berbilah (slatted doors). Sangat fungsional untuk menyimpan perlengkapan ruang makan atau ruang keluarga dengan gaya minimalis Skandinavia-Jepang (Japandi).', 'Konstruksi Sambungan Mortise & Tenon', '160 x 45 x 75 cm', 'Bersihkan dengan lap mikrofiber kering. Oleskan minyak kayu secara berkala untuk menjaga kilau alami kayu jati.', '2026-07-15 08:26:39', '2026-07-15 08:26:39'),
(5, 'BT-4041', 'Meja Sisi Bumi', 'Ruang Tamu', 'Kayu Ash', 6800000.00, 15, NULL, 'https://lh3.googleusercontent.com/aida-public/AB6AXuCDxUY9raKUGQLSBSgINjNBE6Bcq83r5BaY3Q1OemGpTqtD_9kJVU6LL38PKi_OJW_fKJUZ5RF9OIYc00rHV_-tDzhPLfmDrtOF-H-zoAaVJ2qoCaDo1zw5a1gkSbVlyL-lqR1OFURZiwtQTMrUkMzBT6_krgbKAMlpDT3v0kKKKEDGlXwV6C9MtDIC22joCdFtDsLzYdDGJUhc7t8vIZbXSfe3kn5q0leHsPESalKDZtYhI3zPO-ya', 'Meja sisi bundar minimalis dengan kaki tripod ramping. Memperlihatkan tekstur serat kayu ash alami yang elegan, ideal untuk menemani kursi santai Anda.', 'Kaki Tripod Bubut Presisi', 'Diameter 45 cm, Tinggi 55 cm', 'Gunakan tatakan gelas untuk mencegah bercak air pada permukaan meja. Lap dengan kain lembab ringan jika terkena tumpahan cairan.', '2026-07-15 08:26:39', '2026-07-15 08:26:39'),
(8, 'EJ-0967', 'KURSI SANTAI', 'Ruang Tamu', 'Kayu Jati', 800000.00, 89, NULL, 'https://sarjanamebel.com/wp-content/uploads/2018/11/Set-Kursi-Sofa-Sudut-Sarjana-Mebel.jpg', NULL, NULL, '180 x 90 x 75 cm', NULL, '2026-07-15 21:01:12', '2026-07-15 21:01:12'),
(9, 'OD-8738', 'DIPAN NAGA', 'Kamar Tidur', 'Kayu Jati', 7000000.00, 6, NULL, 'https://th.bing.com/th/id/OIP.KQTD1Pkp5z6yZ2U2Fj_GsgHaFj?w=258&h=193&c=7&r=0&o=7&dpr=1.5&pid=1.7&rm=3', NULL, NULL, NULL, NULL, '2026-07-15 21:07:58', '2026-07-15 21:08:32'),
(10, 'RJ-3433', 'LEMARI MULTIFUNGSI', 'Kamar Tidur', 'Kayu Mahoni', 450000.00, 100, NULL, 'https://asset.morefurniture.id/PRODUCT/pd-1681-1782833385-2.webp', 'DENVER Bookcase adalah rak buku minimalis modern dengan desain multifungsi yang fleksibel dan tampil premium. Menggunakan finishing membran, permukaannya terlihat lebih halus, rapi, dan elegan. Rak dapat digunakan dalam posisi berdiri maupun rebah, sesuai kebutuhan ruang Anda. Storage-nya juga dapat dipindah-pindah dan laci bongkar pasang yang bisa dipindahkan. Meiliki warna Govina Oak yang natural, rak ini memberikan kesan hangat dan estetik, cocok untuk ruang tamu, ruang kerja, kamar, maupun area dekorasi rumah.', NULL, '180 x 90 x 75 cm', NULL, '2026-07-15 21:12:32', '2026-07-15 21:12:32'),
(11, 'CV-8765', 'SOFA MODERN', 'Ruang Tamu', 'Kayu Mahoni', 500000.00, 100, NULL, 'https://asset.morefurniture.id/PROCELLA/CLOUD/ss-399-1711429141-3.webp', 'Procella Sofabed Cloud terbuat dari material busa yang empuk dan nyaman, dilapisi kain halus premium yang water repellant, memberikan kenyamanan duduk di waktu yang lama dan aman jika terkena cipratan air. Sofa Cloud memiliki pilihan warna lembut yang cocok untuk ruang tamu dengan style desain minimalis, japandi, hingga skandinavian. Sandaran tangan Sofa Cloud bisa dilepas pasang untuk memaksimalkan fungsi sofa yang bisa dibuka lipat menjadi sofa bed, membuatmu tetap nyaman saat berbaring.', NULL, NULL, NULL, '2026-07-15 21:17:38', '2026-07-15 21:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `created_at`) VALUES
(1, 'admin', '$2y$10$KwqIgfJmQdwjdp99r6yKzurzbpkpaB1qGbhu9qT4QGeQgZZXyLK6y', 'admin', '2026-07-15 19:07:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `furniture`
--
ALTER TABLE `furniture`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `furniture`
--
ALTER TABLE `furniture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
