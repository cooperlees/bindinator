<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Zone List</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; List Zone</p>
      <p class="style1">List Zone</p>
<?
if(isset($_GET['submit']) && $_GET['submit'] == "Print Foreward Zone") {
	// Maybe add check valid Zone ?
	if(isset($_GET['zone']) && validZone($db, $_GET['zone'])) {
		$hQuery = "SELECT * FROM A_RECORDS WHERE zone = '".$_GET['zone']."' ORDER BY hostname";
		$cQuery = "SELECT * FROM CNAMES WHERE zone = '".$_GET['zone']."' ORDER BY cname";

		//Print out all Arecords
		if($res = getResSet($db, $hQuery)) {
			$resType = "host";
			include('include/resultTable.php');
			if($res = getResSet($db, $cQuery)) { //Print out CNAMES
				$resType = "cname";
				echo "   <p align=\"center\" class=\"style1\">Zone CNAMES</p>\n";
				include('include/resultTable.php');
			} else {
				echo "<p>!--> ERROR: Problem with getting CNAMES</p>\n";
			}
		} else {
			echo "<p>!--> ERROR: Problem with getting A Records</p>\n";
		}
	} else {
		echo "<p>No zone specified or not a valid zone.</p>";
	}
}
else if(isset($_GET['submit']) && $_GET['submit'] == "Print Reverse Zone") {
	// Check if zone name valid Network Address
	if(validIP($_GET['zone']) && validZone($db, $_GET['zone'])) {	
		$net = split("\.", $_GET['zone'], 4);
		$zRes = getRes($db, "SELECT class FROM ZONES WHERE name = '".$_GET['zone']."'");
		$zRow = $zRes->fetchRow(MDB2_FETCHMODE_ASSOC);
		if($zRow['class'] == "b") {
			$start = 0;
			$end = 256;
		} else if($zRow['class'] == "c") {
			$start = $net[2];
			$end = $net[2] + 1;
		} else {
			$err = "!--> Error: Invalid Class Specified";
			writeToLog("$err");
			return false;
		}
		
		for ($i = $start; $i < $end; $i++) {
			$query = "SELECT hostname,ip,zone,INET_ATON(ip)AS bin_ip FROM A_RECORDS WHERE ip LIKE '".$net[0].".".$net[1].".$i.%' ORDER BY bin_ip";
			$res = getRes($db, $query);
			$revDNSname = "$i.".$net[1].".".$net[0].".in-addr.arpa.";
			include('include/revTable.php');
		}
	} else {
		$err = "!--> ERROR: Reverse zone name is not the network address and is not a valid zone";
		echo "<p align=\"center\">$err</p>\n";
		writeToLog($err);
	}
} else {
?>
      <p>Please choose the zone you would like printed out.</p>
      <form id="fZoneList" name="fZoneList" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">Foreward Zone to Output:</div></td>
          </tr>
        <tr>
          <td width="50%">Zone</td>
          <td width="50%"><select name="zone" id="zone">
<? printZones($db); ?>
          </select></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
              <input type="submit" name="submit" id="submit" value="Print Foreward Zone" />
              <input type="reset" name="reset" id="reset" value="Reset" />
            </div></td>
          </tr>
      </table>
      </form>
	
      <form id="rZoneList" name="rZoneList" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">Reverse Zone to Output:</div></td>
          </tr>
        <tr>
          <td width="50%">Zone</td>
          <td width="50%"><select name="zone" id="zone">
<? 
	// Pull out Reverse Zones outt DB
	$query = "SELECT * FROM ZONES WHERE rev = 1 ORDER BY fqname";
	$res = getResSet($db, $query);
        foreach($res as $aRes) {
                echo "            <option value=\"".$aRes['name']."\">".$aRes['fqname']."</option>\n";
        }
?>
          </select></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
              <input type="submit" name="submit" id="submit" value="Print Reverse Zone" />
              <input type="reset" name="reset" id="reset" value="Reset" />
            </div></td>
          </tr>
      </table>
      </form>
<?
}
?>
      <p class="style7">Back to <a href="#top">Top</a> or <a href="index.php">Home</a></p>
    </div></td>
  </tr>
  
  <tr>
    <td bgcolor="#000000"><div align="center" class="style7"><font color="#FFFFFF">Copyright &copy; Bindinator Developers <? echo date("Y"); ?></font></div></td>
  </tr>
</table>
<? include('footer.php'); ?>
</body>
</html>
