-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th12 25, 2025 lúc 01:26 PM
-- Phiên bản máy phục vụ: 10.4.13-MariaDB
-- Phiên bản PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `booksaw_db`
--
CREATE DATABASE IF NOT EXISTS `booksaw_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `booksaw_db`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `authors`
--

DROP TABLE IF EXISTS `authors`;
CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `authors`
--

INSERT INTO `authors` (`id`, `name`, `bio`, `avatar`, `created_at`) VALUES
(1, 'Armor Ramsey', 'Tác giả nổi tiếng với các cuốn sách về lối sống tối giản và phiêu lưu.', NULL, '2025-12-01 08:50:51'),
(2, 'Sanchit Howdy', 'Nhà văn chuyên viết về du lịch và khám phá sa mạc.', NULL, '2025-12-01 08:50:51'),
(3, 'Arthur Doyle', 'Tác giả tiểu thuyết kinh điển.', NULL, '2025-12-01 08:50:51'),
(4, 'Klien Marry', 'Chuyên gia về truyện cổ tích hiện đại.', NULL, '2025-12-01 08:50:51'),
(5, 'Timbur Hood', 'Tác giả bestseller về hạnh phúc và chim chóc.', NULL, '2025-12-01 08:50:51'),
(6, 'Adam Silber', 'Nhiếp ảnh gia chuyên nghiệp.', NULL, '2025-12-01 08:50:51'),
(7, 'Bratt Smith', 'Chuyên gia về lối sống đơn giản.', NULL, '2025-12-01 08:50:51'),
(8, 'Nicole Wilson', 'Nhà văn nữ quyền và cảm xúc.', NULL, '2025-12-01 08:50:51'),
(9, 'Marmik Lama', 'Tác giả sách tâm linh và thiền định.', NULL, '2025-12-01 08:50:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `publication_year` year(4) DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `bestseller` tinyint(1) DEFAULT 0,
  `on_sale` tinyint(1) DEFAULT 0,
  `stock` int(11) DEFAULT 99,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`) USING HASH,
  KEY `author_id` (`author_id`),
  KEY `idx_featured` (`featured`),
  KEY `idx_bestseller` (`bestseller`),
  KEY `idx_sale` (`on_sale`),
  KEY `idx_category` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `books`
--

INSERT INTO `books` (`id`, `title`, `slug`, `author_id`, `category_id`, `description`, `price`, `old_price`, `isbn`, `pages`, `publication_year`, `cover_image`, `featured`, `bestseller`, `on_sale`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'Simple way of piece life', 'simple-way-of-piece-life', 1, 6, 'Cuốn sách giúp bạn tìm thấy sự bình yên trong cuộc sống hiện đại.', '40.00', '50.00', NULL, NULL, NULL, 'product-item1', 1, 0, 1, 99, '2025-12-01 08:50:51', '2025-12-01 09:09:07'),
(2, 'Great travel at desert', 'great-travel-at-desert', 2, 4, 'Hành trình kỳ thú qua sa mạc Sahara.', '38.00', '30.00', NULL, NULL, NULL, 'product-item2', 1, 0, 1, 99, '2025-12-01 08:50:51', '2025-12-01 09:55:24'),
(3, 'The lady beauty Scarlett', 'the-lady-beauty-scarlett', 3, 3, 'Câu chuyện tình yêu bất hủ.', '45.00', '35.00', NULL, NULL, NULL, 'product-item3', 1, 0, 1, 99, '2025-12-01 08:50:51', '2025-12-01 09:55:28'),
(4, 'Once upon a time', 'once-upon-a-time', 4, 5, 'Cổ tích dành cho người lớn.', '35.00', '25.00', NULL, NULL, NULL, 'product-item4', 1, 0, 1, 99, '2025-12-01 08:50:51', '2025-12-01 09:55:31'),
(5, 'Birds gonna be happy', 'birds-gonna-be-happy', 5, 8, 'Cuốn sách bán chạy nhất về hạnh phúc.', '45.00', NULL, NULL, NULL, NULL, 'single-image', 1, 1, 0, 99, '2025-12-01 08:50:51', '2025-12-01 09:55:35'),
(6, 'Portrait photography', 'portrait-photography', 6, 7, 'Học chụp chân dung chuyên nghiệp.', '40.00', NULL, NULL, NULL, NULL, 'tab-item1', 1, 0, 0, 99, '2025-12-01 08:50:51', '2025-12-01 09:55:39'),
(7, 'Tips of simple lifestyle', 'tips-of-simple-lifestyle', 7, 6, '100 mẹo sống tối giản hiệu quả.', '40.00', NULL, NULL, NULL, NULL, 'tab-item3', 1, 0, 0, 99, '2025-12-01 08:50:51', '2025-12-01 09:55:44'),
(8, 'Peaceful Enlightment', 'peaceful-enlightment', 9, 8, 'Con đường đến giác ngộ.', '40.00', NULL, NULL, NULL, NULL, 'tab-item5', 1, 0, 0, 99, '2025-12-01 08:50:51', '2025-12-01 09:55:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Business', 'business', 'Sách kinh doanh, khởi nghiệp, quản trị', '2025-12-01 08:50:50'),
(2, 'Technology', 'technology', 'Công nghệ, lập trình, AI', '2025-12-01 08:50:50'),
(3, 'Romantic', 'romantic', 'Tiểu thuyết lãng mạn', '2025-12-01 08:50:50'),
(4, 'Adventure', 'adventure', 'Phiêu lưu, khám phá', '2025-12-01 08:50:50'),
(5, 'Fictional', 'fictional', 'Tiểu thuyết hư cấu', '2025-12-01 08:50:50'),
(6, 'Lifestyle', 'lifestyle', 'Phong cách sống', '2025-12-01 08:50:50'),
(7, 'Photography', 'photography', 'Nhiếp ảnh', '2025-12-01 08:50:50'),
(8, 'Inspiration', 'inspiration', 'Truyền cảm hứng', '2025-12-01 08:50:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `password_hash`, `created_at`) VALUES
(1, 'aa', 'aa@gmail.com', NULL, NULL, '$2y$10$kD0aQxfqF2ag64xNWrGfzOTZpqztwS5JyE61AmbHIXm3f5GSnPIFC', '2025-12-25 13:17:33'),
(2, 'aaaaa', 'bb@gmail.com', NULL, NULL, '$2y$10$21QWC8qd60kewg4UEWFYA.0f3Vpw0qSwA1ClJcjtcpa7H.MQ7tAH.', '2025-12-25 13:18:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Admin',
  `published_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `author_name`, `published_at`, `created_at`) VALUES
(1, 'Reading books always makes the moments happy', 'reading-books-makes-happy', NULL, 'Đọc sách không chỉ mang lại kiến thức mà còn là niềm vui bất tận...', 'post-img1.jpg', 'Admin', '2021-03-30', '2025-12-01 08:50:51'),
(2, '10 cuốn sách thay đổi cuộc đời tôi', '10-books-changed-my-life', NULL, 'Danh sách những cuốn sách đã định hình tư duy của tôi...', 'post-img2.jpg', 'Admin', '2021-03-29', '2025-12-01 08:50:51'),
(3, 'Tại sao nên đọc sách giấy thay vì ebook?', 'paper-vs-ebook', NULL, 'Dùy chọn nào tốt hơn cho sức khỏe và trải nghiệm đọc?', 'post-img3.jpg', 'Admin', '2021-02-27', '2025-12-01 08:50:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Super Admin', 'admin@booksaw.com', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'admin', '2025-12-25 13:25:27');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
