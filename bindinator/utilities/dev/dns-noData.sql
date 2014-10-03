-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2008 at 03:54 PM
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
  `id` int(11) NOT NULL auto_increment,
  `hostname` varchar(40) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `zone` varchar(30) NOT NULL,
  `ext` tinyint(4) NOT NULL default '0' COMMENT 'Include in External View',
  `txt` varchar(80) default NULL COMMENT 'Internal TXT Record',
  `cat` varchar(40) NOT NULL COMMENT 'Category of System',
  `lastmod` varchar(8) NOT NULL,
  `uname` varchar(6) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='BIND DNS A Records Table' AUTO_INCREMENT=0 ;


--
-- Table structure for table `CATEGORIES`
--

CREATE TABLE IF NOT EXISTS `CATEGORIES` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL COMMENT 'Valid ANSTO Zone File',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='System Type Categories' AUTO_INCREMENT=0 ;

--
-- Dumping data for table `CATEGORIES`
--

INSERT INTO `CATEGORIES` (`id`, `name`) VALUES
(1, 'server'),
(2, 'its-admin'),
(3, 'its-user'),
(4, 'user-server'),
(5, 'user-workstation'),
(6, 'printer'),
(7, 'network-equip'),
(8, 'other'),
(9, 'vm'),
(10, 'zones');

-- --------------------------------------------------------

--
-- Table structure for table `CNAMES`
--

CREATE TABLE IF NOT EXISTS `CNAMES` (
  `id` int(11) NOT NULL auto_increment,
  `hostname` varchar(40) NOT NULL,
  `cname` varchar(40) NOT NULL,
  `zone` varchar(30) NOT NULL,
  `ext` tinyint(4) NOT NULL default '0',
  `diffzone` tinyint(4) NOT NULL default '0' COMMENT 'Cname points to Hostname from different zone',
  `lastmod` varchar(8) NOT NULL,
  `uname` varchar(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='BIND CNAME Records Table' AUTO_INCREMENT=0 ;


--
-- Table structure for table `ZONES`
--

CREATE TABLE IF NOT EXISTS `ZONES` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `ichange` tinyint(4) NOT NULL default '0' COMMENT 'Internal Change',
  `echange` tinyint(4) NOT NULL default '0' COMMENT 'External Change',
  `fqname` varchar(40) NOT NULL COMMENT 'DNS Zone FQDN',
  `rev` tinyint(4) NOT NULL default '0' COMMENT 'Reverse Zone',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='Tables of Zones that are Dynamically Generated' AUTO_INCREMENT=0 ;
