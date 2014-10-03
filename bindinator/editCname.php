<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Edit Cname</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Edit CNAME</p>
      <p class="style1">Edit CNAME</p>
<?
# Print Search and back buttons
function printLinks() {
	echo "     <p align=\"center\"><a href=\"editCname.php\">Edit Again</a> | ";
	echo "     <a href=\"#\" onClick=\"history.go(-1)\">Back</a></p>\n";
}

if(isset($_GET['submit']) && isset($_GET['cname'])) {
	# Array of elements to check if set - Manditory
	$toCheck = array("cname", "hostname", "zone", "ext", "username");
	if(checkIfSet($toCheck)) {
		# Check if hostname in a different Zone and make sure FQDN
		$dZone = isFQDN($_GET['hostname']);
		if($dZone) { if(!ereg("\.$", $_GET['hostname'])) { $_GET['hostname'] .= "."; } } # Add . if needed
		# Validate Data - Validates Superglobal Get Array
		if(validateCnameData()) {
			if(cnameExists($db, $_GET['cname'], $_GET['zone'])) {
				modCname($db, $dZone, true);
			} else { # Error - Hostname does not exist in zone
				echo "<p>!--> Error: CNAME does not exist to edit</p>\n";
				printLinks();
			}
		} else { printLinks(); }
	} else { printLinks(); }
}
else if(isset($_GET['cnameToEdit']) && isset($_GET['zone'])) {
	if(validateCnameToEdit() && cnameExists($db, $_GET['cnameToEdit'], $_GET['zone'])) {
		//DB Query Time - Print out data in the form ...
		$query = "SELECT * FROM CNAMES WHERE 
		cname = '".$_GET['cnameToEdit']."' AND
		zone = '".$_GET['zone']."'";

		if($cRes = getRes($db, $query)) {
			$cRow = $cRes->fetchRow(MDB2_FETCHMODE_ASSOC);
?>
      <p>Please complete the form below to edit exisiting CNAME Record.<br />
      </p>
      <form id="editHost" name="editHost" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <input name="cname" type="hidden" value="<? echo $cRow['cname']; ?>" />
      <input name="zone" type="hidden" value="<? echo $cRow['zone']; ?>" />
      <input name="oldext" type="hidden" value="<? echo $cRow['ext']; ?>" />
      <table width="90%" border="0">
        <tr bgcolor="#CCCCCC">
          <td colspan="2"><div align="center" class="style1">Edit CNAME</div></td>
          </tr>
        <tr>
          <td width="50%" align="left" valign="middle">CNAME<br />
            <span class="style3">Not changeable - Not fully qualified - No '.'s</span></td>
          <td width="50%" align="left" valign="middle"><? echo $cRow['cname']; ?></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">Hostname<br />
            <span class="style3">- Fully qualified if in a different Zone to Cname please. <br />
            System will tail the string a '.' if not included ...</span></td>
          <td width="50%" align="left" valign="middle"><input name="hostname" type="text" id="hostname" size="40" maxlength="35" value="<? echo $cRow['hostname']; ?>"/></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">DNS Zone<br /></td>
          <td width="50%" align="left" valign="middle"><? echo $cRow['zone']; ?>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">External Record<br />
            <span class="style3">- Should this host be in the external view?</span></td>
          <td width="50%" align="left" valign="middle"><p>
            <label>
<?
		# Check if the value set
		if($cRow['ext'] == 1) {
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
		if($cRow['ext'] == 0) {
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
          <td width="50%" align="left" valign="middle">User Responsible<br />
            <span class="style3">- Who will be using this IP? (Username)</span></td>
          <td width="50%" align="left" valign="middle"><input name="username" type="text" id="username" size="5" maxlength="3" value="<? echo $cRow['uname']; ?>"/></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
              <input type="submit" name="submit" id="submit" value="Edit CNAME" />
              <input type="reset" name="reset" id="reset" value="Reset Form" />
            </div></td>
          </tr>
      </table>
      </form>
<?
		} else {
			echo "<p align=\"center\">".$_GET['cnameToEdit']." database lookup has failed.</p>";
			printLinks();
		}
	} else {
		echo "<p align=\"center\">".$_GET['cnameToEdit']." is not valid or does not exist in ".$_GET['zone']." zone</p>";
		printLinks();
	}
} else {
?>
      <p>Please enter the CNAME and zone of the cname you wish to edit.</p>
      <form id="fetchCname" name="fetchCname" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">Select CNAME</div></td>
          </tr>
        <tr>
          <td width="50%">CNAME</td>
          <td width="50%"><input name="cnameToEdit" type="text" id="cnameToEdit" size="25" maxlength="20" /></td>
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
            <input type="submit" name="submit" id="submit" value="Fetch CNAME" />
            <input type="reset" name="reset" id="reset" value="Reset Form" />
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
