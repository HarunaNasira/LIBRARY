-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 10, 2025 at 02:43 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `shelf_code` varchar(100) DEFAULT NULL,
  `publication_year` int(11) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `description` text,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `available_quantity` int(11) NOT NULL DEFAULT '1',
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`book_id`),
  UNIQUE KEY `isbn` (`isbn`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `isbn`, `title`, `author`, `shelf_code`, `publication_year`, `genre`, `subject`, `description`, `quantity`, `available_quantity`, `cover_image`, `created_at`, `updated_at`) VALUES
(1, '9780141187761', 'To Kill a Mockingbird', 'Harper Lee', 'BF16', 1960, 'Fiction', 'Literature', 'A novel about racial inequality as seen through the eyes of a young girl in Alabama.', 5, 4, '1744191419_37449.jpg', '2025-04-07 16:50:54', '2025-04-10 09:57:38'),
(2, '9780141393049', '1984', 'George Orwell', 'AS78', 1949, 'Non-Fiction', 'Philosophy', 'A dystopian novel set in a totalitarian regime where independent thinking is a crime.', 3, 3, 'game_of_thrones.jpg', '2025-04-07 16:50:54', '2025-04-10 08:21:50'),
(3, '9780743273565', 'The Great Gatsby', 'F. Scott Fitzgerald', 'BF16', 1925, 'History', 'Other', 'A novel about the mysteriously wealthy Jay Gatsby and his love for Daisy Buchanan.', 4, 2, '', '2025-04-07 16:50:54', '2025-04-10 09:39:59'),
(12, '3e53435353353', 'May Fair Witches', 'Calum Stone', 'VG543', 1934, 'Thriller', 'Science', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 1, 0, '1744276414_the-mayfair-witches-series-3-book-bundle.jpg', '2025-04-10 09:13:34', '2025-04-10 14:36:20'),
(11, '34343434', 'Stochatics 4th Edition', 'dfdfd', 'xfd3434', 1987, 'Non-Fiction', 'Mathematics', 'The beginning of excellence in mathematics', 2, 2, '1744137936_human_score_per_lang.png', '2025-04-08 18:45:36', '2025-04-10 14:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `book_loans`
--

DROP TABLE IF EXISTS `book_loans`;
CREATE TABLE IF NOT EXISTS `book_loans` (
  `loan_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `borrow_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('pending','borrowed','returned','overdue') NOT NULL DEFAULT 'borrowed',
  PRIMARY KEY (`loan_id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `book_loans`
--

INSERT INTO `book_loans` (`loan_id`, `book_id`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`) VALUES
(1, 5, 2, '2025-04-07 17:24:16', '2025-04-21', '2025-04-08', 'returned'),
(3, 2, 2, '2025-04-09 08:51:06', '2025-04-11', NULL, 'borrowed'),
(4, 1, 2, '2025-04-10 06:56:16', '2025-04-24', '2025-04-10', 'returned'),
(5, 11, 2, '2025-04-09 23:00:00', '2025-04-24', '2025-04-10', 'returned'),
(6, 1, 7, '2025-04-09 23:00:00', '2025-04-24', NULL, 'borrowed'),
(7, 12, 2, '2025-04-09 23:00:00', '2025-04-24', NULL, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `profile_pic` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email_reminder` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `role`, `profile_pic`, `created_at`, `updated_at`, `email_reminder`) VALUES
(1, 'admin', '$argon2i$v=19$m=65536,t=4,p=1$VjJDd3E0dGdzM3M1YkNIUw$vPHPV+VSRfS5sQhcyydD/QwfYLTGFk65zAnMpdzJgtY', 'King Admin', 'admin@library.com', 'admin', NULL, '2025-04-07 16:50:54', '2025-04-10 06:33:19', 0),
(2, 'nasira', '$2y$10$yFzlZS1vzbccRSR.0Wt5WuNRCWwGEOdqUQ9pnlBX7d5eoqGYlmBZ2', 'Asantewaa Haruna', 'mirekuprince66@gmail.com', 'user', 'profile_2.png', '2025-04-07 16:50:54', '2025-04-10 12:57:59', 1),
(3, 'Folashade', '$argon2i$v=19$m=65536,t=4,p=1$TGNLL1JmVU1jbFowVXd1ZQ$cMpb7z2yQKjTWv7+nstyXjKZjCmK5SNKTdlV7pLcQUY', 'Folashe Nadin', 'folashe@gmail.com', 'user', NULL, '2025-04-07 17:35:00', '2025-04-10 06:22:16', 0),
(6, 'Someone', '$argon2i$v=19$m=65536,t=4,p=1$OGFod0xIRHlVcGdvRjlSWQ$Ix1bYWtvnTxueX03KNSTLRsLTvLXfqEuuQdwX0LDY6c', 'Someone I know', 'someone@gmail.com', 'user', NULL, '2025-04-10 06:30:49', '2025-04-10 06:30:49', 0),
(5, 'michael', '$argon2i$v=19$m=65536,t=4,p=1$V0xQTTVodDM5UC9idENrNg$J2OixbI/NvNwG8ZNSN5yl2ejegawoM7ov6IjxLzxHlk', 'Somwah', 'chicken@gmail.com', 'admin', NULL, '2025-04-09 21:31:28', '2025-04-10 06:35:25', 0),
(7, 'prince', '$argon2i$v=19$m=65536,t=4,p=1$ZjBGSzhrUnpQOGJpeHR6Ug$tBZlxryM39ToBMazkZlsLQw9AoOn799ljpNX1BwyyBo', 'Prince Azunre', 'prince@gmail.com', 'user', NULL, '2025-04-10 09:34:51', '2025-04-10 11:44:33', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
