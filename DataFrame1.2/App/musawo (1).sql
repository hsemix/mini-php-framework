-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2017 at 08:44 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `musawo`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` varchar(255) NOT NULL,
  `doctor` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `title`, `description`, `status`, `user_id`, `time`, `doctor`, `created_at`) VALUES
(1, 'Test', 'A any test is sok', 1, 1, '9:00 AM', 'Dr. Lwanga', '2017-05-01 00:00:00'),
(2, 'Wooow', 'Yesssss', 0, 1, '', '', '2017-05-01 20:48:30'),
(3, 'a', 'agently', 0, 1, '', '', '2017-05-03 12:52:28'),
(4, 'head ache', 'hello, can i get a checkup?', 0, 6, '', '', '2017-05-05 15:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_comments`
--

CREATE TABLE IF NOT EXISTS `appointment_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `appointment_comments`
--

INSERT INTO `appointment_comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 1, 'A real test for the comment of an appointment', '2017-05-02 16:16:38');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE IF NOT EXISTS `chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userA` int(11) NOT NULL,
  `userB` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `userA`, `userB`, `created_at`) VALUES
(1, 2, 3, '2017-05-06 19:13:25');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `content`, `created_at`) VALUES
(1, 3, 1, 'That car banange', '2017-04-19 09:16:36'),
(2, 1, 3, 'Real Comment', '0000-00-00 00:00:00'),
(3, 1, 3, 'It is now working, Dammmnnnnn', '2017-04-30 09:07:23'),
(4, 1, 2, 'Yap', '2017-04-30 09:08:03'),
(5, 1, 1, 'Really?', '2017-04-30 09:08:19'),
(6, 1, 2, 'kldsjfkldsfkldslkf', '2017-05-01 17:52:38'),
(7, 1, 2, 'we ', '2017-05-03 17:54:51'),
(8, 5, 3, 'it works fine', '2017-05-05 13:36:16'),
(9, 2, 3, 'This is jsut a test', '2017-05-09 20:47:58');

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE IF NOT EXISTS `diseases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `symptom` text NOT NULL,
  `prevention` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `diseases`
--

INSERT INTO `diseases` (`id`, `name`, `description`, `symptom`, `prevention`, `created_at`) VALUES
(1, 'Malaria', 'It kills', 'High temperatures', 'Sleeping under treated mosquito nets', '2017-05-01 00:00:00'),
(2, 'Typhoid', 'It kills', 'Yellow eyes', 'Drinking boiled water', '2017-05-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `inquiry` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `inquiry`, `description`, `created_at`) VALUES
(1, 1, 'specialists', 'A specialist is needed agently', '2017-05-02 18:22:12'),
(2, 1, 'general system', 'sdsfa', '2017-05-03 12:54:37'),
(3, 1, 'chatting', 'I want to chat with a specialist dentist now', '2017-05-03 12:57:36'),
(4, 1, 'general system', 'blah blah', '2017-05-03 12:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_comments`
--

CREATE TABLE IF NOT EXISTS `feedback_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `feedback_comments`
--

INSERT INTO `feedback_comments` (`id`, `user_id`, `post_id`, `content`, `created_at`) VALUES
(1, 1, 1, 'Really', '2017-05-02 18:40:03'),
(2, 1, 3, 'ok', '2017-05-05 15:36:34');

-- --------------------------------------------------------

--
-- Table structure for table `first_aid`
--

CREATE TABLE IF NOT EXISTS `first_aid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `problem` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `firstaid` text NOT NULL,
  `prevention` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `first_aid`
--

INSERT INTO `first_aid` (`id`, `url`, `problem`, `description`, `firstaid`, `prevention`, `created_at`) VALUES
(1, '', 'Malaria', 'Malaria is a life-threatening disease. It\\''s typically transmitted through the bite of an\n                    infected Anopheles mosquito. Infected mosquitoes carry the Plasmodium parasite. When this mosquito\n                      bites you, the parasite is released into your bloodstream.', 'Shaking chills that can range from moderate to severe', 'Sleep under a treated mosquito net', '2017-05-01 00:00:00'),
(2, '', 'Polio', 'bla bla', 'eeeeeeh', 'immunise', '2017-05-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `forum_comments`
--

CREATE TABLE IF NOT EXISTS `forum_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `forum_comments`
--

INSERT INTO `forum_comments` (`id`, `user_id`, `post_id`, `content`, `created_at`) VALUES
(1, 1, 1, 'Hello', '2017-04-30 10:22:56'),
(2, 1, 4, 'Yah, this is now working', '2017-04-30 12:01:41'),
(3, 1, 2, 'kljklslkfdskfsldflkdsfds', '2017-05-01 17:55:05'),
(4, 1, 5, 'kjfkldslfkdslfdsl', '2017-05-01 17:55:35'),
(5, 6, 7, 'ok', '2017-05-05 15:51:20'),
(6, 6, 7, 'we shall', '2017-05-05 15:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `url`, `title`, `content`, `created_at`) VALUES
(1, 1, '', 'The Uganda', 'The Ugandan Facebook', '2017-04-28 02:08:21'),
(2, 1, '', 'A great Test', 'This is so interesting working with ionic2', '2017-04-30 11:51:12'),
(3, 1, '', 'Test', 'One two', '2017-04-30 11:59:37'),
(4, 1, '', 'This is wonderfull', 'Hello, Have you tried working with angular2?', '2017-04-30 12:01:08'),
(5, 1, '', 'Test one', 'One for testing', '2017-05-01 17:55:25'),
(6, 1, '', 'gf', 'ggggg', '2017-05-01 19:27:57'),
(7, 6, '', 'Malaria', 'sleep in mosquito nets', '2017-05-05 15:51:02');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `likeable_type` varchar(255) NOT NULL,
  `likeable_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `likeable_type`, `likeable_id`, `user_id`, `created_at`) VALUES
(1, 'post', 1, 2, '2017-04-18 02:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `reciever_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `chat_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `reciever_id`, `content`, `chat_id`, `status`, `created_at`) VALUES
(1, 2, 3, 'Hey', 1, 0, '2017-05-06 19:13:29');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `url`, `title`, `content`, `created_at`) VALUES
(1, 1, 'images/doc.jpg', 'man osobola?', 'man osobola? Gezaako', '2017-04-18 03:09:20'),
(2, 2, 'images/doc.jpg', 'The Ugandan Facebook', 'The Ugandan Facebook (byakuno.com)', '2017-04-18 04:06:17'),
(3, 2, 'images/doc.jpg', 'Hey', 'Hey is the message', '2017-04-18 07:06:03');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `service_type_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `url`, `service_type_id`, `created_at`) VALUES
(1, 'Annual and routine medical check up', 'Regular health exams and tests can help find problems \r\n                 before they start. They also can help find problems early, \r\n                 when your chances for treatment and cure are better. Which \r\n                 exams and screenings you need depends on your age, health and \r\n                 family history, and lifestyle choices such as what you eat,\r\n                 how active you are, and whether you smoke.', 'images/doc2.jpg', 2, '2017-05-03 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `service_types`
--

CREATE TABLE IF NOT EXISTS `service_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `service_types`
--

INSERT INTO `service_types` (`id`, `name`) VALUES
(1, 'Specialist Services and care'),
(2, 'School Packages');

-- --------------------------------------------------------

--
-- Table structure for table `specialists`
--

CREATE TABLE IF NOT EXISTS `specialists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `about` text NOT NULL,
  `majoring` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `specialists`
--

INSERT INTO `specialists` (`id`, `name`, `description`, `about`, `majoring`) VALUES
(1, 'Paediatrician', 'Doctor for children ', 'Paediatrician is mainly concerened with children', '<p>infections</p><br /><p>cough</p>'),
(2, 'Herbalist', 'local herbs', 'local herbs', 'local herbs');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(1, 'normal'),
(2, 'med'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `type_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `phone`, `password`, `fullname`, `email`, `address`, `type_id`, `created_at`) VALUES
(1, 'hsemix', '0701880228', 'hsemixk', 'Hamidouh Semix', 'semix.hamidouh@gmail.com', 'Najjanakumbi', 3, '2017-03-27 03:10:07'),
(2, 'mahad', '0785338727', 'hsemixk', 'Luswata Mahmood', 'lus@gmail.com', 'Wakiso Town', 2, '2017-04-02 00:00:00'),
(3, 'mukisa', '0877565656', 'hsemixk', 'Namukisa Sophie', 'sop@gmail.com', 'Munyonyo', 2, '2017-04-20 04:07:10'),
(5, 'hamnaj', '701880228', 'hsemixk', 'hamnaj', 'hamnaj@gmail.com', '', 1, '0000-00-00 00:00:00'),
(6, 'paul', '0776439250', 'paul0776', 'paul', 'd8paul@gmail.com', '', 1, '0000-00-00 00:00:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_post_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
