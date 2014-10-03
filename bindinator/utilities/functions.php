<?
####################################
# Cooper Lees - me@cooperlees.com
# Purpose: Hold common functions for
# BIND Database Zone File Webapp
# Last Updated: 20080421
####################################

# Include Pear DB Class
require_once 'MDB2.php';

# Database Variables
include 'db.php';

#####################################################
# Database Functions
#####################################################
# Connect to DB - return the Instance - Must be checked for Error.
function connectDB() {
	global $dsn;
	// Connect Using PEAR MDB2 Class
	$options = array('debug' => 2, 'portability' => MDB2_PORTABILITY_ALL);
	$mdb2 =& MDB2::connect($dsn, $options);
	if (PEAR::isError($mdb2)) {
		writeToLog("!--> DB Connect ERROR: ".$mdb2->getMessage());
		return false;
	} else {
//		fwrite(STDERR, "--> Got a connection - returning\n"); #DEBUG
		$mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC); // set the default fetchmode
		return $mdb2;
	}
}

# Wrapper Disconnect DB
function disconnectDB($aMdb2) {
	return $aMdb2->disconnect();
}

# Select Statement Function
# Return a Iteratable result set
function getResSet($db, $query) {
	MDB2::loadFile('Iterator');
	$res = $db->query($query, true, true, 'MDB2_BufferedIterator');
	if(PEAR::isError($res)) {
		writeToLog("!--> ERROR: Problem with query '$query': ".$res->getMessage());
		return false;
	} else { return $res; }
}

# Return a Non Buffered Iterator Res
function getRes($db, $query) {
	$res = $db->query($query);
	if(PEAR::isError($res)) {
		writeToLog("!--> ERROR: Problem with query '$query': ".$res->getMessage());
		return false;
	} else { return $res; }
}

# Insert Statement Function
function insertSet($db, $query) {
	$affected =& $db->exec($query);
	if (PEAR::isError($affected)) {
		writeToLog("!--> ERROR: Problem with an insert: ".$affected->getMessage());
		return false;
	}
	return $affected;
}

#####################################################
# Utility Functions
# - Included from other functions
#####################################################
# Check for Illegal Chars
function checkIllegalChars($string) {
	$ret = true;
	if(ereg(";", $string)) { $ret = false; }
	if(ereg('"', $string)) { $ret = false; }
	if(ereg("'", $string)) { $ret = false; }
	return $ret;
}

# Output CNAME Error
function cnameError($res) {
	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
	echo "<p align=\"center\">".$_GET['cname']." already exists in zone ".$_GET['zone']."<br \>\n";
	echo "CNAME points @ ".$row['hostname']." - ".$row['lastmod']." used by ".$row['uname']."</p>\n";
	printLinks();
}

# Make sure CNAME does not exist - returns the Res Set to output to user ...
function cnameExists($db, $cname, $zone, $verbose=false) {
	$query = "SELECT * from CNAMES WHERE cname = '$cname'
	AND zone = '$zone'";
	$res = $db->query($query);
	if(PEAR::isError($res)) {
		handleError("cnametExists", $res, $verbose);
		return false;
	} else if($res->numRows() == 1) {
		return $res;
	}
	return false; // e.g. if 2 rows returned - Someting weird if get here
}
# Delete a Cname from a Zone
function delCname($db, $cname, $zone) {
	$cQuery = "DELETE FROM CNAMES WHERE 
	cname = '$cname' AND
	zone = '$zone'";
	
	if($affected = insertSet($db, $cQuery)) {
		echo "<p align=\"center\">CNAME $cname has been deleted.<br \>\n";
		echo "DNS will be updated during the next scheduled generation of zone files.</p>\n";
		# Write to Log
		writeToLog("--> Deleted CNAME $cname in zone $zone from the CNAMES table by ".$_SERVER['PHP_AUTH_USER'], false);
		printLinks();
	} else { # Insert Failure
		echo "<p>!--> ERROR deleting cname from DB. Please refer to logfile or alert administrator.</p>\n";
		writeToLog("!--> ERROR deleting $cname cname from zone $zone from the CNAMES table");
		printLinks();
	}
}

# Delete Host from Table and CNAMES pointing to hostname
function delHost($db, $host, $zone) {
	$hQuery = "DELETE FROM A_RECORDS WHERE 
	hostname = '$host' AND
	zone = '$zone'";
	$cQuery = "DELETE FROM CNAMES WHERE 
	hostname = '$host' AND
	zone = '$zone'";
	
	$cAffected = insertSet($db, $cQuery);
	if($cAffected > 0 ) {
		echo "<p>$cAffected number of CNAMES were deleted.</p>\n";
		writeToLog("--> Deleted $cAffected CNAMES that point to $host in zone $zone", false);
	}
	if($affected = insertSet($db, $hQuery)) {
		echo "<p align=\"center\">Host ".$_GET['hostname']." has been deleted.<br \>\n";
		echo "DNS will be updated during the next scheduled generation of zone files.</p>\n";
		# Write to Log
		writeToLog("--> Deleted ".$_GET['hostname']." in zone ".$_GET['zone']." from the A_RECORDS table and all CNAMES by ".$_SERVER['PHP_AUTH_USER'], false);
		printLinks();
	} else { # Insert Failure
		echo "<p>!--> ERROR deleting host from DB. Please refer to logfile or alert administrator.</p>\n";
		writeToLog("!--> ERROR deleting ".$_GET['hostname']." in zone ".$_GET['zone']." from the A_RECORDS table");
		printLinks();
	}
}

// Get the FQDN of a zone from it's short name
function getZoneFQDN($db, $zone) {
	$query = "SELECT fqname FROM ZONES WHERE name = '$zone'";
	$zRes = getRes($db, $query);
	$zRow = $zRes->fetchRow(MDB2_FETCHMODE_ASSOC);
	if(isset($zRow['fqname'])) {
		return $zRow['fqname'];
	} else { return false; }
}

# Check if a fqdn
function isFQDN($host) {
	if(ereg("^.+\..+", $host)) {
		return 1;
	}
	return 0;
}

# Generic Handiling of Error - One place to update for all functions
function handleError($func, $res, $verbose=false) {
	if($verbose) { fwrite(STDERR, $res->getMessage()."\n"); }
	writeToLog("!--> ERROR: In $func - ".$res->getMessage(), true);
}

# Error to output if host already used ...
function hostError($res) {
	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
	echo "<p align=\"center\">".$_GET['hostname']." already exists in zone ".$_GET['zone']."<br \>\n";
	echo $row['ip']." - ".$row['txt']." - ".$row['lastmod']." used by ".$row['uname']."</p>\n";
	printLinks();
}

# Make sure host does not exist - returns the Res Set to output to user ...
function hostExists($db, $host, $zone, $verbose=false) {
	$query = "SELECT * from A_RECORDS WHERE hostname = '$host'
	AND zone = '$zone'";
	$res = $db->query($query);
	if(PEAR::isError($res)) {
		handleError("hostExists", $res, $verbose);
		return false;
	} else if($res->numRows() == 1) {
		return $res;
	}
	return false; // e.g. if 2 rows returned or 0
}

# Output if ipInUser Error
function ipError($res) {
	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
	echo "<p align=\"center\">".$_GET['ip']." already in use. <br />\n";
	echo $row['hostname']." in zone ".$row['zone']." already uses this IP.</p>\n";
	printLinks();
}

# Check if the IP alreday exists in DB
function ipInUse($db, $ip, $verbose=false) {
	$query = "SELECT * from A_RECORDS WHERE ip = '$ip'";
	$res = $db->query($query);
	if(PEAR::isError($res)) {
		handleError("hostExists", $res, $verbose);
		return false;
	} else if($res->numRows() == 1) {
		return $res;
	}
	return false; // e.g. if 2 rows returned - Someting weird if get here
}

# Function to add or update cname
function modCname($db, $diffZone, $edit=false) {
	$date = date("Ymd");
	# Mod Query to UPDATE rather than INSERT
	if($edit) {
		$query = "UPDATE CNAMES SET
			hostname = '".$_GET['hostname']."',
			ext = ".$_GET['ext'].",
			diffzone = ".$diffZone.",
			lastmod = '".$date."',
			uname = '".$_GET['username']."'
			WHERE cname = '".$_GET['cname']."' AND
			zone = '".$_GET['zone']."'";
	} else {
		$query = "INSERT INTO CNAMES (cname,hostname,zone,ext,diffzone,lastmod,uname)
			VALUES ( 
			'".$_GET['cname']."',
			'".$_GET['hostname']."',
			'".$_GET['zone']."',
			".$_GET['ext'].",
			".$diffZone.",
			'".$date."',
			'".$_GET['username']."')";
	} 
	# DB Manipulation Time
	if($affected = insertSet($db, $query)) {
		# UPDATE THAT THERE IS CHANGE IN DB FOR ZONE
		setChange($db, $_GET['zone'], false, false);
		# Check if Edit has changed External Status or New and needs External
		if(($edit && ($_GET['ext'] != $_GET['oldext'])) || (!$edit && $_GET['ext'] == 1)) {
			setChange($db, $_GET['zone'], true, false);
		}
#		echo "<p>$affected number of rows added in CNAMES table.</p>\n"; #DEBUG ?
		echo "<p align=\"center\">CNAME <a href=\"searchCname.php?cname=".$_GET['cname']."\">".$_GET['cname']."</a>
		 that points @ host <a href=\"searchHost.php?hostname=".$_GET['hostname']."\">".$_GET['hostname']."</a>
		 has been modified/added.<br \>\n";
		echo "DNS will be updated during the next scheduled generation of zone files.</p>\n";
		# Write to Log
		writeToLog("--> Added/Modified CNAME ".$_GET['cname']." to point @ host ".$_GET['hostname']." in ".$_GET['zone']." zone by user ".$_SERVER['PHP_AUTH_USER']);
		printLinks();
		//
	} else { # Insert Failure
		echo "<p>!--> ERROR inserting cname into DB. Please refer to logfile or alert administrator.</p>\n";
		printLinks();
	}
}

# Function to add or update host
function modHost($db, $edit=false) {
	$date = date("Ymd");
	# Mod Query to UPDATE rather than INSERT
	if($edit) {
		$query = "UPDATE A_RECORDS SET
			hostname = '".$_GET['hostname']."',
			zone = '".$_GET['zone']."',
			ext = ".$_GET['ext'].",
			txt = '".$_GET['txt']."',
			cat = '".$_GET['cat']."',
			lastmod = '".$date."',
			uname = '".$_GET['username']."'
			WHERE ip = '".$_GET['ip']."' AND
			hostname = '".$_GET['oldHostname']."'";
	} else {
		$query = "INSERT INTO A_RECORDS (hostname,ip,zone,ext,txt,cat,lastmod,uname)
			VALUES ( 
			'".$_GET['hostname']."',
			'".$_GET['ip']."',
			'".$_GET['zone']."',
			".$_GET['ext'].",
			'".$_GET['txt']."',
			'".$_GET['cat']."',
			'".$date."',
			'".$_GET['username']."')";
	} 
	# DB Manipulation Time
	if($affected = insertSet($db, $query)) {
		# UPDATE THAT THERE IS CHANGE IN DB FOR ZONE
		setChange($db, $_GET['zone'], false, false);
		# Check if Edit has changed External Status or New and needs External
		if(($edit && ($_GET['ext'] != $_GET['oldext'])) || (!$edit && $_GET['ext'] == 1)) {
			setChange($db, $_GET['zone'], true, false);
		}
#		echo "<p>$affected number of rows in A_RECORDS.</p>\n"; #DEBUG ?
		echo "<p align=\"center\">Host <a href=\"searchHost.php?hostname=".$_GET['hostname']."\">".$_GET['hostname']."</a> has been modified/added.<br \>\n";
		echo "DNS will be updated during the next scheduled generation of zone files.</p>\n";
		# Write to Log
		writeToLog("--> Added/Modified ".$_GET['hostname']." in zone ".$_GET['zone']." to point to ".$_GET['ip']." by user ".$_SERVER['PHP_AUTH_USER']);
		printLinks();
	} else { # Insert Failure
		echo "<p>!--> ERROR inserting host into DB. Please refer to logfile or alert administrator.</p>\n";
		printLinks();
	}
}

# Check all required Vars Set in Form Submission
function checkIfSet($toCheck) {
	$retVal = true;
	foreach ($toCheck as $aVar) {
		if(!isset($_GET[$aVar]) || $_GET[$aVar] == "") {
			echo "<p>!--> ERROR: Problem with $aVar - (".$_GET[$aVar].") in the form submission. Please check.</p>\n";
			$retVal = false;
		}
	}
	return $retVal;
}

// Print Zones Select List
function printZones($db) {
	$query = "SELECT * FROM ZONES WHERE rev = 0 ORDER BY fqname";
	$res = getResSet($db, $query);
	foreach($res as $aRes) {
		echo "            <option value=\"".$aRes['name']."\">".$aRes['fqname']."</option>\n";
	}
}

// Set if Zone has changes or not
function setChange($db, $zoneName, $ext=false, $verbose=false) {
	if($ext) { $query = "UPDATE ZONES SET echange = 1 WHERE name = '$zoneName'"; }
	else { $query = "UPDATE ZONES SET ichange = 1 WHERE name = '$zoneName'"; }
	insertSet($db, $query); #Need to check error here - Function does check for error tho 
}

// Set Zone Updated - change to 0
function setUpdated($db, $id, $zoneName, $ext=false, $verbose=false) {
	if($ext) { $query = "UPDATE ZONES SET echange = 0 WHERE id = $id"; }
	else { $query = "UPDATE ZONES SET ichange = 0 WHERE id = $id"; }
	insertSet($db, $query); #Need to check error here - Function does check for error tho
}

##############################
# FORM Validation Functions
##############################
# Validate Data for selecting a cname to edit
function validateCnameToEdit() {
	global $db;
	$retVal = true;
	
        if(!validHostname($_GET['cnameToEdit'])) {
                echo "<p>".$_GET['cnameToEdit']." is not a valid cname Please correct.</p>\n";
                $retVal = false;
        }
	if(!validZone($db, $_GET['zone'], false)) {
                echo "<p>".$_GET['zone']." is not a valid zone for Bindinator. Please correct.</p>\n";
                $retVal = false;
        }
	return $retVal;
}

# Validate Data for selecting a host to edit
function validateHostToEdit() {
	global $db;
	$retVal = true;
	
        if(!validHostname($_GET['hostToEdit'])) {
                echo "<p>".$_GET['hostToEdit']." is not a valid hostname. Please correct.</p>\n";
                $retVal = false;
        }
	if(!validZone($db, $_GET['zone'], false)) {
                echo "<p>".$_GET['zone']." is not a valid zone for Bindinator. Please correct.</p>\n";
                $retVal = false;
        }
	return $retVal;
}

# Function to check all data in add Host request
function validateAddData() {
	global $db;
	$retVal = true;

	if(!validHostname($_GET['hostname'])) {
		echo "<p>".$_GET['hostname']." is not a valid hostname. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validIP($_GET['ip'])) {
		echo "<p>".$_GET['ip']." is not a valid ip address. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validZone($db, $_GET['zone'], false)) {
		echo "<p>".$_GET['zone']." is not a valid zone for Bindinator. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validCategory($db, $_GET['cat'], false)) {
		echo "<p>".$_GET['cat']." is not a valid category for Bindinator. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validExt($_GET['ext'])) {
		echo "<p>".$_GET['ext']." is not a valid value for external view for Bindinator. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validTXT($_GET['txt'])) {
		echo "<p>".$_GET['txt']." is not a valid TXT record for Bindinator. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validUser($_GET['username'])) {
		echo "<p>".$_GET['username']." is not a valid username. Please correct.</p>\n";
		$retVal = false;
	}
	return $retVal;
}

# Check all data for CNAME manipulation
function validateCnameData() {
	global $db;
	$retVal = true;

	if(!validHostname($_GET['hostname'])) {
		echo "<p>".$_GET['hostname']." is not a valid hostname. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validHostname($_GET['cname'])) {
		echo "<p>".$_GET['cname']." is not a valid cname. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validZone($db, $_GET['zone'], false)) {
		echo "<p>".$_GET['zone']." is not a valid zone for Bindinator. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validExt($_GET['ext'])) {
		echo "<p>".$_GET['ext']." is not a valid value for external view for Bindinator. Please correct.</p>\n";
		$retVal = false;
	}
	if(!validUser($_GET['username'])) {
		echo "<p>".$_GET['username']." is not a valid username. Please correct.</p>\n";
		$retVal = false;
	}
	return $retVal;
}

# Check if a Valid category specified
function validCatName($cat) {
	# Check if a string of legit size and chars
	# Simple Check to stop SQL Attacks and for illegal chars
	if(ereg("^[[:alpha:]]{1,30}", $cat)) { 
		return checkIllegalChars($cat); 
	}
	return false; 
}

# Check if a Valid Category
function validCategory($db, $cat, $verbose=false) {
	$query = "SELECT * from CATEGORIES WHERE name = '$cat'";
	$res = $db->query($query);
	if(PEAR::isError($res)) {
		handleError("validCategory", $res, $verbose);
		return false;
	} else if($res->numRows() == 1) {
		return true;
	}
	return false; // e.g. if 2 rows returned - Someting weird if get here
}

# Check if valid Int for EXTERNAL field
function validExt($ext) {
	if($ext < 2 && $ext > -1) {
		return true;
	}
	return false;
}

# Check if a Valid Hostname / Cname Specified
function validHostname($host) {
	# Check if a string of legit size and chars
	# Simple Check to stop SQL Attacks and for illegal chars
	if(ereg("^[[:alpha:]]{1,40}", $host)) { 
		return checkIllegalChars($host); 
	}
	return false; 
}

# Check if a Valid IP Address
function validIP($ip) {
	$aIPReg = "^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}";
	if(!ereg($aIPReg, $ip)) {
		return false;
	} else { return true; }
}

# Check if a Valid Hostname / Cname Specified
function validTXT($txt) {
	# Check if a string of legit size and chars
	# Simple Check to stop SQL Attacks and for illegal chars
	if(ereg("^[[:alpha:]]{1,80}", $txt)) { 
		return checkIllegalChars($txt); 
	}
	return false;
}

# Check valid username
function validUser($user) {
	if(ereg("^[[:alpha:]]{2,6}", $user)) {
		return true;
	}
	return false;
}

# Check if Valid Zone - bool
function validZone($db, $zoneName, $verbose=false) {
	$query = "SELECT * from ZONES WHERE name = '$zoneName'";
	$res = $db->query($query);
	if(PEAR::isError($res)) {
		handleError("validZone", $res, $verbose);
		return false;
	} else if($res->numRows() == 1) {
		return true;
	}
	return false; // e.g. if 2 rows returned - Someting weird if get here
}

# Write a line out to syslog
function writeToLog($logline, $err=true) {
	# Get Syslog Constants
	define_syslog_variables();
	# Open a connection to Syslog
	if(openlog("[bindinator]", LOG_ODELAY, LOG_LOCAL0)) {
		if($err) {
			syslog(LOG_ERR, trim($logline));
		} else {
			syslog(LOG_INFO, trim($logline));
		}
	} 
	# Close connection to Syslog
	closelog();
}

# Return No or Yes for 0 or X
function yesNo($int) {
	if ($int == 0) { return "No"; } 
	else { return "Yes"; }
}
?>
