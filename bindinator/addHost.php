<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Add Host</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Add Host</p>
      <p class="style1">Add Host</p>
<?
# Print Search and back buttons
function printLinks() {
	echo "     <p align=\"center\"><a href=\"addHost.php\">Add Again</a> | ";
	echo "     <a href=\"#\" onClick=\"history.go(-1)\">Back</a></p>\n";
}

if(isset($_GET['submit'])) {
	# Array of elements to check if set - Manditory
	$toCheck = array("hostname", "ip", "zone", "cat", "ext", "txt", "username");
	if(checkIfSet($toCheck)) {
		# Validate Data - Validates Superglobal Get Array
		if(validateAddData()) {
			if(!$hRes = hostExists($db, $_GET['hostname'], $_GET['zone']) && (!$cRes = cnameExists($db, $_GET['cname'], $_GET['zone']))) {
				if(!$iRes = ipInUse($db, $_GET['ip'])) {
					modHost($db, false);
				} else { # Error - IP in use
					ipError($iRes);
				}
			} else { # Error - Hostname exists in zone
				$err = "!--> ERROR: The name '".$_GET['hostname']."' is already in use in zone '".$_GET['zone']."'";
				echo "<p align=\"center\">$err</p>\n";
				writeToLog($err);
				printLinks();
			}
		} else { printLinks(); }
	} else { printLinks(); }
} else {
?>
      <p>Please complete the form below to add a new A Record.<br />
        Please ensure that the IP you are using is avaliable. This system does not support multiple A records per IP.</p>
      <form id="addHost" name="addHost" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr bgcolor="#CCCCCC">
          <td colspan="2"><div align="center" class="style1">Add New Host</div></td>
          </tr>
        <tr>
          <td width="50%" align="left" valign="middle">Hostname<br />
            <span class="style3">- Not fully qualified - No '.'s</span></td>
          <td width="50%" align="left" valign="middle"><input name="hostname" type="text" id="hostname" size="40" maxlength="40" /></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">IP Address<br />
            <span class="style3">- IPV4 Please</span></td>
          <td width="50%" align="left" valign="middle"><input name="ip" type="text" id="ip" size="20" maxlength="15" /></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">DNS Zone<br /></td>
          <td width="50%" align="left" valign="middle"><select name="zone" id="zone">
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
          <td width="50%" align="left" valign="middle">System Category<br /></td>
          <td width="50%" align="left" valign="middle"><select name="cat" id="cat">
<?
	// Load Categories from Database
	$res = getResSet($db, "SELECT * FROM CATEGORIES ORDER BY name");
	foreach($res as $row) {
		echo "            <option value=\"".$row['name']."\">".$row['name']."</option>\n";
	}
?>
          </select></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">External Record<br />
            <span class="style3">- Should this host be in the external view?</span></td>
          <td width="50%" align="left" valign="middle"><p>
            <label>
              <input name="ext" type="radio" id="ext_0" value="1" />
              Yes</label>
            <br />
            <label>
              <input name="ext" type="radio" id="ext_1" value="0" checked="checked" />
              No</label>
            <br />
          </p></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">TXT Record Entry<br />
            <span class="style3">- Only viewable Internally</span></td>
          <td width="50%" align="left" valign="middle"><input name="txt" type="text" id="txt" size="40" maxlength="80" /></td>
        </tr>
        <tr>
          <td width="50%" align="left" valign="middle">User Responsible<br />
            <span class="style3">- Who will be using this IP? (Username)</span></td>
          <td width="50%" align="left" valign="middle"><input name="username" type="text" id="username" size="6" maxlength="6" /></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
              <input type="submit" name="submit" id="submit" value="Add Host" />
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
<?
include('footer.php');
?>
</body>
</html>
