
-- phpMyAdmin SQL Dump
-- version 5.x
-- Database: `hr4_db`

CREATE DATABASE IF NOT EXISTS `hr4_db`;
USE `hr4_db`;

-- Table structure for table `employees`
CREATE TABLE IF NOT EXISTS `employees` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `position` VARCHAR(100) NOT NULL,
  `salary` DECIMAL(10,2) NOT NULL,
  `status` ENUM('ACTIVE','ON LEAVE') NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for table `employees`
INSERT INTO `employees` (`first_name`, `last_name`, `email`, `phone`, `department`, `position`, `salary`, `status`) VALUES
('Juan', 'Dela Cruz', 'juan@example.com', '09171234567', 'Hotel Operations', 'Front Desk Manager', 25000.00, 'ACTIVE'),
('Maria', 'Santos', 'maria@example.com', '09981234567', 'Kitchen', 'Head Chef', 30000.00, 'ACTIVE'),
('Pedro', 'Reyes', 'pedro@example.com', '09221234567', 'Housekeeping', 'Housekeeping Supervisor', 20000.00, 'ON LEAVE');
