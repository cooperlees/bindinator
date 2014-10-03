-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 13, 2008 at 03:15 PM
-- Server version: 5.0.22
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dns`
--

-- --------------------------------------------------------

--
-- Table structure for table `A_RECORDS`
--

CREATE TABLE IF NOT EXISTS `A_RECORDS` (
  `hostname` varchar(40) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `zone` varchar(30) NOT NULL,
  `ext` tinyint(4) NOT NULL default '0' COMMENT 'Include in External View',
  `txt` varchar(40) default NULL COMMENT 'Internal TXT Record',
  `cat` varchar(40) NOT NULL COMMENT 'Category of System',
  `added` varchar(8) NOT NULL,
  `uname` varchar(3) NOT NULL,
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='BIND DNS A Records Table';

--
-- Dumping data for table `A_RECORDS`
--


-- --------------------------------------------------------

--
-- Table structure for table `CATEGORIES`
--

CREATE TABLE IF NOT EXISTS `CATEGORIES` (
  `name` varchar(30) NOT NULL COMMENT 'Valid ANSTO Zone FIle'
) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='System Type Categories';

--
-- Dumping data for table `CATEGORIES`
--


-- --------------------------------------------------------

--
-- Table structure for table `CNAMES`
--

CREATE TABLE IF NOT EXISTS `CNAMES` (
  `hostname` varchar(40) NOT NULL,
  `cname` varchar(40) NOT NULL,
  `zone` varchar(30) NOT NULL,
  `ext` tinyint(4) NOT NULL default '0',
  `added` text NOT NULL,
  `uname` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='BIND CNAME Records Table';

--
-- Dumping data for table `CNAMES`
--

