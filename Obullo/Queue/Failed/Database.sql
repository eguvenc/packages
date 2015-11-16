-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 19, 2014 at 10:37 AM
-- Server version: 5.5.34-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `q_jobs`
--

-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `failed`;

USE `failed`;

--
-- Table structure for table `failures`
--
CREATE TABLE IF NOT EXISTS `failures` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`job_id`  int(11) NOT NULL ,
`job_name`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`job_body`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`job_attempts`  int(11) NOT NULL DEFAULT 0 ,
`error_level`  tinyint(3) NOT NULL ,
`error_message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_file`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_line`  int UNSIGNED NOT NULL ,
`error_trace`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_xdebug`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`error_priority`  tinyint(4) NOT NULL ,
`failure_repeat`  int(11) NOT NULL DEFAULT 0 ,
`failure_first_date`  int(11) NOT NULL COMMENT 'unix timestamp' ,
`failure_last_date`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='Failed Jobs'
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;