-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2026 at 04:12 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thesismatch`
--
CREATE DATABASE IF NOT EXISTS `thesismatch` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_slovenian_ci;
USE `thesismatch`;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovenian_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `student_id`, `topic_id`, `status`, `created_at`) VALUES
(1, 4, 1, 'pending', '2026-05-31 11:00:56'),
(2, 4, 2, 'accepted', '2026-05-31 11:00:56'),
(3, 4, 5, 'rejected', '2026-05-31 11:00:56'),
(4, 5, 1, 'pending', '2026-05-31 11:01:59'),
(5, 5, 3, 'accepted', '2026-05-31 11:01:59'),
(6, 5, 6, 'pending', '2026-05-31 11:01:59'),
(7, 6, 1, 'rejected', '2026-05-31 11:01:59'),
(8, 6, 4, 'pending', '2026-05-31 11:01:59'),
(9, 6, 7, 'pending', '2026-05-31 11:01:59'),
(10, 9, 2, 'pending', '2026-05-31 11:01:59'),
(11, 9, 5, 'accepted', '2026-05-31 11:01:59'),
(12, 9, 8, 'pending', '2026-05-31 11:01:59'),
(13, 9, 8, 'accepted', '2026-05-31 11:02:36');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovenian_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `text`, `user_id`, `topic_id`, `created_at`) VALUES
(1, 'Ali je potrebno predhodno znanje iz kibernetske varnosti?', 4, 1, '2026-05-31 11:00:56'),
(2, 'ML znanje je dovolj, varnost se naučiš sproti.', 2, 1, '2026-05-31 11:00:56'),
(3, 'Kakšen je okvirni obseg naloge?', 4, 2, '2026-05-31 11:00:56'),
(4, 'Tema pokriva spletne in mobilne aplikacije.', 2, 2, '2026-05-31 11:00:56'),
(5, 'Katere podatkovne množice bomo uporabljali?', 4, 6, '2026-05-31 11:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovenian_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(1, 'AI/ML'),
(2, 'Splet'),
(3, 'Varnost'),
(4, 'Podatki'),
(5, 'HCI'),
(6, 'Drugo');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `required_skills` varchar(200) DEFAULT NULL,
  `status` enum('open','taken') NOT NULL DEFAULT 'open',
  `professor_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovenian_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `title`, `description`, `required_skills`, `status`, `professor_id`, `subject_id`, `created_at`) VALUES
(1, 'Zaznavanje anomalij v IoT omrežjih z ML', 'Razvoj sistema za zaznavanje nenavadnega obnašanja v IoT omrežjih z uporabo algoritmov strojnega učenja. Sistem bo moral v realnem času analizirati promet in zaznavati potencialne varnostne grožnje.', 'Python, TensorFlow, osnove omrežij', 'open', 2, 1, '2026-05-31 11:00:56'),
(2, 'Spletna aplikacija za upravljanje urnikov', 'Razvoj moderne spletne aplikacije za avtomatsko generiranje urnikov z upoštevanjem omejitev in preferenc uporabnikov.', 'PHP, MySQL, JavaScript', 'open', 2, 2, '2026-05-31 11:00:56'),
(3, 'Razvoj chatbota za študentsko podporo', 'Implementacija inteligentnega chatbota ki pomaga študentom pri iskanju informacij o predmetih in urnikih.', 'Python, NLP, REST API', 'open', 2, 1, '2026-05-31 11:00:56'),
(4, 'Dostopnost mobilnih aplikacij za slepe', 'Evalvacija in izboljšava dostopnosti mobilnih aplikacij za uporabnike z okvarami vida po standardih WCAG 2.1.', 'Android/iOS, UX research', 'open', 2, 5, '2026-05-31 11:00:56'),
(5, 'Analiza ranljivosti v OAuth 2.0 implementacijah', 'Pregled in analiza pogostih varnostnih pomanjkljivosti v implementacijah OAuth 2.0 protokola ter predlog izboljšav.', 'Networking, Security basics, Python', 'open', 3, 3, '2026-05-31 11:00:56'),
(6, 'Vizualizacija velikih podatkovnih množic', 'Razvoj orodij za interaktivno vizualizacijo in analizo podatkovnih množic z milijoni zapisov v realnem času.', 'D3.js, Python, SQL', 'open', 3, 4, '2026-05-31 11:00:56'),
(7, 'Blockchain tehnologija v izobraževanju', 'Analiza možnosti uporabe blockchain tehnologije za verifikacijo diplom in certifikatov v izobraževalnih ustanovah.', 'Blockchain, Solidity, osnove kriptografije', 'open', 3, 6, '2026-05-31 11:00:56'),
(8, 'Primerjalna analiza LLM modelov za generiranje kode', 'Primerjalna analiza velikih jezikovnih modelov za generiranje in popravljanje programske kode ter evalvacija njihove učinkovitosti.', 'Python, NLP, Git', 'taken', 3, 1, '2026-05-31 11:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','profesor','admin') NOT NULL DEFAULT 'student',
  `confirmed` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovenian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `confirmed`, `created_at`) VALUES
(1, 'Admin', 'admin@thesis.si', '$2y$10$rpkQfzpPM6utdZ6i6iAgquaDjzXhy.OeQTLSAPZ1OBDZx7klS1PYe', 'admin', 1, '2026-05-31 10:42:32'),
(2, 'David Jelenc', 'david.jelenc@thesis.si', '$2y$10$J0VKjaqoPPojSfn8EzG5jO2SHYOzGMOgptyh82opVctJesHQjVbLe', 'profesor', 1, '2026-05-31 10:45:32'),
(3, 'Aleš Smrdel', 'ales.smrdel@thesis.si', '$2y$10$YxAAtKQSFKtrc5Ntx3acW.dPAL4XBg4qbEqvjPCvruUi..T0C4Gz2', 'profesor', 1, '2026-05-31 10:48:10'),
(4, 'Anel Šakanović', 'anel.sakanovic@thesis.si', '$2y$10$FGP.wkFcm65IcCWupyQxTedgCRk9eff4FjfOjG2s7WXekfjm8gvN.', 'student', 1, '2026-05-31 10:48:46'),
(5, 'Janez Novak', 'janez.novak@thesis.si', '$2y$10$udExUOgFFlvtscTnV47kDuuwBTK8orOMLoZvy3ShfgOn/37XmSYwq', 'student', 1, '2026-05-31 10:49:27'),
(6, 'Ana Novak', 'ana.novak@thesis.si', '$2y$10$SEFdsIjkWd3rA2Odf8Ppbe5E3jbX8fIUEb2DACRxjSeqbjKkNkP2.', 'student', 1, '2026-05-31 10:49:53'),
(7, 'Profesor X', 'profesor.x@thesis.si', '$2y$10$S.Felsgj/oLs47R4PhoBYutFQoWoAjqS0AdlMqOmRtNP6W.1oxl3q', 'profesor', 1, '2026-05-31 10:52:11'),
(8, 'Profesor Y', 'profesor.y@thesis.si', '$2y$10$.isLYtRb7vQ6kAjHmYg1nOoFyBMtoEzB3xqZYsqYgNniJy9jD1pqi', 'profesor', 1, '2026-05-31 10:52:43'),
(9, 'Cristiano Ronaldo', 'cr7@thesis.si', '$2y$10$OKEHvHd7cpscNv.FtgdS.uB71eIo2Wg2RkdxMXq4r7wlOx9Ni7SSq', 'student', 1, '2026-05-31 10:54:07'),
(10, 'Čakajoči Profesor', 'profesor.z@thesis.si', '$2y$10$iTeeleEzOHkAPv67ZPdW0uyuhDYHpvy3Hl5VBAIFnpj5WN03RJBnC', 'profesor', 0, '2026-05-31 10:55:37'),
(11, 'Še En Čakajoči Profesor', 'profesor.w@thesis.si', '$2y$10$SwQs8LckE6zMEleSmnyMx.p2aOB7qSrTkjuYeENVe1pw9e3oAgLj2', 'profesor', 0, '2026-05-31 10:57:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `topics_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
