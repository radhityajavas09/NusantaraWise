CREATE DATABASE IF NOT EXISTS nusantarawise_db;
USE nusantarawise_db;

-- Tabel Destinasi
CREATE TABLE IF NOT EXISTS destinasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(500) NOT NULL,
    rating DECIMAL(2,1) DEFAULT NULL,
    harga VARCHAR(100) DEFAULT NULL,
    highlights TEXT DEFAULT NULL,
    tips TEXT DEFAULT NULL,
    maps VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Galeri
CREATE TABLE IF NOT EXISTS galeri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    src VARCHAR(500) NOT NULL,
    title VARCHAR(255) NOT NULL,
    loc VARCHAR(255) NOT NULL,
    size VARCHAR(20) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Users (untuk register & login user)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Reviews (komentar + rating oleh user)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinasi_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL,
    komentar TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destinasi_id) REFERENCES destinasi(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert akun admin default
-- PENTING: Jalankan fix_db.php via browser untuk set password admin123 dengan hash yang benar
-- Atau jalankan: php generate_hash.php lalu copy hashnya ke sini
-- Hash di bawah adalah untuk password "admin123" yang dihasilkan oleh PHP password_hash()
INSERT INTO users (nama, email, password, role) VALUES
('Administrator', 'admin@nusantarawise.id', '$2y$10$YourHashHere', 'admin')
ON DUPLICATE KEY UPDATE
  password = '$2y$10$YourHashHere',
  role = 'admin';
-- CATATAN: Jalankan fix_db.php di browser (http://localhost:8000/fix_db.php) untuk set password yang benar!

-- Insert Data Default Destinasi
INSERT INTO destinasi (nama, kategori, lokasi, deskripsi, gambar, rating, harga, highlights, tips, maps) VALUES
('Raja Ampat', 'Pulau', 'Papua Barat Daya', 'Raja Ampat adalah kepulauan yang terdiri dari lebih 1.500 pulau kecil nan cantik di ujung barat Pulau Papua. Dikenal sebagai surga bawah laut dunia dengan keanekaragaman hayati laut tertinggi di bumi. Spot diving dan snorkeling kelas dunia yang memukau para penyelam dari seluruh penjuru dunia.', '/images/Wai-Resort-Raja-Ampat.jpg', 4.9, 'Rp 500.000', 'Snorkeling Kelas Dunia,1.500+ Pulau,Biodiversitas Tertinggi,Spot Foto Epik', 'Kunjungi antara Oktober–April untuk cuaca terbaik. Bawa uang tunai karena ATM sangat terbatas. Hormati alam dengan tidak menyentuh terumbu karang.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1013476.8168785407!2d130.0!3d-1.0!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d57702edc0d8c01%3A0x13a2a282e4faba23!2sRaja%20Ampat!5e0!3m2!1sid!2sid!4v1710000000001'),
('Candi Borobudur', 'Budaya', 'Jawa Tengah', 'Borobudur adalah candi Buddha terbesar di dunia yang dibangun pada abad ke-8 dan ke-9 Masehi oleh Dinasti Syailendra. Merupakan Situs Warisan Dunia UNESCO yang memiliki 2.672 panel relief dan 504 arca Buddha. Pemandangan matahari terbit di balik stupanya adalah salah satu momen paling ikonik di Indonesia.', 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?w=800&q=80', 4.8, 'Rp 50.000', 'Warisan Dunia UNESCO,Candi Buddha Terbesar,Sunrise Spektakuler,Relief Bersejarah', 'Datang saat sunrise (05.30 WIB) untuk pemandangan terbaik. Pakai pakaian sopan dan sarung yang bisa disewa di pintu masuk. Hindari musim hujan (Nov–Feb).', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.1!2d110.2037!3d-7.6079!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7afca1413dde71%3A0x5ca7e26b5d082e78!2sBorobudur%20Temple!5e0!3m2!1sid!2sid!4v1710000000002'),
('Gunung Bromo', 'Gunung', 'Jawa Timur', 'Gunung Bromo adalah gunung berapi aktif yang berada di tengah Taman Nasional Bromo Tengger Semeru. Lautan pasir seluas 10 km² mengelilingi kaldera-nya menciptakan pemandangan yang dramatis. Pemandangan dari puncak Penanjakan saat fajar dengan latar belakang Gunung Semeru adalah salah satu yang paling indah di Indonesia.', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=800&q=80', 4.8, 'Rp 35.000', 'Gunung Berapi Aktif,Lautan Pasir Dramatis,Sunrise Fenomenal,Trek ke Kawah', 'Bawa jaket tebal karena suhu bisa 0°C di pagi hari. Naik jeep dari Cemoro Lawang. Lindungi kamera dari debu vulkanik. Kunjungi antara April–Oktober.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15803.4!2d112.9465!3d-7.9425!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd636a74e14a815%3A0xc50aba5697a05e2a!2sMount%20Bromo!5e0!3m2!1sid!2sid!4v1710000000003'),
('Pantai Pink Lombok', 'Pantai', 'Nusa Tenggara Barat', 'Pantai Pink atau Tangsi Beach adalah salah satu dari sedikit pantai berpasir merah muda di dunia. Warna unik pasirnya berasal dari campuran serpihan terumbu karang merah dengan pasir putih. Terletak di kawasan Taman Nasional Gunung Rinjani, pantai ini menawarkan air laut yang jernih dan snorkeling yang menakjubkan.', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80', 4.7, 'Rp 20.000', 'Pasir Merah Muda,Langka di Dunia,Air Laut Jernih,Snorkeling Seru', 'Bisa dicapai dengan perahu dari Kuta Lombok (2 jam). Bawa bekal makanan & minuman karena tidak ada warung. Kunjungi saat pagi hari sebelum ramai.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15809.8!2d116.5528!3d-8.8963!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcda6b1d69b9b87%3A0x6b4b5437add5ca6b!2sPink%20Beach%20Lombok!5e0!3m2!1sid!2sid!4v1710000000004'),
('Hutan Pinus Mangunan', 'Alam', 'Daerah Istimewa Yogyakarta', 'Hutan Pinus Mangunan adalah destinasi alam yang memadukan ketenangan hutan pinus dengan udara sejuk pegunungan Bantul. Pohon-pohon pinus yang menjulang tinggi menciptakan suasana magis dan instagramable. Tempat ideal untuk camping, piknik keluarga, dan foto-foto estetik di sela-sela deretan pohon pinus.', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&q=80', 4.5, 'Rp 5.000', 'Udara Sejuk & Segar,Spot Foto Viral,Cocok untuk Camping,Pemandangan Bukit', 'Buka 24 jam namun terbaik pagi hari (06.00–09.00). Bawa jaket tipis karena cukup dingin di pagi hari. Tersedia spot foto berbayar di dalam kawasan.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.8!2d110.3744!3d-7.8951!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a15a7b54f9f1f%3A0x9c5a1c8b45b0e234!2sMangunan%20Pine%20Forest!5e0!3m2!1sid!2sid!4v1710000000005'),
('Pulau Komodo', 'Pulau', 'Nusa Tenggara Timur', 'Pulau Komodo adalah rumah bagi Komodo (Varanus komodoensis), reptil terbesar di bumi yang hanya ada di Indonesia. Taman Nasional Komodo, yang merupakan Situs Warisan Dunia UNESCO, juga menawarkan pantai berpasir merah muda, perairan jernih untuk diving, dan bukit savana yang eksotis.', 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=800&q=80', 4.9, 'Rp 150.000', 'Habitat Komodo Asli,Warisan Dunia UNESCO,Diving & Snorkeling,Pantai Pink Terdekat', 'Wajib ditemani ranger resmi saat trekking. Jaga jarak minimal 5 meter dari Komodo. Bawa kamera underwater untuk foto bawah air yang indah.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127283.5!2d119.5!3d-8.58!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2da74fc57c23571f%3A0x5f4e9f2c7ae9b1a1!2sKomodo%20Island!5e0!3m2!1sid!2sid!4v1710000000006'),
('Danau Toba', 'Alam', 'Sumatera Utara', 'Danau Toba adalah danau vulkanik terbesar di dunia dan terdalam di Asia Tenggara, terbentuk dari letusan supervulkan sekitar 74.000 tahun lalu. Di tengah danau terdapat Pulau Samosir, pusat kebudayaan suku Batak dengan rumah adat, makam raja, dan tarian tradisional yang memukau.', 'https://images.unsplash.com/photo-1580619305218-8423a7ef79b5?w=800&q=80', 4.7, 'Rp 15.000', 'Danau Vulkanik Terbesar,Pulau Samosir,Budaya Batak,Pemandangan Pegunungan', 'Coba kuliner khas Batak yaitu arsik (ikan mas bumbu kuning). Sewa sepeda atau motor untuk keliling Pulau Samosir. Kunjungi Desa Tomok dan Museum Batak.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d254866.4!2d98.7667!3d2.6833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x302e4b0f2e08d5f3%3A0x3d8b1a8bb5f77a58!2sLake%20Toba!5e0!3m2!1sid!2sid!4v1710000000007'),
('Pantai Kuta Bali', 'Pantai', 'Bali', 'Pantai Kuta adalah ikon wisata Bali yang terkenal di seluruh dunia dengan ombaknya yang cocok untuk berselancar, pasir putih keemasan yang membentang panjang, dan pemandangan matahari terbenam yang menakjubkan. Sepanjang pantai dipenuhi surf school, warung, bar, dan toko souvenir yang menarik.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80', 4.6, 'Gratis', 'Surfing Terbaik,Sunset Spektakuler,Kehidupan Malam,Kuliner Pinggir Pantai', 'Hati-hati dengan pedagang dan jasa. Sewa papan surfing di sekitar pantai. Datang sore hari untuk menikmati sunset. Pakai sunscreen dan baju renang.', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.0!2d115.1627!3d-8.7184!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd2430bdb5a6d7f%3A0x1c1f5e0c7c3b73b!2sKuta%20Beach!5e0!3m2!1sid!2sid!4v1710000000008');

-- Insert Data Default Galeri
INSERT INTO galeri (src, title, loc, size) VALUES
('https://images.unsplash.com/photo-1559628376-f3fe5f782a2e?w=600&q=80', 'Raja Ampat', 'Papua Barat', 'tall'),
('https://images.unsplash.com/photo-1596402184320-417e7178b2cd?w=600&q=80', 'Borobudur', 'Jawa Tengah', ''),
('https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=600&q=80', 'Gunung Bromo', 'Jawa Timur', ''),
('https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1000&q=80', 'Pantai Kuta Bali', 'Bali', 'wide'),
('https://images.unsplash.com/photo-1580619305218-8423a7ef79b5?w=600&q=80', 'Danau Toba', 'Sumatera Utara', ''),
('https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=600&q=80', 'Pulau Komodo', 'NTT', 'tall'),
('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&q=80', 'Pantai Pink Lombok', 'NTB', ''),
('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600&q=80', 'Hutan Pinus Mangunan', 'Yogyakarta', ''),
('https://images.unsplash.com/photo-1506197603052-3cc9c3a201bd?w=600&q=80', 'Kebudayaan Bali', 'Bali', ''),
('https://images.unsplash.com/photo-1549989476-69a92fa57c36?w=600&q=80', 'Alam Nusantara', 'Indonesia', '');
