-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 05, 2017 at 09:33 PM
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
-- Table structure for table `EmailName`
--

CREATE TABLE `EmailName` (
  `Email` varchar(250) NOT NULL,
  `Name` varchar(250) NOT NULL,
  `EventId` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `EventEmails`
--

CREATE TABLE `EventEmails` (
  `EventId` varchar(40) NOT NULL,
  `Email` varchar(350) NOT NULL,
  `votedLoc` tinyint(1) NOT NULL,
  `votedTime` tinyint(1) NOT NULL,
  `location` varchar(250) NOT NULL,
  `date` date NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `EventEmails`
--

INSERT INTO `EventEmails` (`EventId`, `Email`, `votedLoc`, `votedTime`, `location`, `date`, `start`, `end`) VALUES
('f60256996bce7c44b48c', 'aa@aa.cb', 0, 0, '', '0000-00-00', '00:00:00', '00:00:00'),
('f60256996bce7c44b48c', 'aa@aa.cc', 0, 0, '', '0000-00-00', '00:00:00', '00:00:00'),
('f60256996bce7c44b48c', 'aa@aa.cd', 1, 0, '2', '0000-00-00', '00:00:00', '00:00:00');

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
  `AdminPassword` varchar(300) NOT NULL,
  `Creator` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Events`
--

INSERT INTO `Events` (`EventId`, `Address`, `Date`, `StartTime`, `EndTime`, `Description`, `EventTitle`, `Password`, `AdminPassword`, `Creator`) VALUES
('f60256996bce7c44b48c', '1', '2017-12-06', '06:15:00', '07:00:00', 'test2', 'test2', '$2y$12$/PgzoMuo1GjZrDQ0tWL04OoHTAZFAtxiXRnR2S3HWuUOj7HUgwpvO', '$2y$12$FJU6n3M4gPozPWWrpvzFbefDmcNRgORyL/Ixs5iKDhuaYRx8RbUzy', 'aa@aa.ca');

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

--
-- Dumping data for table `EventTasks`
--

INSERT INTO `EventTasks` (`EventId`, `TaskName`, `NameInCharge`, `EmailInCharge`) VALUES
('f60256996bce7c44b48c', '1', 'aa', 'aa@aa.ca'),
('f60256996bce7c44b48c', '2', '', ''),
('f60256996bce7c44b48c', '3', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `LocationPoll`
--

CREATE TABLE `LocationPoll` (
  `EventId` varchar(40) NOT NULL,
  `Address` varchar(250) NOT NULL,
  `votes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `LocationPoll`
--

INSERT INTO `LocationPoll` (`EventId`, `Address`, `votes`) VALUES
('f60256996bce7c44b48c', '1', 0),
('f60256996bce7c44b48c', '2', 1),
('f60256996bce7c44b48c', '3', 0);

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

--
-- Dumping data for table `TaskDiscussion`
--

INSERT INTO `TaskDiscussion` (`EventId`, `email`, `Comment`, `date`, `time`) VALUES
('f60256996bce7c44b48c', 'aa@aa.cd', 'aa', '2017-12-05', '00:00:00'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aa', '2017-12-05', '13:21:00'),
('f60256996bce7c44b48c', 'aa@aa.cd', '', '2017-12-05', '13:27:00'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aaa', '2017-12-05', '13:29:00'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'b', '2017-12-05', '13:32:12'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'b', '2017-12-05', '13:32:13'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'b', '2017-12-05', '13:32:15'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aaas', '2017-12-05', '13:34:03'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aaasd', '2017-12-05', '13:34:05'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aaasd', '2017-12-05', '13:34:06'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aaasd', '2017-12-05', '13:34:12'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aaasd', '2017-12-05', '13:34:13'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aaasd', '2017-12-05', '13:34:14'),
('f60256996bce7c44b48c', 'aa@aa.cd', 'aa', '2017-12-05', '13:36:56');

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
-- Dumping data for table `TimePoll`
--

INSERT INTO `TimePoll` (`EventId`, `StartTime`, `EndTime`, `Date`, `votes`) VALUES
('f60256996bce7c44b48c', '06:15:00', '06:45:00', '2017-12-29', 0),
('f60256996bce7c44b48c', '06:15:00', '07:00:00', '2017-12-06', 0),
('f60256996bce7c44b48c', '06:30:00', '07:00:00', '2017-12-08', 0);

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
