-- E-Recruitment Portal Database Schema
-- Run this SQL in your PHPMyAdmin or MySQL terminal to initialize the database

CREATE DATABASE IF NOT EXISTS `erecruitment_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `erecruitment_db`;

-- 1. Users Table (Base table for Candidates, Employers, and Admins)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('candidate', 'employer', 'admin') NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Companies/Employers Profiles Table
CREATE TABLE IF NOT EXISTS `companies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `company_name` VARCHAR(150) NOT NULL,
    `website` VARCHAR(150),
    `industry` VARCHAR(100),
    `company_size` VARCHAR(50),
    `description` TEXT,
    `logo` VARCHAR(255),
    `address` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Candidates Profiles Table
CREATE TABLE IF NOT EXISTS `candidates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `phone` VARCHAR(20),
    `skills` TEXT, -- Comma-separated or serialized skills
    `experience` TEXT,
    `education` TEXT,
    `resume_path` VARCHAR(255),
    `profile_pic` VARCHAR(255),
    `bio` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Job Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'fa-briefcase',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. Jobs Vacancy Table
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT NOT NULL,
    `category_id` INT,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `requirements` TEXT,
    `location` VARCHAR(100) NOT NULL,
    `salary` VARCHAR(100),
    `job_type` ENUM('Full-time', 'Part-time', 'Contract', 'Remote', 'Internship') NOT NULL,
    `status` ENUM('active', 'inactive', 'closed') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expiry_date` DATE,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. Job Applications Table
CREATE TABLE IF NOT EXISTS `applications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `job_id` INT NOT NULL,
    `candidate_id` INT NOT NULL,
    `resume_path` VARCHAR(255) NOT NULL, -- Keep path of resume sent at the time of application
    `cover_letter` TEXT,
    `status` ENUM('pending', 'under_review', 'accepted', 'rejected') DEFAULT 'pending',
    `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
-- 7. Resumes Table
CREATE TABLE IF NOT EXISTS `resumes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `candidate_id` INT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `extracted_text` LONGTEXT,
    `parsed_json` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `resume_skills` (
    `resume_id` INT NOT NULL,
    `skill_id` INT NOT NULL,
    PRIMARY KEY (`resume_id`, `skill_id`),
    FOREIGN KEY (`resume_id`) REFERENCES `resumes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`skill_id`) REFERENCES `skills`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Skills Table
CREATE TABLE IF NOT EXISTS `skills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 9. Candidate Skills Linking Table
CREATE TABLE IF NOT EXISTS `candidate_skills` (
    `candidate_id` INT NOT NULL,
    `skill_id` INT NOT NULL,
    PRIMARY KEY (`candidate_id`, `skill_id`),
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`skill_id`) REFERENCES `skills`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert Seed Data (Categories)
INSERT INTO `categories` (`name`, `icon`) VALUES 
('Software Development', 'fa-code'),
('Design & Creative', 'fa-palette'),
('Marketing & Sales', 'fa-chart-line'),
('Finance & Accounting', 'fa-calculator'),
('Human Resources', 'fa-users-gear'),
('Customer Support', 'fa-headset')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Insert Seed Users
-- Password is 'password123' for all seeded accounts
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`) VALUES
(1, 'System Administrator', 'admin@erecruit.com', '$2y$12$YyHAyXVcWrdCoUyfEQARIu3xzZADnkUFeRWcQmz6yShAs2FJdty.e', 'admin', 'active'),
(2, 'TechSphere Solutions', 'employer@erecruit.com', '$2y$12$YyHAyXVcWrdCoUyfEQARIu3xzZADnkUFeRWcQmz6yShAs2FJdty.e', 'employer', 'active'),
(3, 'John Doe', 'candidate@erecruit.com', '$2y$12$YyHAyXVcWrdCoUyfEQARIu3xzZADnkUFeRWcQmz6yShAs2FJdty.e', 'candidate', 'active')
ON DUPLICATE KEY UPDATE `email`=`email`;

-- Link profile tables for seeded accounts
INSERT INTO `companies` (`id`, `user_id`, `company_name`, `website`, `industry`, `company_size`, `description`, `address`) VALUES
(1, 2, 'TechSphere Solutions', 'https://techsphere.com', 'Software Development', '50-200 employees', 'Leading web development company.', 'Kathmandu, Nepal')
ON DUPLICATE KEY UPDATE `user_id`=`user_id`;

INSERT INTO `candidates` (`id`, `user_id`, `phone`, `skills`, `experience`, `education`, `bio`) VALUES
(1, 3, '9876543210', 'PHP, HTML, CSS, JavaScript, Bootstrap', '2 years web developer', 'BCA Graduate', 'Aspiring web developer looking for PHP/MySQL opportunities.')
ON DUPLICATE KEY UPDATE `user_id`=`user_id`;

