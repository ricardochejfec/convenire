-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 13, 2017 at 09:12 PM
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
-- Table structure for table `EventEmails`
--

CREATE TABLE `EventEmails` (
  `EventId` varchar(40) NOT NULL,
  `Email` varchar(350) NOT NULL,
  `votedLoc` tinyint(1) NOT NULL,
  `location` varchar(250) NOT NULL,
  `votedTime` tinyint(1) NOT NULL,
  `date` date NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `Password` varchar(300) NOT NULL,
  `Creator` varchar(250) NOT NULL
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
  `Address` varchar(250) NOT NULL,
  `votes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TaskDiscussion`
--

CREATE TABLE `TaskDiscussion` (
  `EventId` varchar(40) NOT NULL,
  `email` varchar(350) NOT NULL,
  `Comment` varchar(250) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TimePoll`
--

CREATE TABLE `TimePoll` (
  `EventId` varchar(40) NOT NULL,
  `StartTime` time NOT NULL DEFAULT '00:00:00',
  `EndTime` time NOT NULL DEFAULT '00:00:00',
  `Date` date NOT NULL,
  `votes` int(11) NOT NULL
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
  ADD PRIMARY KEY (`EventId`,`Address`);

--
-- Indexes for table `TaskDiscussion`
--
ALTER TABLE `TaskDiscussion`
  ADD PRIMARY KEY (`EventId`,`email`,`date`,`time`);

--
-- Indexes for table `TimePoll`
--
ALTER TABLE `TimePoll`
  ADD PRIMARY KEY (`EventId`,`StartTime`,`EndTime`,`Date`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
