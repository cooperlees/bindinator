<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Edit DNS Zone</title>
<link href="../bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="../images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="../index.php">Home</a> &gt; <a href="index.php">Administration</a> &gt; Edit Zone</p>
      <p class="style1">Bindinator Backend Administration</p>
<?
if(isset($_GET['name']) && isset($_GET['fqname']) && isset($_GET['id'])) {
	if(validHostname($_GET['name']) || validIP($_GET['name'])) {
		if(isFQDN($_GET['fqname'])) {
			if(!ereg("\.$", $_GET['fqname'])) { $_GET['fqname'] .= "."; }
			if($_GET['rev'] == 0) {
				$query = "UPDATE ZONES SET
				name = '".$_GET['name']."',
				fqname = '".$_GET['fqname']."',
				rev = ".$_GET['rev']."
				WHERE id = ".$_GET['id'];
			} else if($_GET['rev'] == 1) {
				$query = "UPDATE ZONES SET
				name = '".$_GET['name']."',
				fqname = '".$_GET['fqname']."',
				rev = ".$_GET['rev'].",
				class = '".$_GET['class']."'
				WHERE id = ".$_GET['id'];
			} else {
				$err = "!--> ERROR: Problem with rev's value - ".$_GET['rev'];
				echo "<p align=\"center\">$err</p>\n";
				writeToLog($err, true);
				$qErr = false;
			}
			if(!$qErr) {
				# ADD To DB
				if($addected = insertSet($db, $query)) {
					$msg = "Edited DNS Zone ".$_GET['fqname']." with name ".$_GET['name']." by user ".$_SERVER['PHP_AUTH_USER'];
					echo "<p align=\"center\">$msg</p>\n";
					echo "<p align=\"center\">Please ensure templates are updated for this zone.</p>\n";
					writeToLog("--> $msg", false);
				} else {
					$err = "!--> ERROR: Error editing zone ".$_GET['name']." to DB. Please check logs.";
					echo "<p align=\"center\">$err</p>\n";
					writeToLog($err, true);
				}
			}
		} else {
			$err = "!--> ERROR: Invalid zonename ".$_GET['fqname'];
			echo "<p align=\"center\">$err</p>\n";
			writeToLog($err, true);
		}
	} else {
		$err = "!--> ERROR: Invalid zonename ".$_GET['name'];
		echo "<p align=\"center\">$err</p>\n";
		writeToLog($err, true);
	}
} else if(isset($_GET['zoneToEdit'])) {
	$query = "SELECT * FROM ZONES WHERE name = '".$_GET['zoneToEdit']."'";
	if($zRes = getRes($db, $query)) {
		$zRow = $zRes->fetchRow(MDB2_FETCHMODE_ASSOC);
?>
      <form name="editZone" id="editZone" action="<? echo $_SERVER['PHP_SELF']; ?>" method="get">
      <input type="hidden" name="id" value="<? echo $zRow['id']; ?>" />
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">Edit DNS Zone</div></td>
          </tr>
        <tr>
          <td width="50%">Zone Name:<br />
            <span class="style3">- Short Form name</span></td>
          <td width="50%"><input name="name" type="text" id="name" value="<? echo $zRow['name']; ?>" size="25" maxlength="20" /></td>
        </tr>
        <tr>
          <td width="50%"><p>Zone FQDN:<br />
            <span class="style3">Fully Qualified Domain Name</span></p>
            </td>
          <td width="50%"><input name="fqname" type="text" id="fqname" value="<? echo $zRow['fqname']; ?>" size="40" maxlength="35" /></td>
        </tr>
        <tr>
          <td width="50%"><p>Reverse Zone:<br />
            <span class="style3">Yes or No</span></p>
            </td>
          <td width="50%"><select name="rev" id="rev">
<?
	if($zRow['rev'] == 1) {
		echo "            <option value=\"1\" selected>Yes</option>";
		echo "            <option value=\"0\">No</option>";
	} else {
		echo "            <option value=\"1\">Yes</option>";
		echo "            <option value=\"0\" selected>No</option>";
		
	}
?>
          </select></td>
        </tr>
        <tr>
          <td width="50%"><p>Reverse Network Class:<br />
            <span class="style3">Class B or C Supported</span></p>
            </td>
          <td width="50%"><select name="class" id="class">
<?
	// Select Correct Class
	if($zRow['class'] == 'b') {
		echo "            <option value=\"b\" selected>Class B</option>
            <option value=\"c\">Class C</option>\n";
	} else {
		echo "            <option value=\"b\">Class B</option>
            <option value=\"c\" selected>Class C</option>\n";
	}
?>
          </select></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
            <input type="submit" name="submit" id="submit" value="Edit Zone" />
            <input type="reset" name="Reset" id="reset" value="Reset" />
          </div>
          <div align="center"></div></td>
          </tr>
      </table>
      </form>
<?
	} else {
		echo"<p align=\"center\">!--> ERROR: Database Query Error. Please check logs.</p>\n";
	}
} else {
?>
      <form name="zoneToEdit" id="zoneToEdit" action="<? echo $_SERVER['PHP_SELF']; ?>" method="get">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">DNS Zone To Edit</div></td>
          </tr>
        <tr>
          <td width="50%">Zone Name:<br />
            <span class="style3">- Short Form name</span></td>
          <td width="50%"><select name="zoneToEdit" id="zoneToEdit">
<?
	// Load Zones from Database
	$res = getResSet($db, "SELECT * FROM ZONES ORDER BY name");
	foreach($res as $row) {
		echo "            <option value=\"".$row['name']."\">".$row['fqname']."</option>\n";
	}
?>
            </select>
           </td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
            <input type="submit" name="submit" id="submit" value="Edit Zone" />
            <input type="reset" name="Reset" id="reset" value="Reset" />
          </div>
          <div align="center"></div></td>
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
