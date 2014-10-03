<?
####################################
# Cooper Lees - me@cooperlees.com
# Purpose: CNAME Class Object
# to hold data of a single Record
# Last Updated: 200803018
####################################

// Class to hold CNAME data
class cname {
	public $hostname=NULL;
	public $cname=NULL;
	public $zone=NULL;
	public $ext=0;
	public $diffzone=0;
	public $lastmod=NULL;
	public $uname=NULL;

	// Constructor to set Values
	function __construct($aHn, $aCn, $aZone, $aExt, $aDiffZone, $aDate, $aUn) {
		$this->hostname=$aHn;
		$this->cname=$aCn;
		$this->zone=$aZone;
		$this->ext=$aExt;
		$this->diffzone=$aDiffZone;
		if ($aDate == "" || $aDate == NULL) {
			$this->lastmod=date("Ymd");
		} else { $this->lastmod=$aDate; }
		$this->uname=$aUn;
	}
//      $newData = new cname(hostname, cname, zone, external, date-added, added-by);

	# Check if cname already Exists for that host
	function chkNotInDB($db, $verbose=false) {
		$query = "SELECT hostname,cname,zone FROM CNAMES WHERE 
			hostname = '".$this->hostname."' AND 
			cname = '".$this->cname."' AND
			zone = '".$this->zone."'";
		$res = $db->query($query);
		if(PEAR::isError($res)) {
			if($verbose) { 
				fwrite(STDERR, "!--> Cname check Error: ".$res->getMessage()."\n"); 
			}
			return false;
		} else {
			if($res->numRows() >= 1) {
				if($verbose) {
					fwrite(STDERR, "!--> Cname already exists: ".$this->cname." -> ".$this->hostname." in zone ".$this->zone."\n");
				}
				return false;
			} else { return true; } # Cname does not exist in DB
		}
	}

	# Add a CNAME to DB - Need a DB Connection and CNAME Object
	function addToDB($db, $verbose=false) {
		if($this->chkNotInDB($db, $verbose)) {
			$query = "INSERT INTO CNAMES (hostname, cname, zone, ext, diffzone, lastmod, uname)
				VALUES (
				'".$this->hostname."',
				'".$this->cname."',
				'".$this->zone."',
				".$this->ext.",
				".$this->diffzone.",
				'".$this->lastmod."',
				'".$this->uname."')";
//				echo "Query = ".$query."\n"; #DEBUG
			$res = $db->query($query);
			if(PEAR::isError($res)) {
				if($verbose) { 
					fwrite(STDERR, "!--> Cname Add Error: ".$res->getMessage()."\n"); 
				}
				return false;
			} else {
				if($verbose) {
					fwrite(STDERR, "--> Added ".$this->cname." to the Cname database\n"); 
				}
				return true;
			}
		}
	}
}
?>
