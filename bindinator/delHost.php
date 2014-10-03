<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Delete Host</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Delete Host</p>
      <p class="style1">Delete Host</p>
<?
# Print Search and back buttons
function printLinks() {
	echo "     <p align=\"center\"><a href=\"delHost.php\">Edit Again</a> | ";
	echo "     <a href=\"#\" onClick=\"history.go(-1)\">Back</a></p>\n";
}

if(isset($_GET['hostname']) && isset($_GET['zone'])) {
	if($hRes = validHostName($_GET['hostname'])) {
		if($zRes = validZone($db, $_GET['zone'])) {
			if(hostExists($db, $_GET['hostname'], $_GET['zone'])) {
				delHost($db, $_GET['hostname'], $_GET['zone']);
			} else {
				echo "<p align=\"center\">".$_GET['hostname']." does not exist in ".$_GET['zone']." zone</p>\n";
				printLinks();
			}
		} else {
			echo "<p>!--> ERROR: Not a valid zone specified.</p>\n";
			printLinks();
		}
	} else {
		hostError($hRes);
	}
} else {
?>
      <p>Please give the Hostname and Zone of the A Record to be deleted.<br />
      All CNAMES pointing @ this A Record will be deleted.</p>
      <form id="delHost" name="delHost" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center">Delete Host Record</div></td>
          </tr>
        <tr>
          <td width="50%">Hostname</td>
          <td width="50%"><input name="hostname" type="text" id="hostname" size="25" maxlength="20" /></td>
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
            <input type="submit" name="submit" id="submit" value="Delete Host" />
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
