-- Database setup for Tristan Barber Application
-- Drop the database if it exists (useful for clean setup, remove in production)
DROP DATABASE IF EXISTS `tristras`;
-- Create the database
CREATE DATABASE `tristras` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tristras`;
-- 1. Erabiltzaileen Taula (Users)
-- Rolak: 'client', 'barber', 'admin'
CREATE TABLE `erabiltzaileak` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `izena` VARCHAR(100) NOT NULL,
    `posta` VARCHAR(100) NOT NULL UNIQUE,
    `pasahitza` VARCHAR(255) NOT NULL,
    `telefonoa` VARCHAR(20) DEFAULT NULL,
    `rola` ENUM('client', 'barber', 'admin') DEFAULT 'client',
    `irudia` VARCHAR(255) DEFAULT NULL,
    `sortua` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- 2. Zerbitzuen Taula (Services)
CREATE TABLE `zerbitzuak` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `izena` VARCHAR(100) NOT NULL,
    `deskribapena` TEXT,
    `iraupena` INT NOT NULL,
    `prezioa` DECIMAL(10, 2) NOT NULL,
    `irudia` VARCHAR(255) DEFAULT NULL,
    `sortua` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- 3. Hitzorduen Taula (Appointments)
-- Egoerak: 'pending', 'confirmed', 'completed', 'cancelled'
CREATE TABLE `hitzorduak` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bezero_id` INT NOT NULL,
    `langile_id` INT DEFAULT NULL,
    `zerbitzu_id` INT NOT NULL,
    `data` DATE NOT NULL,
    `hasiera` TIME NOT NULL,
    `amaiera` TIME NOT NULL,
    `egoera` ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    `prezioa` DECIMAL(10, 2) NOT NULL,
    `sortua` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`bezero_id`) REFERENCES `erabiltzaileak`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`langile_id`) REFERENCES `erabiltzaileak`(`id`) ON DELETE
    SET NULL,
        FOREIGN KEY (`zerbitzu_id`) REFERENCES `zerbitzuak`(`id`) ON DELETE RESTRICT
);
-- User Permissions Setup
-- Note: Requires running this script as root to grant these privileges
-- Create the general app user
DROP USER IF EXISTS 'tristras_user' @'localhost';
CREATE USER 'tristras_user' @'localhost' IDENTIFIED BY '1MG32025';
GRANT SELECT,
    INSERT,
    UPDATE,
    DELETE ON `tristras`.* TO 'tristras_user' @'localhost';
-- Also ensure 'root' can access it with '1MG32025' if they use that locally as requested
-- We'll assume root already exists, just forcefully setting the password. Use with caution in real envs.
-- ALTER USER 'root'@'localhost' IDENTIFIED BY '1MG32025'; 
-- GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
-- Hasierako datuak txertatu (Zerbitzuak eta Erabiltzaileak)
-- Pasahitzak 'pasahitza' moduan ezarri dira (bcrypt: $2y$10$f.8M8Bw3gM1.q7zF6.s1i.nF2t0oZ.mZp2nQ3qI/D.8g/3k6bLxB6 '123456' da)
SET @common_password_hash = '$2y$10$f.8M8Bw3gM1.q7zF6.s1i.nF2t0oZ.mZp2nQ3qI/D.8g/3k6bLxB6';
-- Add test Barbers
INSERT INTO `erabiltzaileak` (
        `izena`,
        `posta`,
        `pasahitza`,
        `telefonoa`,
        `rola`,
        `irudia`
    )
VALUES (
        'Jon Barberoa',
        'jon@tristras.eus',
        @common_password_hash,
        '600111222',
        'barber',
        'irudiak/barbers/jon.png'
    ),
    (
        'Mikel Barberoa',
        'mikel@tristras.eus',
        @common_password_hash,
        '600333444',
        'barber',
        'irudiak/barbers/mikel.png'
    ),
    (
        'Ander Barberoa',
        'ander@tristras.eus',
        @common_password_hash,
        '600555666',
        'barber',
        'irudiak/barbers/ander.png'
    );
-- Bezeroa gehitu (Client)
INSERT INTO `erabiltzaileak` (
        `izena`,
        `posta`,
        `pasahitza`,
        `telefonoa`,
        `rola`,
        `irudia`
    )
VALUES (
        'Bezero Froga',
        'bezeroa@proba.eus',
        @common_password_hash,
        '699888777',
        'client',
        'irudiak/bezero_proba.png'
    );
-- Admina gehitu (Admin)
INSERT INTO `erabiltzaileak` (
        `izena`,
        `posta`,
        `pasahitza`,
        `telefonoa`,
        `rola`
    )
VALUES (
        'Admin Nagusia',
        'admin@tristras.eus',
        @common_password_hash,
        '600000000',
        'admin'
    );
-- Zerbitzuak gehitu (Services)
INSERT INTO `zerbitzuak` (
        `izena`,
        `deskribapena`,
        `iraupena`,
        `prezioa`,
        `irudia`
    )
VALUES (
        'Ilea moztu',
        'Moztu klasikoa, guraizeekin eta makinarekin.',
        30,
        20.00,
        'irudiak/services/ilea.png'
    ),
    (
        'Bizarra',
        'Bizarraren forma ematea eta profilatzea.',
        20,
        15.00,
        'irudiak/services/bizarra.png'
    ),
    (
        'Konboa',
        'Ilea moztu eta bizarra txukundu zerbitzu osoa.',
        45,
        30.00,
        'irudiak/services/konboa.png'
    );
-- Probetarako hitzordu batzuk gehitu
-- Bezero Froga (ID 4) books with Jon (ID 1)
INSERT INTO `hitzorduak` (
        `bezero_id`,
        `langile_id`,
        `zerbitzu_id`,
        `data`,
        `hasiera`,
        `amaiera`,
        `egoera`,
        `prezioa`
    )
VALUES (
        4,
        1,
        1,
        CURDATE(),
        '10:30:00',
        '11:15:00',
        'completed',
        20.00
    ),
    (
        4,
        2,
        2,
        CURDATE(),
        '11:15:00',
        '11:35:00',
        'confirmed',
        15.00
    ),
    (
        4,
        3,
        3,
        CURDATE(),
        '12:00:00',
        '12:45:00',
        'confirmed',
        30.00
    );
