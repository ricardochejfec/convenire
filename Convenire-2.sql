-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 26, 2017 at 09:25 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

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
-- Table structure for table `EventEmails`
--

CREATE TABLE `EventEmails` (
  `EventId` varchar(40) NOT NULL,
  `Email` varchar(350) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Events`
--

CREATE TABLE `Events` (
  `EventId` varchar(40) NOT NULL,
  `Address` varchar(100) NOT NULL,
  `Date` date NOT NULL,
  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `EndTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Description` varchar(400) NOT NULL,
  `EventTitle` varchar(250) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `AdminPassword` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `EventTasks`
--

CREATE TABLE `EventTasks` (
  `EventId` varchar(40) NOT NULL,
  `TaskName` varchar(250) NOT NULL,
  `NameInCharge` varchar(250) NOT NULL,
  `EmailInCharge` varchar(350) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `LocationPoll`
--

CREATE TABLE `LocationPoll` (
  `EventId` varchar(40) NOT NULL,
  `Location` varchar(250) NOT NULL,
  `Votes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TaskDiscussion`
--

CREATE TABLE `TaskDiscussion` (
  `EventId` varchar(40) NOT NULL,
  `email` varchar(350) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Comment` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TimePoll`
--

CREATE TABLE `TimePoll` (
  `EventId` varchar(40) NOT NULL,
  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `EndTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Date` date NOT NULL,
  `Votes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `EventEmails`
--
ALTER TABLE `EventEmails`
  ADD PRIMARY KEY (`EventId`,`Email`);

--
-- Indexes for table `Events`
--
ALTER TABLE `Events`
  ADD PRIMARY KEY (`EventId`);

--
-- Indexes for table `EventTasks`
--
ALTER TABLE `EventTasks`
  ADD PRIMARY KEY (`EventId`,`TaskName`);

--
-- Indexes for table `LocationPoll`
--
ALTER TABLE `LocationPoll`
  ADD PRIMARY KEY (`EventId`);

--
-- Indexes for table `TaskDiscussion`
--
ALTER TABLE `TaskDiscussion`
  ADD PRIMARY KEY (`EventId`,`Time`);

--
-- Indexes for table `TimePoll`
--
ALTER TABLE `TimePoll`
  ADD PRIMARY KEY (`EventId`,`StartTime`,`EndTime`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
