<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Version <? echo $VERSION; ?> - BIND Web Frontend</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Zone Stats</p>
      <p class="style1">Zone Statistics</p>
      <p>This page will echo statistics on each zone. Very large posibilities here. Will do last. Will leave basic for now.</p>
<?
# Function to generate Standard Stats
function genZoneStats($zone) {
	global $db;

	$hCount = "SELECT COUNT(*) FROM A_RECORDS WHERE zone = '$zone'";
	$cCount = "SELECT COUNT(*) FROM CNAMES WHERE zone = '$zone'";
	$uQuery = "SELECT ichange,echange FROM ZONES WHERE name = '$zone'";
	
	$hRes = getRes($db, $hCount);
	$cRes = getRes($db, $cCount);
	$uRes = getRes($db, $uQuery);

	$hRow = $hRes->fetchRow(MDB2_FETCHMODE_ASSOC);
	$cRow = $cRes->fetchRow(MDB2_FETCHMODE_ASSOC);
	$uRow = $uRes->fetchRow(MDB2_FETCHMODE_ASSOC);
	
	echo "<p align=\"center\">Zone <strong>$zone</strong> has ".$hRow['count(*)']." A Records and ".$cRow['count(*)']." CNAMES.<br \>\n";
	echo "Internal Update = ".$uRow['ichange']." | External Update = ".$uRow['echange']."</p>\n";
}

# Category Based Stats
function genZoneCatStat($zone, $cat) {

}

// Generate Zone Statistics
$zRes = getResSet($db, "SELECT * FROM ZONES ORDER BY name");
foreach($zRes as $zRow) {
	genZoneStats($zRow['name']);
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
