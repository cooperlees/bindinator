#!/usr/bin/php
<?
####################################
# Cooper Lees - me@cooperlees.com
# Purpose: Generate a BIND Zone File
# - Append to a ZONENAME.zone.template
# Last Updated: 20080503
####################################

# Generates a BIND Zone File for A Records,
# CNAMES and TXT records only.

# Include Global Functions
include('./functions.php');
# Include Classes
include './classes/a_record.php';
include './classes/cname.php';

// Copy Template
// Copy zone template to ODIR
function copyTemplate($zone) {
	global $EXT, $ODIR, $OFILE, $REV, $TDIR, $ZFILE;

	# Destination
	if($EXT) {
		$TFILE = $TDIR."/".$zone."-ext.zone.template";
		$ZFILE = $zone."-ext.zone";
	} else {
		$TFILE = $TDIR."/".$zone.".zone.template";
		$ZFILE = $zone.".zone";
	}
	# Different Folder for reverse Files ...
	if($REV) {
		$OFILE = $ODIR."/revzones/".$ZFILE;
	} else {
		$OFILE = $ODIR."/zones/".$ZFILE;
	}

	if(file_exists($TFILE)) {
		// Update Zone Template Serial - Use of External Perl Script - Should write func.
		exec("./update-dns-serial.pl $TFILE", $output, $retVal);
		if($retVal) {
			writeToLog("!--> Error: Error with updating $TFILE zone serial");
		}
		if(copy($TFILE, $OFILE)) {
			return true;
		} else {
			writeToLog("!--> Error: Can't copy $TFILE to $OFILE.", true);
			return false;
		}
	} else {
		writeToLog("!--> Error: Can't find $TFILE.", true);
		return false;
	}
}

// Open Zone File for appending
function openZoneFile() {
	global $OFILE;
	if(file_exists($OFILE)) {
		if($f = fopen($OFILE, 'a+')) {
			return $f; //Return File Handle
		} else {
			writeToLog("!--> Error: Fopen error on $OFILE", true);
			return false;
		}
	} else {
		writeToLog("!--> Error: $OFILE does not exist", true);
		return false;
	}
}

// Category Header Printing
function writeCatHeader($f, $cat) {
	// Read in from files files with Header
	$headFile = "conf/catHeaders/$cat.txt";
	if(file_exists($headFile)) {
		$header = file($headFile);
		foreach ($header as $headLine) {
			fwrite($f, $headLine);
		}
	} else {
		writeToLog("!--> Can not open $headfile", true);
	}
}

// Write to file the CNAME
function writeAcname($f, $row) {
	if(strlen($row['cname']) < 8) {
		$line = $row['cname']."\t\tIN\tCNAME\t".$row['hostname']."\n";
	} else {
		$line = $row['cname']."\tIN\tCNAME\t".$row['hostname']."\n";
	}
	fwrite($f, $line);
}

// Check if host Has Cname
function writeHostCnames($f, $host) {
	global $db, $EXT;
	
	if($EXT) {
		$query="SELECT * FROM CNAMES WHERE hostname='".$host."' AND ext=1 ORDER BY cname";
	} else {
		$query="SELECT * FROM CNAMES WHERE hostname='".$host."' ORDER BY cname";
	}
	$res = $db->query($query, true, true, 'MDB2_BufferedIterator');
        if(PEAR::isError($res)) {
                writeToLog("writeHostCname: ".$res->getMessage(), true);
                return false;
        }
	foreach($res as $row) {
		writeAcname($f, $row);
	}
}

// Append to Zone File
function writeArec($f, $row) {
	global $EXT;
	// Write out the Arecord
	if(strlen($row['hostname']) < 8) {
		$line = $row['hostname']."\t\tIN\tA\t".$row['ip']."\n";
	} else {
		$line = $row['hostname']."\tIN\tA\t".$row['ip']."\n";
	}
	fwrite($f, $line);
	// Write out the TXT - if Internal
	if(!$EXT) {
		if($row['txt'] != "") {
			fwrite($f, "\t\tIN\tTXT\t\"".$row['txt']."\"\n");
		}
	}
	writeHostCnames($f, $row['hostname']);
	// Seperate Hosts with ;
	fwrite($f, ";\n");
}

// Write Cnames to hosts in other Zones
function writeCnames($f, $zone) {
	global $db, $EXT;

	// Write Category Header
	writeCatHeader($f, "cname");

	if($EXT) {
		$query = "SELECT * FROM CNAMES WHERE 
			zone = '".$zone."' AND
			diffzone = 1 AND ext=1 ORDER BY cname";
	} else {
		$query = "SELECT * FROM CNAMES WHERE 
			zone = '".$zone."' AND
			diffzone = 1 ORDER BY cname";
	}
	$res = $db->query($query, true, true, 'MDB2_BufferedIterator');
        if(PEAR::isError($res)) { 
		writeToLog("!--> Error getting CNAMES: ".$res->getMessage(), true); 
		return false;
	} else {
		foreach($res as $row) {
			writeAcname($f, $row);
		}
	}
	return true;
}

# Generate a query for each Category
# per zone - Return the Result Set
function genQuery($zone, $category) {
	global $db, $EXT;

	if($EXT) {
		$query = "SELECT * FROM A_RECORDS WHERE zone='".$zone."'
		AND cat='".$category."' AND ext=1 ORDER BY hostname";
	} else {
		$query = "SELECT * FROM A_RECORDS WHERE zone='".$zone."'
		AND cat='".$category."' ORDER BY hostname";
	}
	return getResSet($db, $query);
}

// Pull all valid zones out of DB -
// Generate File for each in $ODIR
function genZoneFiles() {
	global $db, $EXT, $tDir, $VERBOSE;

	# Load iterator implmentations
	MDB2::loadFile('Iterator');

	# Queries for Required Data from DB
	# Get all ZONES to Generate
	$zNameQuery="SELECT * FROM ZONES WHERE rev = 0 ORDER BY name";
	# Get all CATEGORIES for Zone File Organisation
	$catQuery = "SELECT * FROM CATEGORIES ORDER BY name";
	
	# Get Query Iteratable Results
	$zRes = getResSet($db, $zNameQuery);
	$cRes = getResSet($db, $catQuery);

	# Generate for each Zone
	foreach($zRes as $zRow) {
		$zone = $zRow['name'];
		if($EXT) { 
			$change = $zRow['echange']; 
			$startOutput = "\t--> Started writing external zone file for $zone zone ...\n";
		} else { 
			$change = $zRow['ichange']; 
			$startOutput = "\t--> Started writing zone file for $zone zone ...\n";
		}
		if($change) { # Checking if zone has had changes
			if($VERBOSE) { fwrite(STDERR, $startOutput); }
			writeToLog(trim($startOutput), false);
			if(copyTemplate($zone) && $f = openZoneFile()) {
				writeCnames($f, $zone); # Write Cnames the point @ other zones
				foreach($cRes as $cRow) { # Publish Hosts each Category
					$cat = $cRow['name'];
					writeCatHeader($f, $cat);
					if($hRes = genQuery($zone, $cat)) {
						foreach($hRes as $row) {
							writeArec($f, $row);
						}
					} else {
						writeToLog("!--> Error: Unable to get a resultset for $cat in $zone zone.", true);
					} 
				}
				$finZoneOutput = "\t--> Finished writing zone file for $zone zone\n";
				if($VERBOSE) { fwrite(STDERR, $finZoneOutput); }
				writeToLog(trim($finZoneOutput), false);
				# Set Zone to No change
				setUpdated($db, $zRow['id'], $zRow['name'], $EXT, $VERBOSE);
			} else {
				writeToLog("!--> Error with copying template or opening copied template for zone $zone.", true);
			}
		} else {
			if($EXT) { #DEBUG - Can turn off if wish ...
				$noChangeOut = "\t- No external changes on the $zone zone.\n";
			} else {
				$noChangeOut = "\t- No changes on the $zone zone.\n";
			}
			writeToLog(trim($noChangeOut), false);
			if ($VERBOSE) { fwrite(STDERR, $noChangeOut); }
		}
	}
	return true;
}

// Write out the Reverse File
function writeRevZone($db, $CLASS, $NET, $f) {
	// Generate for each Class C Address Range - Modify for your Subnets
	$retVal = true; //Indicate if no Errors ...

	# Split Network Address Up
	$net = split("\.", $NET, 4);
	$sMask = "255.255.255.0";

	if($CLASS == "b") {
		$start = 0;
		$end = 256;
	} else if($CLASS == "c") {
		$start = $net[2];
		$end = $net[2] + 1;
	} else {
		$err = "!--> Error: Invalid Class Specified";
		writeToLog("$err");
		return false;
	}

	fwrite($f, "; Reverse file for $NET\n");
	fwrite($f, "; - Fixed addresses only - DHCP addresses included from another file\n");

	# Interate for all Class C Address Ranges
	for($i = $start; $i < $end; $i++) {
		fwrite($f, ";\n");
		fwrite($f, "; Network Range ".$net[0].".".$net[1].".$i.0/$sMask\n");
		fwrite($f, "\$ORIGIN $i.".$net[1].".".$net[0].".in-addr.arpa.\n");
		fwrite($f, ";\n");
		$rQuery = "SELECT hostname,ip,zone,INET_ATON(ip)AS bin_ip FROM A_RECORDS WHERE ip LIKE '".$net[0].".".$net[1].".$i.%' ORDER BY bin_ip";
		$rRes = getResSet($db, $rQuery);
		foreach($rRes as $row) {
			# Split on '.' to break up IP
			$ip = split("\.", $row['ip'], 4);
			$oct = $ip[3];
			if($fqdn = getZoneFQDN($db, $row['zone'])) {
				# Write line to File
				$line = "$oct\t\tIN\tPTR\t".$row['hostname'].".$fqdn\n";
				fwrite($f, $line);
			} else {
				writeToLog("!--> ERROR: Unable to get FQDN for ".$row['zone'].". Host ".$row['name']." will not be in the reverse file.");
				$retVal = false; #Set False so we can keep writing file
			}
		}
		fwrite($f, ";\n"); 
	}
	return $retVal;
}

// Gen Reverse Zone File
function genRevZoneFiles($db) {
	global $NET, $tDir, $VERBOSE;

	$zNameQuery="SELECT * FROM ZONES WHERE name = '$NET'";
	$zRes = getRes($db, $zNameQuery);
	if($zRes->numRows() == 1) {
		$zRow = $zRes->fetchRow(MDB2_FETCHMODE_ASSOC);
	} else {
		$err = "!--> $NET is not a valid Zone name ...";
		fwrite(STDERR, "$err\n");
		writeToLog($err);
		return false;
	}

	var_dump($zRow); #DEBUG

	$startOutput = "\t--> Started writing reverse zone file for $NET zone ...\n";
	if($zRow['echange']==1 || $zRow['ichange']==1) { # Checking if zone has had changes
		if($VERBOSE) { fwrite(STDERR, $startOutput); }
		writeToLog(trim($startOutput), false);
		if(copyTemplate($NET) && $f = openZoneFile()) {
			if(writeRevZone($db, $zRow['class'], $NET, $f)) { 
				$finZoneOutput = "--> Finished writing zone file for $NET zone";
				if($VERBOSE) { fwrite(STDERR, "\t$finZoneOutput\n"); }
				writeToLog($finZoneOutput, false);
				fclose($f); //Always close open file
			} else {
				$err = "!--> Error writing zone file for $zone zone";
				if($VERBOSE) { fwrite(STDERR, "\t$err\n"); }
				writeToLog($err);
				fclose($f); //Always close open file
				return false;
			}
			return true;
		} else {
			writeToLog("!--> Error with copying template or opening copied template for zone $NET.", true);
			return false;
		}
	} else { echo "--> No changes for $NET\n"; return false; } #DEBUG
}

function printHelp() {
	global $VERSION;
	fwrite(STDERR, "-- Generate Zone File $VERSION --
Usage: php genZoneFiles.php -[d:r:ehvV]
	-d = OPTIONAL output directory. Default = '/var/bindinator'
	-e = Generate External View Files
	-r = Generate Reverse File for suplied subnet
	-h = Help (this output)
	-v = Verbose Mode (more verbose output)
	-V = Print Version\n");
}

function handleArgs() {
	global $EXT, $NET, $ODIR, $REV, $VERSION, $VERBOSE;
	$opts = getopt("d:r:ehvV");

	if(array_key_exists("h", $opts)) {
		printHelp();
		exit;
	}
	else if(array_key_exists("V", $opts)) {
		fwrite(STDERR, "-- checkZoneFile Version $VERSION --\n");
		exit;
	}
	// Want Output Dir Changed ?
	if(array_key_exists("d", $opts)) {
		$ODIR=$opts["d"];
	}

	if(array_key_exists("v", $opts)) {
		$VERBOSE = true;
		fwrite(STDERR, "--> Verbose Output enabled ...\n");
	}

	if(array_key_exists("e", $opts)) { # Want External View Generated ?
		$EXT = true;
		fwrite(STDERR, "### Generating the External View ###\n");
	}
	if(array_key_exists("r", $opts)) { # Generate Reverse File
		$REV=true;
		$NET=$opts["r"];
	}
	if($VERBOSE) { fwrite(STDERR, "--> Finished parsing arguments\n"); }
}
######## MAIN PROGRAM ########
include('conf/genZoneFiles.conf'); # Include CONF Variables - Move to a DB
$db=NULL;
$EXT=false;
$NET=NULL;
$OFILE=NULL;
$REV=false;
$VERSION="0.3";
$ZFILE=NULL;
$ZONE=NULL;

handleArgs(); // Parse Command Line Arguments
if($db = connectDB()) {
	if($REV) {
		if(genRevZoneFiles($db)) {
			if($VERBOSE) { fwrite(STDERR, "--> Finished generating reverse zone files to $ODIR.\n"); }
		} else { exit(3); } 
	}
	else if(genZoneFiles($db)) {
		if($VERBOSE) { fwrite(STDERR, "--> Finished generating zone files to $ODIR.\n"); }
	} else { exit(2); }

	$db->disconnect(); # Kill the connection
} else {
	if($VERBOSE) { fwrite(STDERR, "!--> ERROR connecting to the Database\n"); }
	exit(1); # Exit indicating Error
}

/* Exit Codes:
1 = General DB Connection issue
2 = Problem Generating Foreward Files
3 = Problem Generating Reverse File
4 = No valid reverse zone specified
5 = Reverse Zone IP Class Not Specified
*/
###############################
?>
