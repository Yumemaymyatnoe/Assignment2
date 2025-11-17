-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2025 at 05:39 PM
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
-- Database: `triosoft_it_solution_careers`
--

-- --------------------------------------------------------

--
-- Table structure for table `eoi`
--

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `job_ref` varchar(20) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `street_address` varchar(40) NOT NULL,
  `suburb_town` varchar(40) NOT NULL,
  `state` varchar(20) NOT NULL,
  `postcode` varchar(4) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `skill1` varchar(20) DEFAULT NULL,
  `skill2` varchar(20) DEFAULT NULL,
  `skill3` varchar(20) DEFAULT NULL,
  `skill4` varchar(20) DEFAULT NULL,
  `other_skills` text DEFAULT NULL,
  `status` enum('New','Current','Final') DEFAULT 'New',
  `application_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eoi`
--

INSERT INTO `eoi` (`EOInumber`, `job_ref`, `first_name`, `last_name`, `street_address`, `suburb_town`, `state`, `postcode`, `email`, `phone`, `skill1`, `skill2`, `skill3`, `skill4`, `other_skills`, `status`, `application_date`) VALUES
(1, 'Web-Developer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'WA', '6542', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'Python', NULL, NULL, 'Laravel', 'New', '2025-10-28 15:44:15'),
(2, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Docker', NULL, '', 'New', '2025-10-28 15:58:41'),
(3, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Docker', NULL, '', 'New', '2025-10-28 16:00:01'),
(4, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:17:23'),
(5, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:23:21'),
(6, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:23:40'),
(7, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:25:17'),
(8, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:25:29'),
(9, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:32:26'),
(10, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:34:44'),
(11, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:35:22'),
(12, 'Programmer', 'Mohona Tabassum', 'Sneha', 'B2-26-3A, Razak City Residence', 'Sungai Besi', 'QLD', '4008', 'mohonatabassumsneha@gmail.com', '1234567898', 'HTML', 'MySQL', 'Java', NULL, '', 'New', '2025-10-28 16:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `job_ref` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `salary_range` varchar(50) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `posted_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `job_ref`, `title`, `description`, `requirements`, `salary_range`, `location`, `posted_date`) VALUES
(1, 'Web-Developer', 'Web Developer', 'We are looking for a skilled Web Developer to join our dynamic team. You will be responsible for developing and maintaining web applications using modern technologies.', 'PHP, HTML, CSS, JavaScript, MySQL', '$70,000 - $90,000', 'Melbourne, VIC', '2025-10-28 15:42:18'),
(2, 'Programmer', 'Software Programmer', 'Join our software development team to create innovative solutions. You will work on various projects including web applications, mobile apps, and enterprise software.', 'Python, Java, C++, Database Design', '$75,000 - $95,000', 'Sydney, NSW', '2025-10-28 15:42:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD UNIQUE KEY `job_ref` (`job_ref`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
