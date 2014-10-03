<?
####################################
# Cooper Lees - me@cooperlees.com
# Purpose: A_RECORD Class Object
# to hold data of a single Record
# Last Updated: 200803014
####################################

// Class to hold A_Record Data
class a_record {
	public $hostname=NULL;
	public $ip=NULL;
	public $zone=NULL;
	public $ext=false;
	public $txt=NULL;
	public $cat=NULL;
	public $lastmod=NULL;
	public $uname=NULL;

	//Constructor
	function __construct($aHn, $aIP, $aZone, $aExt=false, $aTxt, $aCat, $aDate, $aUn) { 
		$this->hostname=$aHn;
		$this->ip=$aIP;
		$this->zone=$aZone;
		$this->ext=$aExt;
		$this->txt=$aTxt;
		$this->cat=$aCat;
		if ($aDate == "" || $aDate == NULL) {
                        $this->lastmod=date("Ymd");
		} else { $this->lastmod=$aDate; }
		$this->uname=$aUn;
	}
//	$newData = new a_record(hostname, ip, zone, external, txt, category, lastmod, added-by);

	# Check if A Record already Exists for that host
	function chkNotInDB($db, $verbose=false) {
		$query = "SELECT hostname,ip,zone FROM A_RECORDS WHERE 
			hostname = '".$this->hostname."' AND 
			ip = '".$this->ip."' AND
			zone = '".$this->zone."'";
		$res = $db->query($query);
		if(PEAR::isError($res)) {
			if($verbose) { 
				fwrite(STDERR, "!--> A Record check Error: ".$res->getMessage()."\n"); 
			}
			return false;
		} else {
			if($res->numRows() >= 1) {
				if($verbose) {
					fwrite(STDERR, "!--> A Record already exists: ".$this->hostname." -> ".$this->ip." in zone ".$this->zone."\n");
				}
				return false;
			} else { return true; } # Cname does not exist in DB
		}
	}

	# Add A RECORD to DB - Need a DB Connection
	function addToDB($db, $verbose=false) {
		if($this->chkNotInDB($db, $verbose)) {
			$query = "INSERT INTO A_RECORDS (hostname, ip, zone, ext, txt, cat, lastmod, uname)
				VALUES (
				'".$this->hostname."',
				'".$this->ip."',
				'".$this->zone."',
				".$this->ext.",
				'".$this->txt."',
				'".$this->cat."',
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
					fwrite(STDERR, "--> Added ".$this->hostname." -> ".$this->ip." to the A_RECORDS database\n"); 
				}
				return true;
			}
		}
	}
}
?>
