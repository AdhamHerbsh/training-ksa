-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for training
CREATE DATABASE IF NOT EXISTS `training` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `training`;

-- Dumping structure for table training.clearance_forms
CREATE TABLE IF NOT EXISTS `clearance_forms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id_iqama_number` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nationality` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `country_code` varchar(5) COLLATE utf8mb4_general_ci DEFAULT '+966',
  `mobile_number` varchar(9) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `submission_date` date NOT NULL,
  `work_card_expiry_date` date NOT NULL,
  `has_housing` enum('Yes','No') COLLATE utf8mb4_general_ci NOT NULL,
  `id_photo_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `work_card_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `administration_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_iqama_number` (`id_iqama_number`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `clearance_forms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table training.health_centers
CREATE TABLE IF NOT EXISTS `health_centers` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `center_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `governorate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `region_cluster` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=389 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table training.hospitals
CREATE TABLE IF NOT EXISTS `hospitals` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `hospital_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `governorate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `region_cluster` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table training.intern_survey
CREATE TABLE IF NOT EXISTS `intern_survey` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `q1` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `q2` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `q3` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `problems` text COLLATE utf8mb4_general_ci,
  `suggestions` text COLLATE utf8mb4_general_ci,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `intern_survey_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table training.trainees
CREATE TABLE IF NOT EXISTS `trainees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `ar_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `en_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `age` int NOT NULL,
  `country_code` varchar(5) COLLATE utf8mb4_general_ci DEFAULT '+966',
  `mobile_number` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_general_ci NOT NULL,
  `university` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `uni_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `major` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `degree` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `supervisor_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `supervisor_email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `center` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `training_letter_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Pending','Reviewed','Accepted','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `national_id` (`national_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table training.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `national_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country_code` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(9) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_type` enum('Supervisor','Trainee') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `national_id` (`national_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
