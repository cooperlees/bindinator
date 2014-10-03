#!/usr/bin/php
<?
####################################
# Cooper Lees - me@cooperlees.com
# Purpose: Parse a Bind DNS Zone File
# and read into a Database table.
# Last Updated: 200803017
####################################

# Parse a BIND Zone File for A Records,
# CNAMES and TXT records only.

# Include Global Functions
include('./functions.php');
# Include Classes
include './classes/a_record.php';
include './classes/cname.php';

// Check Zone for Valid (According to DB of Valid Zones)
function checkZoneName() {
	global $db, $VERBOSE, $ZFILE, $ZONE;
	
	// Get Zone Name
	ereg("([[:alnum:]]+)\.zone", $ZFILE, $zone);
	$ZONE = strtolower($zone[1]);
	if ($VERBOSE) { fwrite(STDERR, "--> CheckZoneName: The Zone = $ZONE\n"); }
	if(validZone($db, $ZONE, $VERBOSE)) {
		return true;
	}
	else {
		fwrite(STDERR, "!--> Error: Non-valid zone: ".$zone[1]."\n");
		return false;
	}
}

// Work out System Category - Working off Default -ps in hostname
// Default = Server or printer
function sudoCategory($hostname) {
	if(ereg("[[:alnum:]]-ps", $hostname)) {
		return "printer";
	} else {
		return "server";
	}
}

// Create A Record Object
function createArec($A_ARRAY, $txt) {
	global $aCount, $db, $uname, $VERBOSE, $ZONE;
	
	# Organise Data
	$hostname = trim($A_ARRAY[1]);
	$ip = trim($A_ARRAY[2]);
	$cat = sudoCategory($hostname);

	# A Rec Object
	$newArec = new a_record($hostname, $ip, $ZONE, 0, $txt, $cat, date("Ymd"), $uname);
//	var_dump($newArec); #DEBUG
	if($newArec->addToDB($db, $VERBOSE)) { # Add into Database
		$aCount++;
	}
}

// Create Cname Object from Read In Line
function createCname($C_ARRAY) {
	global $cCount, $db, $uname, $VERBOSE, $ZONE;
	
	# Organise Data
	$cname = trim($C_ARRAY[1]);
	$hostname = trim($C_ARRAY[2]);
	if(ereg("^[[:alnum:]]+\.[[:alnum:]]+", $hostname)) {
		$diffZone = 1;
	} else { $diffZone = 0; }
	
	# Cname Object
	$newCname = new cname($hostname, $cname, $ZONE, 0, $diffZone, date("Ymd"), $uname);
//	var_dump($newCname); #DEBUG
	if($newCname->addToDB($db, $VERBOSE)) { # Add into Database
		$cCount++;
	}
}

// Parse Zone File 
// - Return an array of A_RECORD and CNAME Objects
function parseZoneFile($fileName, $verbose=false) {
	if ($verbose) { fwrite(STDERR, "--> Starting to parse $fileName ...\n"); }
	$handle = @fopen($fileName, "r");
	if ($handle) {
		$A_REC=false;
		$CUR_DESC=NULL;
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			if(!ereg("^;", $buffer) && ereg("^(.*)IN\tCNAME\t(.*)\n", $buffer, $C_ARRAY)) {
//				echo "CNAME: $buffer"; #DEBUG
//				var_dump($C_ARRAY); #DEBUG
				createCname($C_ARRAY); 
			}
			else if($A_REC) {
				if(ereg("^;\n", $buffer)) {
					$CUR_DESC = ereg_replace("\"", "", $CUR_DESC);
//					echo trim($CUR_DESC)."\n"; #DEBUG
					createArec($A_ARRAY, trim($CUR_DESC, "\n"));
					$A_REC=false;
					$CUR_DESC=NULL;
					continue; # Go to next line
				}
				else if(ereg("^.*(HINFO)\t(.*)", $buffer, $H_ARRAY)) {
					$CUR_DESC .= trim($H_ARRAY[2], "\n");
				}
				else if(ereg("^.*(TXT)\t(.*)", $buffer, $T_ARRAY)) {
					$CUR_DESC .= trim($T_ARRAY[2], "\n");
				}
			} 
			else if(ereg("^;.*", $buffer) || ereg("^ *\n", $buffer) || $buffer == "") {
				continue; # Next Line - Commented or empty
			}
			else if(ereg("^(.*)IN\tA\t(.*)\n", $buffer, $A_ARRAY)) {
				$A_REC=true;
//				echo "A: $buffer"; #DEBUG
//				var_dump($A_ARRAY); #DEBUG
			}
		}
		fclose($handle);
	} else {
		fwrite(STDERR, "!--> ERROR: Error opening $fileName for reading.\n");
	}
}

function printHelp() {
	global $VERSION;
	fwrite(STDERR, "-- Parse Zone File $VERSION
Usage: php parseZoneFile.php -f FILENAME -[hvV]
	-h = Help (this output)
	-v = Verbose Mode (more verbose output)
	-V = Print Version\n");
}

function handleArgs() {
	global $VERSION, $VERBOSE, $ZFILE;
	$opts = getopt("f:hvV");
	if(array_key_exists("h", $opts)) {
		printHelp();
		exit;
	}
	if(array_key_exists("V", $opts)) {
		fwrite(STDERR, "-- checkZoneFile Version $VERSION --\n");
		exit;
	}
	if(array_key_exists("v", $opts)) {
		$VERBOSE = true;
		fwrite(STDERR, "--> Verbose Output enabled ...\n");
	}
	if(!array_key_exists("f", $opts)) {
		printHelp();
		die("!--> Must specify a Zone file for me to parse ...\n");
	} else {
		$ZFILE = $opts['f'];
	}
	if($VERBOSE) {
		fwrite(STDERR, "--> Finished parsing arguments\n");
	}
}
######## MAIN PROGRAM ########
# CONF Variables
$aCount=0;
$cCount=0;
$db=NULL;
$uname = trim(shell_exec('whoami'));
$VERSION="0.1";
$VERBOSE=false;
$ZFILE=NULL;
$ZONE=NULL;

handleArgs(); // Parse Command Line Arguments
if(file_exists($ZFILE)) {
	if($db = connectDB()) { # 1 Connection per parse
		if($noError = checkZoneName()) {
			parseZoneFile($ZFILE, $VERBOSE);
		}
		$db->disconnect(); # Kill the connection
		if($noError == false) { exit(2); } # Exit with Error if Bad ZoneName
		else {
			echo "--> Parse complete:\n";
			echo "\t- There was $aCount A Records and $cCount CNAMES added to DB.\n";
		}
	} else {
		exit(1); # Exit indicating Error
	}
} else {
	die("!--> ERROR: File $ZFILE does not exist.\n");
}

/* Exit Codes:
1 = General DB Connection issue
2 = Bad Zone name
*/
?>
