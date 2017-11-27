-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 27, 2017 at 03:29 AM
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
  `Email` varchar(350) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `EventEmails`
--

INSERT INTO `EventEmails` (`EventId`, `Email`) VALUES
('89b9b98d621b4477490d', 'andre@lol.ca'),
('89b9b98d621b4477490d', 'phil@lmao.ca'),
('89b9b98d621b4477490d', 'ricky@damn.ca');

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
('3bb71e30e547b166d2e8', 'McGill', '2017-11-27', '11:15:00', '11:30:00', 'Final exam of comp 312 - managing matrix universes', 'Final exam 312', 'comp312', 'profherro'),
('89b9b98d621b4477490d', 'Trottier', '2017-11-14', '11:45:00', '12:00:00', 'the end of the world', 'Apocalypse', 'end', 'admin'),
('9a83be5a448e01199e31', 'McGill', '2017-11-27', '11:15:00', '11:30:00', 'Final exam of comp 312 - managing matrix universes', 'Final exam 312', 'comp312', 'profherro');

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
('89b9b98d621b4477490d', 'bring water', '', ''),
('89b9b98d621b4477490d', 'buy drinks', '', ''),
('89b9b98d621b4477490d', 'buy food', '', ''),
('89b9b98d621b4477490d', 'buy monster', '', ''),
('89b9b98d621b4477490d', 'Get bbq', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `LocationPoll`
--

CREATE TABLE `LocationPoll` (
  `EventId` varchar(40) NOT NULL,
  `Address` varchar(250) NOT NULL,
  `Votes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `LocationPoll`
--

INSERT INTO `LocationPoll` (`EventId`, `Address`, `Votes`) VALUES
('3bb71e30e547b166d2e8', 'CJE', 0),
('3bb71e30e547b166d2e8', 'Concordia', 0),
('3bb71e30e547b166d2e8', 'McGill', 0),
('3bb71e30e547b166d2e8', 'UQAM', 0),
('89b9b98d621b4477490d', 'Montreal', 0),
('89b9b98d621b4477490d', 'NY', 0),
('9a83be5a448e01199e31', 'CJE', 0),
('9a83be5a448e01199e31', 'Concordia', 0),
('9a83be5a448e01199e31', 'McGill', 0),
('9a83be5a448e01199e31', 'UQAM', 0);

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
  `StartTime` time NOT NULL DEFAULT '00:00:00',
  `EndTime` time NOT NULL DEFAULT '00:00:00',
  `Date` date NOT NULL,
  `Votes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TimePoll`
--

INSERT INTO `TimePoll` (`EventId`, `StartTime`, `EndTime`, `Date`, `Votes`) VALUES
('3bb71e30e547b166d2e8', '11:15:00', '11:30:00', '2017-11-27', 0),
('3bb71e30e547b166d2e8', '11:15:00', '11:30:00', '2017-11-28', 0),
('89b9b98d621b4477490d', '08:00:00', '10:30:00', '2017-11-23', 0),
('89b9b98d621b4477490d', '11:15:00', '12:30:00', '2017-11-02', 0),
('89b9b98d621b4477490d', '15:00:00', '01:30:00', '2017-11-17', 0),
('9a83be5a448e01199e31', '11:15:00', '11:30:00', '2017-11-27', 0),
('9a83be5a448e01199e31', '11:15:00', '11:30:00', '2017-11-28', 0);

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
  ADD PRIMARY KEY (`EventId`,`email`,`Time`);

--
-- Indexes for table `TimePoll`
--
ALTER TABLE `TimePoll`
  ADD PRIMARY KEY (`EventId`,`StartTime`,`EndTime`,`Date`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
