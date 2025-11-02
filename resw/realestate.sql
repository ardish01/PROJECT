-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2017 at 06:47 AM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `realestate`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Insert admin users first
--

INSERT INTO `users` (
  `username`,
  `email`,
  `password`,
  `full_name`,
  `phone`,
  `is_admin`
) VALUES
('admin', 'admin@jaggamandu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '1234567890', 1),
('admin2', 'admin2@jaggamandu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator2', '1234567890', 1);

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `agent_id` int(10) NOT NULL AUTO_INCREMENT,
  `agent_name` varchar(150) NOT NULL,
  `agent_address` varchar(250) NOT NULL,
  `agent_contact` varchar(20) NOT NULL,
  `agent_email` varchar(25) NOT NULL,
  PRIMARY KEY (`agent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`agent_id`, `agent_name`, `agent_address`, `agent_contact`, `agent_email`) VALUES
(1, 'Ronas Pahiju', 'Byasi Bhaktapur', '987654321', 'ronas@gmail.com'),
(2, 'Krish Karmacharya', 'Balakhu Bhaktapur', '123456789', 'krish@gmail.com'),
(3, 'Aardish Duwal', 'Dekocha Bhaktapur', '02 4728 5284', 'aardish@gmail.com');

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(10) NOT NULL AUTO_INCREMENT,
  `property_title` varchar(150) NOT NULL,
  `property_details` text,
  `price` float NOT NULL,
  `property_address` varchar(200) NOT NULL,
  `property_img` text,
  `floor_space` varchar(20) NOT NULL,
  `agent_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  PRIMARY KEY (`property_id`),
  KEY `agent_id` (`agent_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_property_agent` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`agent_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_property_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `properties` (after users and agents are created)
--

INSERT INTO `properties` (`property_id`, `property_title`, `property_details`, `price`, `property_address`, `property_img`, `floor_space`, `agent_id`, `user_id`) VALUES
(2, 'Apartment For Rent', 'Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables for real-time schemas. Dramatically maintain clicks-and-mortar solutions without functional solutions.\r\n\r\nCompletely synergize resource sucking relationships via premier niche markets. Professionally cultivate one-to-one customer service with robust ideas. Dynamically innovate resource-leveling customer service for state of the art customer service', 7000, 'Dekocha Bhaktapur', 'images/properties/bed-2-1.jpg,images/properties/bed-2-2.jpg,images/properties/liv-2-1.jpg,images/properties/liv-2-2.jpg,images/properties/kitchen-2-1.jpg', '1650 X 1350', 3, 1),
(3, 'Apartment For Sale', 'Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables for real-time schemas. Dramatically maintain clicks-and-mortar solutions without functional solutions.\r\n\r\nCompletely synergize resource sucking relationships via premier niche markets. Professionally cultivate one-to-one customer service with robust ideas. Dynamically innovate resource-leveling customer service for state of the art customer service', 80000, 'KamalBinayak Bhaktapur', 'images/properties/bed-3-1.jpg,images/properties/bed-3-2.jpg,images/properties/liv-3-1.jpg,images/properties/liv-3-2.jpg,images/properties/kitchen-3-1.jpg', '1500 X 1300', 3, 1),
(4, 'Office-Space for Sale', 'Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables for real-time schemas. Dramatically maintain clicks-and-mortar solutions without functional solutions.\r\n\r\nCompletely synergize resource sucking relationships via premier niche markets. Professionally cultivate one-to-one customer service with robust ideas. Dynamically innovate resource-leveling customer service for state of the art customer service', 100000, 'Chyamasignh Bhaktapur', 'images/properties/bed-4-1.jpg,images/properties/bed-4-2.jpg,images/properties/liv-4-1.jpg,images/properties/liv-4-2.jpg,images/properties/kitchen-4-1.jpg', '1650 X 1350', 2, 1);

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` boolean DEFAULT FALSE,
  `user_id` int(10) NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_message_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `bookings`
--

CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` int(10) NOT NULL AUTO_INCREMENT,
  `property_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text,
  PRIMARY KEY (`booking_id`),
  KEY `property_id` (`property_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_booking_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;  
  
  
  
  
