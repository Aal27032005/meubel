-- ============================================================
-- Database: db_meubel
-- Aplikasi CRUD Inventaris Mebel - Jatijaya Furniture
-- ============================================================
CREATE DATABASE IF NOT EXISTS db_meubel;
USE db_meubel;

-- ── Tabel users ──────────────────────────────────────────────
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun default: admin / admin123
INSERT INTO users (username, password, full_name) VALUES
('admin', '$2y$10$Fz/qh7NbLB/7inLYsSlt6eZUF9LhAbu348/aAXViscvGSzOj..hBi', 'Administrator');

-- ── Tabel furniture ──────────────────────────────────────────
DROP TABLE IF EXISTS furniture;
CREATE TABLE furniture (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    sku                VARCHAR(50)  NOT NULL UNIQUE,
    name               VARCHAR(150) NOT NULL,
    category           VARCHAR(100) NOT NULL,
    material           VARCHAR(100) NOT NULL,
    price              DECIMAL(15,2) NOT NULL,
    stock              INT NOT NULL,
    image_path         VARCHAR(255) DEFAULT NULL,
    image_url          VARCHAR(500) DEFAULT NULL,
    description        TEXT DEFAULT NULL,
    craftsmanship      VARCHAR(255) DEFAULT NULL,
    dimensions         VARCHAR(100) DEFAULT NULL,
    care_instructions  TEXT DEFAULT NULL,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Data Produk Contoh ───────────────────────────────────────
INSERT INTO furniture (sku, name, category, material, price, stock, image_url, description, craftsmanship, dimensions, care_instructions) VALUES
('NT-0923','Meja Makan Nusa','Ruang Makan','Kayu Jati',37500000.00,14,
 'https://lh3.googleusercontent.com/aida-public/AB6AXuCyRc2OYv81Y2EKjO7YARheIrIE--bjQRtFFqvoA-i6iVVXzPvV6GYEqiVnfuVAwEsh6hfKoYvRgJp9JPSNuBvIn5xVZbEfxpB6VMFu9MjThZZiAR6jbJWY6Xx5YZ_x4-k3HG9ZkjCBr4QTdnuYC-M3vc2kp1nNnsOT1-p_G2A5Ji0IiOyLIeDh5CKdVZy5n2vTtUKhpKuXU7UK6T4ov3VZMNwas55zd94qT-CSj0_U66FQXoUI43H',
 'Meja makan berbahan kayu jati Grade-A dari Jawa Tengah.','Tepi Organik Sentuhan Tangan','180 x 90 x 75 cm',
 'Lap dengan kain kering lembut. Olesi minyak jati setiap 6 bulan.'),
('SC-8812','Kursi Santai Saka','Ruang Tamu','Kayu Mahoni',17500000.00,2,
 'https://lh3.googleusercontent.com/aida-public/AB6AXuDbE0p_YDD9DBeVrL_z3pf0Sma9oYtitNW-ay69kj_ktIOdJSRfW-JxI1Nhxj8uRkZ3EI9f0dv4jNO3u2d2DYmm_fWFiTrECteEx3OICWzE1EENfpX2IxRW7vUquhdGo93NE9g1hl8OwSK-UakTDVMEXpG7EKP2GGTC2SmJc6zLkiapYoiewVA0seDovuC0YWCUznhkF0cClN2AzKPGDc4L_4gsqQ2FXZTYpCSrHnQjV4dAqOWZ0f40',
 'Kursi santai rangka mahoni solid dengan pelapis beludru hijau zamrud.','Pahatan Tangan Halus','85 x 80 x 95 cm',
 'Bersihkan dengan penyedot debu berujung sikat lembut.'),
('UP-1005','Lampu Gantung Ubud','Pencahayaan','Rotan',4500000.00,45,
 'https://lh3.googleusercontent.com/aida-public/AB6AXuDepGWzEfsgOPVaGH9CQ8tNstCV0ceqcZ9jDkEKyIhghel8Jyarv9a47rd13FCbtynJwJ-Z8JCQr7jm--ZxjyOOZsJdCkc1lwo3-cSXnE8T1w9JVS2tLJeQeb67mRLRPE6zy3Nvcbl0p0q4WJvmUhY5r57UlTAKrytJfj5RTZZc1GCAaWIHkEt8fYe8bEo8tBbqw8CsxZKpWe4VVgvXI1jxxWWAdLISubbFh1Kud1qWGU0vOfT8RqYf',
 'Lampu gantung rotan tenunan tangan dengan pola bayangan geometris.','Anyaman Tangan Tradisional','Diameter 50 cm, Tinggi 60 cm',
 'Bersihkan debu dengan kuas kering. Jauhi kelembaban tinggi.'),
('NC-1204','Kredensa Nusa','Ruang Tamu','Kayu Jati',58000000.00,8,
 'https://lh3.googleusercontent.com/aida-public/AB6AXuB1rgn-owRDBW8t_5Oj9TTEGhTk_gAriD-Irryis8aRWMxm-4V1bVDMM1Ft5gHhHhLCamf5bt1bTZSZfI4Ni546cDFK4yI9hY0Kx8msR_Qfeg4BrecMN_DTdcLArwPvofV94CDmbeKwig5h2TnOx8sBdYZNZg9AFAHnuSiORagSUn1EUwJTo4cSw9ET9qIgskOPrHLBe6DNhgBSnDc41Sr3bLdBkF8NBOYKh72eH6Kw-wNReiCDPP6A',
 'Kredensa kayu jati dengan pintu geser berbilah, gaya Japandi.','Mortise & Tenon','160 x 45 x 75 cm',
 'Lap dengan mikrofiber kering. Oleskan minyak kayu berkala.'),
('BT-4041','Meja Sisi Bumi','Ruang Tamu','Kayu Ash',6800000.00,15,
 'https://lh3.googleusercontent.com/aida-public/AB6AXuCDxUY9raKUGQLSBSgINjNBE6Bcq83r5BaY3Q1OemGpTqtD_9kJVU6LL38PKi_OJW_fKJUZ5RF9OIYc00rHV_-tDzhPLfmDrtOF-H-zoAaVJ2qoCaDo1zw5a1gkSbVlyL-lqR1OFURZiwtQTMrUkMzBT6_krgbKAMlpDT3v0kKKKEDGlXwV6C9MtDIC22joCdFtDsLzYdDGJUhc7t8vIZbXSfe3kn5q0leHsPESalKDZtYhI3zPO-ya',
 'Meja sisi bundar dengan kaki tripod, tekstur serat kayu ash alami.','Kaki Tripod Bubut Presisi','Diameter 45 cm, Tinggi 55 cm',
 'Gunakan tatakan gelas. Lap dengan kain lembab ringan.');
