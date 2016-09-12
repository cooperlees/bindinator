<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Edit Host</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Edit Host</p>
      <p class="style1">Edit Host</p>
<?
# Print Search and back buttons
function printLinks() {
	echo "     <p align=\"center\"><a href=\"editHost.php\">Edit Again</a> | ";
	echo "     <a href=\"#\" onClick=\"history.go(-1)\">Back</a></p>\n";
}

if(isset($_GET['submit']) && isset($_GET['hostname'])) {
	$toCheck = array("hostname", "zone", "cat", "ext", "txt", "username");
	if(checkIfSet($toCheck)) {
		if(validateAddData()) {
			// Check that not editing to a exisiting host or IP.
			if(($_GET['hostname'] == $_GET['oldHostname']) || !$hRes = hostExists($db, $_GET['hostname'], $_GET['zone'])) {
				if(($_GET['ip'] == $_GET['oldIp']) || !$iRes = ipInUse($db, $_GET['ip'])) {
					modHost($db, true);
				} else { # Error - IP in use
					ipError($iRes);
				}
			} else { # Error - Hostname exists in Zone
				hostError($hRes);
			}
		} else { printLinks(); }
	} else { printLinks(); }
} 
else if(isset($_GET['hostToEdit']) && isset($_GET['zone'])) {
	if(validateHostToEdit() && hostExists($db, $_GET['hostToEdit'], $_GET['zone'])) {
		//DB Query Time - Print out data in the form ...
		$query = "SELECT * FROM A_RECORDS WHERE 
		hostname = '".$_GET['hostToEdit']."' AND
		zone = '".$_GET['zone']."'";

		if($hRes = getRes($db, $query)) {
			$hRow = $hRes->fetchRow(MDB2_FETCHMODE_ASSOC);
?>
      <p>Please complete the form below to edit exisiting A Record.<br />
      </p>
      <form id="editHost" name="editHost" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <input type="hidden" name="oldHostname" value="<? echo $hRow['hostname']; ?>" />
      <input type="hidden" name="ip" value="<? echo $hRow['ip']; ?>" />
      <input type="hidden" name="oldIp" value="<? echo $hRow['ip']; ?>" />
      <input type="hidden" name="oldext" value="<? echo $hRow['ext']; ?>" />
      <table width="90%" border="0">
        <tr bgcolor="#CCCCCC">
          <td colspan="2"><div align="center" class="style1">Edit Host</div></td>
          </tr>
        <tr>
          <td width="50%" align="left" valign="middle">Hostname<br />
            <span class="style3">- Not fully qualified - No '.'s</span></td>
          <td width="50%" align="left" valign="middle"><input name="hostname" type="text" id="hostname" size="40" maxlength="40" value="<? echo $hRow['hostname']; ?>" /></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">IP Address<br />
            <span class="style3">- Unchangeable, please delete and re-add host if changing is required.</span></td>
          <td width="50%" align="left" valign="middle"><? echo $hRow['ip']; ?></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">DNS Zone<br /></td>
          <td width="50%" align="left" valign="middle"><select name="zone" id="zone">
<?
	// Load Zones from Database
	$zRes = getResSet($db, "SELECT * FROM ZONES WHERE rev = 0 ORDER BY name");
	foreach($zRes as $row) {
		if($hRow['zone'] == $row['name']) {
			echo "            <option value=\"".$row['name']."\" selected>".$row['fqname']."</option>\n";
		} else {
			echo "            <option value=\"".$row['name']."\">".$row['fqname']."</option>\n";
		}
	}
?>
          </select></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">System Category<br /></td>
          <td width="50%" align="left" valign="middle"><select name="cat" id="cat">
<?
	// Load Categories from Database
	$res = getResSet($db, "SELECT * FROM CATEGORIES ORDER BY name");
	foreach($res as $row) {
		if($hRow['cat'] == $row['name']) {
			echo "            <option value=\"".$row['name']."\" selected>".$row['name']."</option>\n";
		} else {
			echo "            <option value=\"".$row['name']."\">".$row['name']."</option>\n";
		}
	}
?>
          </select></td>
        <tr>
          <td width="50%" align="left" valign="middle">External Record<br />
            <span class="style3">- Should this host be in the external view?</span></td>
          <td width="50%" align="left" valign="middle"><p>
            <label>
<?
		# Check if the value set
		if($hRow['ext'] == 1) {
			echo "              <input name=\"ext\" type=\"radio\" id=\"ext_0\" value=\"1\" checked=\"checked\"/>\n";
		} else {
			echo "              <input name=\"ext\" type=\"radio\" id=\"ext_0\" value=\"1\" />\n";
		}
?>
              Yes</label>
            <br />
            <label>
<?
		# Check if the value set
		if($hRow['ext'] == 0) {
			echo "              <input name=\"ext\" type=\"radio\" id=\"ext_1\" value=\"0\" checked=\"checked\"/>\n";
		} else {
			echo "              <input name=\"ext\" type=\"radio\" id=\"ext_1\" value=\"0\" />\n";
		}
?>
              No</label>
            <br />
          </p></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">TXT Record Entry<br />
            <span class="style3">- Only viewable Internally</span></td>
          <td width="50%" align="left" valign="middle"><input name="txt" type="text" id="txt" size="40" maxlength="80" value="<? echo $hRow['txt']; ?>"/></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">User Responsible<br />
            <span class="style3">- Who will be using this IP? (Username)</span></td>
          <td width="50%" align="left" valign="middle"><input name="username" type="text" id="username" size="5" maxlength="3" value="<? echo $hRow['uname']; ?>"/></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
              <input type="submit" name="submit" id="submit" value="Edit Host" />
              <input type="reset" name="reset" id="reset" value="Reset Form" />
            </div></td>
          </tr>
      </table>
      </form>
<?
		} else {
			echo "<p align=\"center\">Problem with the database query of ".$_GET['hostToEdit']."</p>\n";
			printLinks();
		}
	} else {
		echo "<p align=\"center\">".$_GET['hostToEdit']." is not a valid host in ".$_GET['zone']." zone</p>\n";
		printLinks(); }
} else {
?>
      <p>Please enter the hostname and zone of the host (A Record) you wish to edit.</p>
      <form id="fetchHost" name="fetchHost" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">Select Host</div></td>
          </tr>
        <tr>
          <td width="50%">Hostname</td>
          <td width="50%"><input name="hostToEdit" type="text" id="hostToEdit" size="25" maxlength="20" /></td>
        </tr>
        <tr>
          <td width="50%">Zone</td>
          <td width="50%"><select name="zone" id="zone">
<?
	// Load Zones from Database
	$res = getResSet($db, "SELECT * FROM ZONES WHERE rev = 0 ORDER BY name");
	foreach($res as $row) {
		echo "            <option value=\"".$row['name']."\">".$row['fqname']."</option>\n";
	}
?>
          </select></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
            <input type="submit" name="submit" id="submit" value="Fetch Host" />
            <input type="reset" name="reset" id="reset" value="Reset Form" />
            </div></td>
          </tr>
      </table>
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
