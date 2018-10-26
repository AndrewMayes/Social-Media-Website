-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 26, 2018 at 05:43 PM
-- Server version: 5.7.24-0ubuntu0.16.04.1
-- PHP Version: 7.0.32-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs418`
--
CREATE DATABASE IF NOT EXISTS `cs418` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `cs418`;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `type`) VALUES
(1, 'Global', 'public'),
(2, 'Gaming', 'private'),
(3, 'Sports', 'private'),
(4, 'Anime', 'private');

-- --------------------------------------------------------

--
-- Table structure for table `group_users`
--

DROP TABLE IF EXISTS `group_users`;
CREATE TABLE `group_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `group_users`
--

INSERT INTO `group_users` (`user_id`, `group_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 3),
(3, 1),
(3, 2),
(3, 4),
(4, 1),
(5, 1),
(5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `msg_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `msg` varchar(6000) NOT NULL,
  `post_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_id` int(10) UNSIGNED NOT NULL,
  `likes` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`, `likes`) VALUES
(1, 5, 'Hello. This is my first post!', '2018-10-23 02:52:11', 1, 1),
(2, 2, '<b>hello there friends</b>', '2018-10-23 02:53:45', 1, 0),
(3, 3, 'Is this the home group?', '2018-10-23 02:54:33', 1, 0),
(4, 4, 'Hey', '2018-10-23 02:55:02', 1, 0),
(5, 5, 'I hate racing games', '2018-10-23 02:55:24', 2, 0),
(6, 4, 'Are there any good spy video games?', '2018-10-23 02:57:46', 2, 1),
(7, 4, 'Howdy!', '2018-10-23 02:58:17', 1, 0),
(8, 3, 'testing testing', '2018-10-23 02:58:52', 3, 0),
(9, 1, 'where am I?', '2018-10-23 02:59:38', 1, 0),
(10, 5, 'kachow', '2018-10-23 03:00:17', 1, 0),
(11, 1, 'hello bois?', '2018-10-26 00:05:09', 1, 2),
(12, 5, 'nicely done!', '2018-10-26 00:35:26', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `messages_likes`
--

DROP TABLE IF EXISTS `messages_likes`;
CREATE TABLE `messages_likes` (
  `msg_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages_likes`
--

INSERT INTO `messages_likes` (`msg_id`, `user_id`) VALUES
(1, 5),
(6, 1),
(11, 1),
(11, 5),
(12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sub_groups`
--

DROP TABLE IF EXISTS `sub_groups`;
CREATE TABLE `sub_groups` (
  `sg_id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `img` varchar(580) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `password`, `email`, `username`, `img`) VALUES
(1, 'Tow', 'Mater', '@mater', 'mater@rsprings.gov', 'mater', 'Mater_(Cars).png'),
(2, 'Sally', 'Carrera', '@sally', 'porsche@rsprings.gov', 'sally', 'sally-cars-3-81.7.jpg'),
(3, 'Doc', 'Hudson', '@doc', 'hornet@rsprings.gov', 'doc', '149548-1532336916.jpg'),
(4, 'Finn', 'McMissile', '@mcmissile', 'topsecret@agent.org', 'mcmissile', 'finn-mcmissile-cars-2-68.6.jpg'),
(5, 'Lightning', 'McQueen', '@mcqueen', 'kachow@rusteze.com', 'mcqueen', '0409LM.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `group_users`
--
ALTER TABLE `group_users`
  ADD PRIMARY KEY (`user_id`,`group_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `messages_likes`
--
ALTER TABLE `messages_likes`
  ADD PRIMARY KEY (`msg_id`,`user_id`),
  ADD KEY `msg_id` (`msg_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sub_groups`
--
ALTER TABLE `sub_groups`
  ADD PRIMARY KEY (`sg_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `sub_groups`
--
ALTER TABLE `sub_groups`
  MODIFY `sg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `group_users`
--
ALTER TABLE `group_users`
  ADD CONSTRAINT `group_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `group_users_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`);

--
-- Constraints for table `messages_likes`
--
ALTER TABLE `messages_likes`
  ADD CONSTRAINT `messages_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_likes_ibfk_3` FOREIGN KEY (`msg_id`) REFERENCES `messages` (`msg_id`);

--
-- Constraints for table `sub_groups`
--
ALTER TABLE `sub_groups`
  ADD CONSTRAINT `sub_groups_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
