<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Delete DNS Zone</title>
<link href="../bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="../images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="../index.php">Home</a> &gt; <a href="index.php">Administration</a> &gt; Delete Zone</p>
      <p class="style1">Bindinator Backend Administration</p>
<?
# Clean out Zone's Entries from Database
function cleanOutTables($db, $zName) {
	$hQuery = "DELETE FROM A_RECORDS WHERE zone = '$zName'";
	$cQuery = "DELETE FROM CNAMES WHERE zone = '$zName'";

	# Delete CNAMES
	if($affected = insertSet($db, $cQuery)) {
		$msg = "--> Sucessfully cleaned out zone $zName entries from the CNAME table";
		echo "<p align=\"center\">$msg</p>\n";
		writeToLog($msg);
	} else {
		$err = "!--> Problem with cleaning out zone $zName entries from the CNAME table. Manual cleaning will be required.";
		echo "<p align=\"center\">$err</p>\n";
		writeToLog($err);
	}
	# Delete A_RECS
	if($affected = insertSet($db, $hQuery)) {
		$msg = "--> Sucessfully cleaned out zone $zName entries from the A_RECORDS table";
		echo "<p align=\"center\">$msg</p>\n";
		writeToLog($msg);
	} else {
		$err = "!--> Problem with cleaning out zone $zName entries from the A_RECORDS table. Manual cleaning will be required.";
		echo "<p align=\"center\">$err</p>\n";
		writeToLog($err);
	}
}

if(isset($_GET['zone'])) {
	$sQuery = "SELECT name FROM ZONES WHERE id = ".$_GET['zone'];
	$query = "DELETE FROM ZONES WHERE id = ".$_GET['zone'];

	# Get Name of Zone
	if($nRes = getRes($db, $sQuery)) {
		$nRow = $nRes->fetchRow(MDB2_FETCHMODE_ASSOC);
		$name = $nRow['name'];
		if($affected = insertSet($db, $query)) {
			$msg = "--> Deleted zone $name by ".$_SERVER['PHP_AUTH_USER'];
			echo "<p align=\"center\">$msg</p>\n";
			writeToLog($msg);
			# Clean out A_RECORDS and CNAME tables of Zone data
			cleanOutTables($db, $name);
		} else {
			$err = "!--> ERROR: Problem deleting zone $name";
			echo "<p align=\"center\">$err</p>\n";
			writeToLog($err);
		}
	} else { 
		$err = "!--> ERROR: Prob getting name of zone with id ".$_GET['zone']; 
		echo "<p align=\"center\">$err</p>\n";
		writeToLog($err);
	}
} else {
?>
      <form name="delZone" id="delZone" action="<? echo $_SERVER['PHP_SELF']; ?>" method="get">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">Delete Zone<br />
            <span class="style3">Please select zone to delete.<br /> <strong>Be AWARE</strong> this will remove the zone and ALL CORRESPONDING entries in the A Records and CNAME Records tables.</span></div></td>
          </tr>
        <tr>
          <td width="50%">Zone:<br />
            <span class="style3">- Please select from list</span></td>
          <td width="50%"><select name="zone" id="zone">
<?
	// Load Zones from Database
	$res = getResSet($db, "SELECT * FROM ZONES ORDER BY name");
	foreach($res as $row) {
		echo "            <option value=\"".$row['id']."\">".$row['fqname']."</option>\n";
	}
?>
          </select>
          </td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
            <input type="submit" name="submit" id="submit" value="Delete Zone" />
            <input type="reset" name="Reset" id="reset" value="Reset" />
          </div>
          </td>
          </tr>
      </table>
      </form>
<?
}
?>
      <p class="style7">Back to <a href="#top">Top</a> or <a href="../index.php">Home</a></p>
    </div></td>
  </tr>
  
  <tr>
    <td bgcolor="#000000"><div align="center" class="style7"><font color="#FFFFFF">Copyright &copy; Bindinator Developers <? echo date("Y"); ?></font></div></td>
  </tr>
</table>
<? include('../footer.php'); ?>
</body>
</html>
