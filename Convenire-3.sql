-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 27, 2017 at 01:14 AM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Convenire`
--

-- --------------------------------------------------------

--
-- Table structure for table `Events`
--

CREATE TABLE `Events` (
  `EventId` varchar(40) NOT NULL,
  `Address` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `StartTime` time NOT NULL DEFAULT '00:00:00',
  `EndTime` time NOT NULL DEFAULT '00:00:00',
  `Description` varchar(400) NOT NULL,
  `EventTitle` varchar(250) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `AdminPassword` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Events`
--

INSERT INTO `Events` (`EventId`, `Address`, `Date`, `StartTime`, `EndTime`, `Description`, `EventTitle`, `Password`, `AdminPassword`) VALUES
('89b9b98d621b4477490d', 'Trottier', '2017-11-14', '11:45:00', '12:00:00', 'the end of the world', 'Apocalypse', 'end', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Events`
--
ALTER TABLE `Events`
  ADD PRIMARY KEY (`EventId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
