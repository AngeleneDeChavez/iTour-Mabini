-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2024 at 01:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capstonedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(150) NOT NULL,
  `activity_name` varchar(222) NOT NULL,
  `price` int(100) NOT NULL,
  `resort_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `activity_name`, `price`, `resort_id`) VALUES
(1, 'Hiking', 990, 2),
(4, 'Trike', 532, 1),
(5, 'Banana Boat', 456, 1),
(8, 'Hiking', 990, 1),
(10, 'Swimming', 990, 1),
(11, 'Playpool', 532, 1);

-- --------------------------------------------------------

--
-- Table structure for table `added_activities`
--

CREATE TABLE `added_activities` (
  `id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `resort_id` int(11) NOT NULL,
  `date_added` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(150) NOT NULL,
  `username` varchar(222) NOT NULL,
  `password` varchar(222) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$cL/MhGc4xLfDTQvH1vEfNuVpf8lEEsJKXAijorTkDMKzMNHWcL1Vq', '2024-10-08 15:44:39');

-- --------------------------------------------------------

--
-- Table structure for table `employeesdb`
--

CREATE TABLE `employeesdb` (
  `id` int(11) NOT NULL,
  `username` varchar(222) NOT NULL,
  `password` varchar(222) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `middlename` varchar(200) NOT NULL,
  `email_address` varchar(150) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `office_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employeesdb`
--

INSERT INTO `employeesdb` (`id`, `username`, `password`, `lastname`, `firstname`, `middlename`, `email_address`, `contact_number`, `address`, `position`, `department`, `office_address`) VALUES
(1, 'angelenedc', '$2y$10$coyYe4Dc7NGQ4OmP/yCZX.vNdaTXxhYaBIJioV6t5sHDX7IqwFhIe', ' Chavez', 'Angelene', 'Dalwampu', 'dechavezangelene1@gmail.com', '09091973990', 'Orense, Bauan, Batangas', 'CFM', 'Tourism', 'Anilao, Mabini, Batangas'),
(2, 'angelene', '$2y$10$1sjYwuRFkl7qYk8F07YkTedehrQkfSYGE0rXo76wOGNor/EH60AKy', 'Aj', 'Jella', 'Dalwampo', 'dgetsf@gmai.com', '09091973990', '123 Calm Waters Lane Tranquil Town, CA 90210', 'CFM', 'Tourism', 'Anilao, Mabini, Batangas');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(20) NOT NULL,
  `recipient_id` int(20) NOT NULL,
  `message_content` varchar(222) NOT NULL,
  `label` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `recipient_id`, `message_content`, `label`) VALUES
(1, 1, 3, 'testcase', 'Reminder'),
(2, 1, 1, 'kmjnhbgvfcgvhbjnkm', 'Reminder'),
(3, 1, 2, 'kmjnhbgvfcgvhbjnkm', 'Reminder'),
(4, 1, 3, 'kmjnhbgvfcgvhbjnkm', 'Reminder'),
(5, 1, 1, 'hbd', 'Announcement'),
(6, 1, 2, 'hbd', 'Announcement'),
(7, 1, 3, 'hbd', 'Announcement'),
(8, 1, 1, 'hbd', 'Announcement'),
(9, 1, 2, 'hbd', 'Announcement'),
(10, 1, 3, 'hbd', 'Announcement'),
(11, 1, 1, '', ''),
(12, 1, 2, '', ''),
(13, 1, 3, '', ''),
(14, 1, 1, '', ''),
(15, 1, 1, 'hjkdel', 'important'),
(16, 1, 2, 'hjkdel', 'important'),
(17, 1, 3, 'hjkdel', 'important'),
(18, 1, 2, 'test', 'Announcement'),
(19, 1, 1, 'test send', 'Announcement'),
(20, 1, 2, 'test send', 'Announcement'),
(21, 1, 3, 'test send', 'Announcement');

-- --------------------------------------------------------

--
-- Table structure for table `messagesdb`
--

CREATE TABLE `messagesdb` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `label` varchar(50) DEFAULT NULL,
  `send_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messagesdb`
--

INSERT INTO `messagesdb` (`id`, `sender_id`, `recipient_id`, `message_content`, `label`, `send_time`, `created_at`) VALUES
(1, 1, 1, 'vhjj', '', '0000-00-00 00:00:00', '2024-10-18 02:28:24'),
(2, 1, 2, 'yes', '', '2024-10-18 10:30:00', '2024-10-18 02:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `nationalities`
--

CREATE TABLE `nationalities` (
  `id` int(11) NOT NULL,
  `nationality` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nationalities`
--

INSERT INTO `nationalities` (`id`, `nationality`) VALUES
(1, 'Afghan'),
(2, 'Albanian'),
(3, 'Algerian'),
(4, 'Andorran'),
(5, 'Angolan'),
(6, 'Antiguan and Barbudan'),
(7, 'Argentine'),
(8, 'Armenian'),
(9, 'Australian'),
(10, 'Austrian'),
(11, 'Azerbaijani'),
(12, 'Bahamas'),
(13, 'Bahraini'),
(14, 'Bangladeshi'),
(15, 'Barbadian'),
(16, 'Bashkir'),
(17, 'Belarusian'),
(18, 'Belgian'),
(19, 'Belizean'),
(20, 'Beninese'),
(21, 'Bhutanese'),
(22, 'Bolivian'),
(23, 'Bosnian'),
(24, 'Botswanan'),
(25, 'Brazilian'),
(26, 'British'),
(27, 'Bruneian'),
(28, 'Bulgarian'),
(29, 'Burkinabe'),
(30, 'Burundian'),
(31, 'Cabo Verdean'),
(32, 'Cambodian'),
(33, 'Cameroonian'),
(34, 'Canadian'),
(35, 'Central African'),
(36, 'Chadian'),
(37, 'Chilean'),
(38, 'Chinese'),
(39, 'Colombian'),
(40, 'Comorian'),
(41, 'Congolese'),
(42, 'Costa Rican'),
(43, 'Croatian'),
(44, 'Cuban'),
(45, 'Cypriot'),
(46, 'Czech'),
(47, 'Danish'),
(48, 'Djiboutian'),
(49, 'Dominican'),
(50, 'Dutch'),
(51, 'East Timorese'),
(52, 'Ecuadorean'),
(53, 'Egyptian'),
(54, 'Salvadoran'),
(55, 'Equatorial Guinean'),
(56, 'Eritrean'),
(57, 'Estonian'),
(58, 'Eswatini'),
(59, 'Ethiopian'),
(60, 'Fijian'),
(61, 'Finnish'),
(62, 'French'),
(63, 'Gabonese'),
(64, 'Gambian'),
(65, 'Georgian'),
(66, 'German'),
(67, 'Ghanaian'),
(68, 'Greek'),
(69, 'Grenadian'),
(70, 'Guatemalan'),
(71, 'Guinea-Bissauan'),
(72, 'Guinean'),
(73, 'Guyanese'),
(74, 'Haitian'),
(75, 'Honduran'),
(76, 'Hungarian'),
(77, 'Icelandic'),
(78, 'Indian'),
(79, 'Indonesian'),
(80, 'Iranian'),
(81, 'Iraqi'),
(82, 'Irish'),
(83, 'Israeli'),
(84, 'Italian'),
(85, 'Ivorian'),
(86, 'Jamaican'),
(87, 'Japanese'),
(88, 'Jordanian'),
(89, 'Kazakh'),
(90, 'Kenyan'),
(91, 'Kiribati'),
(92, 'Kuwaiti'),
(93, 'Kyrgyz'),
(94, 'Laotian'),
(95, 'Latvian'),
(96, 'Lebanese'),
(97, 'Lesotho'),
(98, 'Liberian'),
(99, 'Libyan'),
(100, 'Liechtensteiner'),
(101, 'Lithuanian'),
(102, 'Luxembourger'),
(103, 'Malawian'),
(104, 'Malaysian'),
(105, 'Maldivian'),
(106, 'Malian'),
(107, 'Maltese'),
(108, 'Marshallese'),
(109, 'Mauritanian'),
(110, 'Mauritian'),
(111, 'Mexican'),
(112, 'Micronesian'),
(113, 'Moldovan'),
(114, 'Monacan'),
(115, 'Mongolian'),
(116, 'Montenegrin'),
(117, 'Moroccan'),
(118, 'Mozambican'),
(119, 'Namibian'),
(120, 'Nauruan'),
(121, 'Nepalese'),
(122, 'Dutch'),
(123, 'Nigerian'),
(124, 'North Korean'),
(125, 'Norwegian'),
(126, 'Omani'),
(127, 'Pakistani'),
(128, 'Palauan'),
(129, 'Palestinian'),
(130, 'Panamanian'),
(131, 'Papua New Guinean'),
(132, 'Paraguayan'),
(133, 'Peruvian'),
(134, 'Philippine'),
(135, 'Polish'),
(136, 'Portuguese'),
(137, 'Qatari'),
(138, 'Romanian'),
(139, 'Russian'),
(140, 'Rwandan'),
(141, 'Saint Kitts and Nevis'),
(142, 'Saint Lucian'),
(143, 'Saint Vincent and the Grenadines'),
(144, 'Samoan'),
(145, 'San Marinese'),
(146, 'Sao Tomean'),
(147, 'Saudi Arabian'),
(148, 'Senegalese'),
(149, 'Serbian'),
(150, 'Seychellois'),
(151, 'Sierra Leonean'),
(152, 'Singaporean'),
(153, 'Slovak'),
(154, 'Slovenian'),
(155, 'Solomon Islander'),
(156, 'Somali'),
(157, 'South African'),
(158, 'South Korean'),
(159, 'Spanish'),
(160, 'Sri Lankan'),
(161, 'Sudanese'),
(162, 'Surinamese'),
(163, 'Swedish'),
(164, 'Swiss'),
(165, 'Syrian'),
(166, 'Tajik'),
(167, 'Tanzanian'),
(168, 'Thai'),
(169, 'Togolese'),
(170, 'Tongan'),
(171, 'Trinidadian'),
(172, 'Tunisian'),
(173, 'Turkish'),
(174, 'Turkmen'),
(175, 'Tuvaluan'),
(176, 'Ugandan'),
(177, 'Ukrainian'),
(178, 'United Arab Emirates'),
(179, 'United Kingdom'),
(180, 'United States'),
(181, 'Uruguayan'),
(182, 'Uzbek'),
(183, 'Vanuatuan'),
(184, 'Venezuelan'),
(185, 'Vietnamese'),
(186, 'Yemeni'),
(187, 'Zambian'),
(188, 'Zimbabwean');

-- --------------------------------------------------------

--
-- Table structure for table `page_visits`
--

CREATE TABLE `page_visits` (
  `id` int(11) NOT NULL,
  `visit_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_visits`
--

INSERT INTO `page_visits` (`id`, `visit_count`) VALUES
(1, 43);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `resort_name` varchar(255) NOT NULL,
  `type_class` varchar(255) NOT NULL,
  `rooms_total` int(11) NOT NULL,
  `total_guests` int(11) NOT NULL,
  `overnight_guests` int(11) NOT NULL,
  `foreign_visitors` int(11) NOT NULL,
  `this_municipality` int(11) NOT NULL,
  `this_province` int(11) NOT NULL,
  `other_provinces` int(11) NOT NULL,
  `foreign_country_male` int(11) NOT NULL,
  `foreign_country_female` int(11) NOT NULL,
  `foreign_country_total` int(11) NOT NULL,
  `grand_total_visitors` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `resort_name`, `type_class`, `rooms_total`, `total_guests`, `overnight_guests`, `foreign_visitors`, `this_municipality`, `this_province`, `other_provinces`, `foreign_country_male`, `foreign_country_female`, `foreign_country_total`, `grand_total_visitors`, `created_at`) VALUES
(1, 'Tranquil Waters Retreat', 'Type/Class Placeholder', 24, 1565, 1259, 1169, 0, 0, 1565, 47, 58, 1565, 1565, '2024-10-22 08:33:44'),
(2, 'Sunset Paradise Resort', 'Type/Class Placeholder', 4, 918, 0, 409, 0, 0, 918, 10, 11, 918, 918, '2024-10-22 08:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `resorts`
--

CREATE TABLE `resorts` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `resort_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `business_permit_no` varchar(100) NOT NULL,
  `contact_no` varchar(100) NOT NULL,
  `resort_info` varchar(222) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `resort_images` text DEFAULT NULL,
  `profile_picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resorts`
--

INSERT INTO `resorts` (`id`, `username`, `password`, `lastname`, `firstname`, `middlename`, `resort_name`, `address`, `business_permit_no`, `contact_no`, `resort_info`, `email`, `resort_images`, `profile_picture`) VALUES
(1, 'serenity_cove', '$2y$10$xrP42bwngmyzew8DtSQoz..VHOopPgS3.gHCj5B3NmPnjT.zx5uZ.', 'De  Chavez', 'Angelene', 'Dalwampo', 'Serenity Cove Resort', '123 Calm Waters Lane Tranquil Town, CA 90210', 'SC-123456', '09683745263', 'sample update\r\n\r\nLocation: 123 Calm Waters Lane, Tranquil Town, CA 90210\r\nOverview: A beachfront resort offering a tranquil escape with stunning ocean views.\r\nServices:\r\nLuxurious accommodations (ocean-view rooms, private ', 'angelene.dechavez.96@gmail.com', NULL, ''),
(2, 'tranquil_waters', '$2y$10$fcx4gsYdjNz/xSgxrbAUZuZgkAXgY55XUbEnrykOLE3s9.HXs5FYS', 'Dalawampu', 'Jella', 'Del Valle', 'Tranquil Waters Retreat', '456 Peaceful Stream Blvd Relaxation City, FL 33101', 'TW-654321', '0932735273', 'Test 1 2 3 4', 'angelyynnn@gmail.com', '', 'uploads/mabinilogo.png'),
(3, 'sunset_paradise', '$2y$10$SxbQotuAPNTx80RelBp33uPEISBEGhTNT51zcTix3OjZZRvkm7ye.', 'Aj', 'Jella', 'Dalwampo', 'Sunset Paradise Resort', '789 Sunset Boulevard, Oceanview, HI 96815', ' SP-987654', '09563538243', '', 'dechavezangelene@gmail.com', NULL, 'uploads/WIN_20241011_14_56_31_Pro (2).jpg');

-- --------------------------------------------------------

--
-- Table structure for table `resorts_img`
--

CREATE TABLE `resorts_img` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resorts_img`
--

INSERT INTO `resorts_img` (`id`, `name`, `image_url`) VALUES
(1, 'Casa', 'images/casa.jpg'),
(2, 'Masasa', 'images/Masasa-Beach-Batangas-1.jpg'),
(3, 'Eagle', 'images/eagle.jpg'),
(5, 'Sea Spring', 'images/sea-spring.jpg'),
(6, 'Seapoc', 'images/seapoc.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_registration`
--

CREATE TABLE `tourist_registration` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `no_of_tourist` int(100) NOT NULL,
  `children` int(11) DEFAULT NULL,
  `youth` int(11) DEFAULT NULL,
  `adults` int(11) DEFAULT NULL,
  `male` int(11) DEFAULT NULL,
  `female` int(11) DEFAULT NULL,
  `nationality` int(11) DEFAULT NULL,
  `arrival` date DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure` date DEFAULT NULL,
  `stay_days` int(11) DEFAULT NULL,
  `stay_nights` int(11) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `resort_id` int(11) DEFAULT NULL,
  `date_column` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourist_registration`
--

INSERT INTO `tourist_registration` (`id`, `first_name`, `surname`, `email`, `contact`, `province`, `city`, `barangay`, `no_of_tourist`, `children`, `youth`, `adults`, `male`, `female`, `nationality`, `arrival`, `arrival_time`, `departure`, `stay_days`, `stay_nights`, `rooms`, `resort_id`, `date_column`) VALUES
(7, 'Angelene', 'Dalawampu', 'angelene.dechavez.96@gmail.com', '09091973990', NULL, NULL, NULL, 102, 5, 5, 5, 5, 2, NULL, '2024-10-08', '18:46:00', '2024-10-08', 1, 0, 1, 2, '2024-10-22'),
(8, 'Angelene', 'De Chavez', 'jelladalawampu7@gmail.com', '09091973990', NULL, NULL, NULL, 102, 5, 5, 5, 4, 4, NULL, '2024-10-08', '07:02:00', '2024-10-08', 1, 0, 1, 2, '2024-10-22'),
(9, 'Jella', 'Dalawampu', 'gelyynnn@gmail.com', '09091973990', NULL, NULL, NULL, 102, 5, 5, 5, 5, 5, NULL, '2024-10-08', '19:05:00', '2024-10-08', 1, 0, 1, 1, '2024-10-22'),
(10, 'Angelene', 'De Chavez', 'gelyynnn@gmail.com', '09091973990', NULL, NULL, NULL, 67, 5, 5, 5, 4, 5, NULL, '2024-10-09', '09:06:00', '2024-10-09', 1, 0, 1, 1, '2024-10-22'),
(11, 'Angelene', 'Dalawampu', 'gelyynnn@gmail.com', '09091973990', NULL, NULL, NULL, 102, 4, 5, 5, 5, 4, 34, '2024-02-09', '03:22:00', '2024-02-14', 5, 4, 3, 2, '2024-10-22'),
(12, 'Jella', 'De Chavez', 'angelene.dechavez.96@gmail.com', '9876789332', NULL, NULL, NULL, 45, 5, 5, 4, 3, 4, 134, '2024-07-09', '03:24:00', '2024-07-17', 8, 7, 3, 2, '2024-10-22'),
(13, 'Jella', 'De Chavez', 'angelene.dechavez.96@gmail.com', '9876789332', NULL, NULL, NULL, 45, 5, 5, 4, 3, 4, 134, '2024-07-09', '03:24:00', '2024-07-17', 8, 7, 3, 2, '2024-10-22'),
(19, 'Angelene', 'De Chavez', 'jelladalawampu7@gmail.com', '9876789332', NULL, NULL, NULL, 102, 5, 5, 4, 3, 5, 134, '2024-05-09', '15:58:00', '2024-05-09', 1, 0, 1, 2, '2024-10-22'),
(20, 'Angelene', 'De Chavez', 'gelyynnn@gmail.com', '9876789332', NULL, NULL, NULL, 67, 4, 3, 4, 4, 5, 10, '2024-01-09', '16:00:00', '2024-01-10', 2, 1, 2, 2, '2024-10-22'),
(21, 'Angelene', 'De Chavez', 'dechavezangelene@gmail.com', '09091973990', NULL, NULL, NULL, 409, 5, 3, 5, 4, 4, 134, '2024-04-09', '16:13:00', '2024-04-09', 1, 0, 3, 3, '2024-10-22'),
(22, 'Angelene', 'Dalawampu', 'angelene.dechavez.96@gmail.com', '09091973990', NULL, NULL, NULL, 409, 10, 11, 23, 2, 3, 25, '2024-10-10', '22:38:00', '2024-10-10', 1, 0, 0, 3, '2024-10-22'),
(23, 'Jella', 'Dalawampu', 'gelyynnn@gmail.com', '09091973990', NULL, NULL, NULL, 100, 10, 11, 11, 4, 4, 134, '2024-12-10', '13:29:00', '2024-12-10', 1, 0, 1, 3, '2024-10-22'),
(24, 'Angelene', 'De Chavez', 'dechavex@gmail.com', '0909765384', NULL, NULL, NULL, 102, 23, 45, 23, 5, 5, 134, '2025-01-11', '19:18:00', '2025-01-11', 1, 0, 2, 1, '2024-10-22'),
(25, 'Angelene', 'De Chavez', 'jelladalawampu7@gmail.com', '09091973990', NULL, NULL, NULL, 1000, 10, 12, 12, 20, 30, 133, '2024-10-22', '04:21:00', '2024-10-24', 3, 2, 10, 2, '2024-10-22'),
(26, 'Jella', 'Dalawampu', 'jelladalawampu7@gmail.com', '09091973990', NULL, NULL, NULL, 500, 250, 150, 100, 250, 250, 16, '2024-10-22', '18:30:00', '2024-10-24', 3, 2, 10, 3, '2024-10-22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resort_id` (`resort_id`);

--
-- Indexes for table `added_activities`
--
ALTER TABLE `added_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `resort_id` (`resort_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employeesdb`
--
ALTER TABLE `employeesdb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `messagesdb`
--
ALTER TABLE `messagesdb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `nationalities`
--
ALTER TABLE `nationalities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_visits`
--
ALTER TABLE `page_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resorts`
--
ALTER TABLE `resorts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tourist_registration`
--
ALTER TABLE `tourist_registration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resort` (`resort_id`),
  ADD KEY `fk_nationality` (`nationality`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(150) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `added_activities`
--
ALTER TABLE `added_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(150) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employeesdb`
--
ALTER TABLE `employeesdb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `messagesdb`
--
ALTER TABLE `messagesdb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nationalities`
--
ALTER TABLE `nationalities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `page_visits`
--
ALTER TABLE `page_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `resorts`
--
ALTER TABLE `resorts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tourist_registration`
--
ALTER TABLE `tourist_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `fk_resort_id` FOREIGN KEY (`resort_id`) REFERENCES `resorts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `added_activities`
--
ALTER TABLE `added_activities`
  ADD CONSTRAINT `added_activities_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `added_activities_ibfk_2` FOREIGN KEY (`resort_id`) REFERENCES `resorts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `resorts` (`id`);

--
-- Constraints for table `messagesdb`
--
ALTER TABLE `messagesdb`
  ADD CONSTRAINT `messagesdb_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `employeesdb` (`id`),
  ADD CONSTRAINT `messagesdb_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `resorts` (`id`);

--
-- Constraints for table `tourist_registration`
--
ALTER TABLE `tourist_registration`
  ADD CONSTRAINT `fk_nationality` FOREIGN KEY (`nationality`) REFERENCES `nationalities` (`id`),
  ADD CONSTRAINT `fk_resort` FOREIGN KEY (`resort_id`) REFERENCES `resorts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
