#!/usr/bin/php
<?
####################################
# Cooper Lees - me@cooperlees.com
# Purpose: Install Bindinator Script
# Last Updated: 20080505
####################################

# Script to install Bindinator

# Function to update DB_USER and PASSWORD
function dbSettings($DB_USER, $DB_PASS, $DB_TYPE, $DB_HOST, $DB_NAME) {
	global $WEBDIR;
	$DBTEMP = "$WEBDIR/utilities/db.php.template";
#	$DBTEMP = "../db.php.template";

	if($dbFile = file($DBTEMP)) {
		$f = fopen("$WEBDIR/utilities/db.php", "w");
#		$f = fopen("../db.php", "w");
		foreach($dbFile as $line) {
			# Check for line of interest otherwise just print out
			if(ereg("^.*phptype.*", $line)) {
				fwrite($f, "\t'phptype' => '$DB_TYPE',\n");
			} 
			else if(ereg("^.*username.*", $line)) {
				fwrite($f, "\t'username' => '$DB_USER',\n");
			} 
			else if(ereg("^.*password.*", $line)) {
				fwrite($f, "\t'password' => '$DB_PASS',\n");
			} 
			else if(ereg("^.*hostspec.*", $line)) {
				fwrite($f, "\t'hostspec' => '$DB_HOST',\n");
			} 
			else if(ereg("^.*database.*", $line) && ($DB_TYPE == "mysql" || $DB_TYPE == "postgres")) {
				fwrite($f, "\t'database' => '$DB_NAME'\n");
			}
			else {
				fwrite($f, $line);
			}
		}
		fclose($f);
	} else {
		echo "!--> Unable to open and load $DBCONF\n"; 
		return false;
	}
}

# Set Up Database Tables
function setUpTables() {
	if($db = connectDB()) {
		# Create A_RECORDS Table
		$aQuery = "CREATE TABLE IF NOT EXISTS `A_RECORDS` (
		`id` int(11) NOT NULL auto_increment,
		`hostname` varchar(40) NOT NULL,
		`ip` varchar(15) NOT NULL,
		`zone` varchar(30) NOT NULL,
		`ext` tinyint(4) NOT NULL default '0' COMMENT 'Include in External View',
		`txt` varchar(80) default NULL COMMENT 'Internal TXT Record',
		`cat` varchar(40) NOT NULL COMMENT 'Category of System',
		`lastmod` varchar(8) NOT NULL,
		`uname` varchar(6) NOT NULL,
		PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='BIND DNS A Records Table' AUTO_INCREMENT=0";
		insertSet($db, $aQuery);

		# Create CNAMES Table
		$cQuery = "CREATE TABLE IF NOT EXISTS `CNAMES` (
		`id` int(11) NOT NULL auto_increment,
		`hostname` varchar(40) NOT NULL,
		`cname` varchar(40) NOT NULL,
		`zone` varchar(30) NOT NULL,
		`ext` tinyint(4) NOT NULL default '0',
		`diffzone` tinyint(4) NOT NULL default '0' COMMENT 'Cname points to Hostname from different zone',
		`lastmod` varchar(8) NOT NULL,
		`uname` varchar(6) NOT NULL,
		PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='BIND CNAME Records Table' AUTO_INCREMENT=0 ;";
		insertSet($db, $cQuery);

		# Create Categories Table + Default Populate
		$catQuery = "CREATE TABLE IF NOT EXISTS `CATEGORIES` (
		`id` int(11) NOT NULL auto_increment,
		`name` varchar(30) NOT NULL COMMENT 'Valid ANSTO Zone File',
		PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='System Type Categories' AUTO_INCREMENT=0 ;";
		insertSet($db, $catQuery);

		$catInsert = "INSERT INTO `CATEGORIES` (`id`, `name`) VALUES
		(1, 'server'),
		(2, 'its-admin'),
		(3, 'its-user'),
		(4, 'user-server'),
		(5, 'user-workstation'),
		(6, 'printer'),
		(7, 'network-equip'),
		(8, 'other'),
		(9, 'vm'),
		(10, 'zones')";
		insertSet($db, $catInsert);

		# Create ZONES Database
		$zQuery = "CREATE TABLE IF NOT EXISTS `ZONES` (
		`id` int(11) NOT NULL auto_increment,
		`name` varchar(40) NOT NULL,
		`ichange` tinyint(4) NOT NULL default '0' COMMENT 'Internal Change',
		`echange` tinyint(4) NOT NULL default '0' COMMENT 'External Change',
		`fqname` varchar(40) NOT NULL COMMENT 'DNS Zone FQDN',
		`rev` tinyint(4) NOT NULL default '0' COMMENT 'Reverse Zone',
		`class` varchar(1) default NULL,
		PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=ascii COMMENT='Tables of Zones that are Dynamically Generated' AUTO_INCREMENT=0 ;";
		insertSet($db, $zQuery);

		echo "--> Database Tables Created. Please reduce the database users privledges.\n";
	} else {
		echo "!--> ERROR: Unable to connect to your Database.\n";
		return false;
	}
	disconnectDB($db);
	return true;
}

# Function for getting a valid yes or no response
function yesNoCheck() {
	while(true) {
		fscanf(STDIN, "%s\n", $input);
		if(eregi("^y", $input)) {
			return true;
		} else if(eregi("^n", $input)) {
			return false;
		} 
		echo " - Invalid Input. Yes or No (y/n): ";
	}
}

### MAIN Program ###
$VERSION="0.6 beta";

# Global Vars
$APACHE_USER="apache";
$DATADIR="/var/bindinator";
$DB_USER="root";
$DB_HOST="localhost";
$WEBDIR="/var/www/html/bindinator/";

echo "
-- Bindinator $VERSION Setup --
- Cooper Lees - me@cooperlees.com

This program will ask you a series of questions and then go off and create:
- Bindiator Web Dir
- Bindinator Data Dir (/var/bindinator default)
- Database Tables 
	- The DB User will need to be able to create tables to start with!
- Default configuration

Please enter the data for the following:
- A blank entry will select the default option

";

# Data Aquisition
# Web Dir
echo "Web Dir [Full Path Please] (Default = $WEBDIR): ";
fscanf(STDIN, "%s\n", $wdInput);
if(ereg("^/.+", $wdInput)) {
	$WEBDIR = $wdInput;
}
if(!file_exists($WEBDIR)) {
	echo" --> $WEBDIR does not exist, Create It ? (y/n): ";
	$cWebDir = yesNoCheck();
}

# Data Dir
echo "Data Dir (Default = $DATADIR): ";
fscanf(STDIN, "%s\n", $ddInput);
if(ereg("^/.+", $ddInput)) {
	$DATADIR = $ddInput;
}
if(!file_exists($DATADIR)) {
	echo " --> $DATADIR does not exist, Create It ? (y/n): ";
	$cDataDir = yesNoCheck();
}

# Apache User
echo "Enter the USER apache runs as (Default = $APACHE_USER): ";
fscanf(STDIN, "%s\n", $apInput);
if(ereg("^[[:alpha:]]{1,40}", $apInput)) {
	$APACHE_USER=strtolower($apInput);
} else if($apInput != "") {
	echo "!--> Invalid Apache User - Continuting with default: $APACHE_USER\n";
}

### Do The Install ###

# 1
# Copy webdir to $WEBDIR
if($cWebDir) {
	if(!mkdir($WEBDIR)) {
		echo "!--> Failed to make $WEBDIR - Exiting\n";
		exit(1);
	}
}
# Call cp for copy of data
system("cp -rp bindinator/* $WEBDIR", $retVal);
if($retVal != 0) {
	echo "!--> FAILED to copy webdir to $WEBDIR - Exiting\n";
	exit(1);
}
echo "\t--> Created Web Dir @ $WEBDIR\n";

# 2
# Create and copy stuff to $DATADIR
if($cDataDir) {
	if(!mkdir($DATADIR)) {
		echo "!--> Failed to make $DATADIR - Exiting\n";
		exit(1);
	}
}
# Call cp for copy of data
system("cp -rp templates $DATADIR", $retVal);
if($retVal != 0) {
	echo "!--> FAILED to copy webdir to $DATADIR - Exiting\n";
	exit(1);
}
$zDir = $DATADIR."/zones";
$rzDir = $DATADIR."/revzones";
if(!mkdir($zDir)) {
	echo "!--> Failed to make $zDir - Exiting\n";
	exit(1);
}
if(!mkdir($rzDir)) {
	echo "!--> Failed to make $rzDir - Exiting\n";
	exit(1);
}
echo "\t--> Created Data Dir @ $DATADIR\n";

# Shell Script to fix Permissions
system("./setBindinatorPerms.bash $APACHE_USER $WEBDIR $DATADIR");

# 3
# Create Database tables etc.
# DB User
echo "Enter Database User for Bindinator (Default = $DB_USER): ";
fscanf(STDIN, "%s\n", $dbInput);
if(ereg("^[[:alpha:]]{1,40}", $dbInput)) {
	$DB_USER=strtolower($dbInput);
} else if($dbInput != "") {
	echo "!--> Invalid DB Username - Continuting with default: $DB_USER\n";
}
# Get DB Password to allow only enter once
echo "DB Password (please clear your shell history after install): ";
fscanf(STDIN, "%s\n", $DB_PASS);
if(strlen($DB_PASS) > 50) {
	echo "--> DB password suspeciously long ...\n";
}
# Get DB Type from 
echo "DB Type (mysql, oracle or postgres): ";
while(true) {
	fscanf(STDIN, "%s\n", $DB_TYPE);
	$DB_TYPE = strtolower($DB_TYPE);
	if($DB_TYPE == "mysql" || $DB_TYPE == "oracle" || $DB_TYPE == "postgres") {
		break;
	} else {
		echo " - Invalid DB Type - reenter (mysql, oracle or postgres): ";
	}
}
# If MYSQL get the DB_NAME
if($DB_TYPE == "mysql" || $DB_TYPE == "postgres") {
	echo "Database Name: ";
	while(true) {
		fscanf(STDIN, "%s\n", $DB_NAME);
		if(ereg("^[[:alpha:]]{1,40}", $DB_NAME)) {
			break;
		} else {
			echo " - Invalid DB Name - reenter: ";
		}
	}
}
# Get DB Host
echo "DB Host [fqdn or IP]: (Default = $DB_HOST): ";
fscanf(STDIN, "%s\n", $dbHostInput);
if(ereg("^.+", $dbHostInput)) {
	$DB_HOST = $dbHostInput;
} else if($dbHostInput != "") {
	echo " - Invalid DB Host - Will continue with $DB_HOST\n";
}

# Modify functions.php to have correct DB user and password
$funcFile = $WEBDIR."/utilities/functions.php";
#$funcFile = "../functions.php";
dbSettings($DB_USER, $DB_PASS, $DB_TYPE, $DB_HOST, $DB_NAME);

# Include Global Functions
include("$funcFile");
if(!setUpTables()) {
	echo "!--> Error with Database Setup - Exiting\n";
	exit(1);
}
echo "--> Database Setup complete. Please check categories and zones from Admin Web Interface.\n";

# Echo completion Ouput - User must configure Apache
echo "
--> Bindinator Setup Complete:
	- Webfiles located:	$WEBDIR
	- Data Dir located:	$DATADIR
	- DB User:		$DB_USER
	- DB Type:		$DB_TYPE
	- Apache User:		$APACHE_USER
 
-- POST INSTALLATION NOTES --

--> You will be required to configure apache. A form of basic auth (e.g. Flat File or LDAP) must be applied to $WEBDIR.
---> Check the utilities/conf folder for an example config.
!--> *Remember* each zone you add requires and internal and external zone template

-----------------------------------
Enjoy Using Bindinator,
Any questions please feel free to contact the Bindinator Team.

http://bindinator.sourceforge.net/
- Be active in our mailing lists and Forum !\n";
?>

