<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Delete Cname</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Delete Cname</p>
      <p class="style1">Delete Cname</p>
<?
# Print Search and back buttons
function printLinks() {
	echo "     <p align=\"center\"><a href=\"delCname.php\">Edit Again</a> | ";
	echo "     <a href=\"#\" onClick=\"history.go(-1)\">Back</a></p>\n";
}

if(isset($_GET['cname']) && isset($_GET['zone'])) {
	if(validHostname($_GET['cname'])) {
		if($zRes = validZone($db, $_GET['zone'])) {
			if(cnameExists($db, $_GET['cname'], $_GET['zone'])) {
				delCname($db, $_GET['cname'], $_GET['zone']);
			} else {
				echo "<p align=\"center\">".$_GET['cname']." does not exists in ".$_GET['zone']." zone</p>\n";
			}
		} else {
			echo "<p>!--> ERROR: Not a valid zone specified.</p>\n";
			printLinks();
		}
	} else {
		echo "<p>!--> ERROR: Invalid CNAME ".$_GET['cname']." in zone ".$_GET['zone']."</p>\n";
		printLinks();
		
	}
} else {
?>
      <p>This will only Remove the CNAME only.</p>
      <form id="delCname" name="delCname" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center">Delete CNAME Record</div></td>
        </tr>
        <tr>
          <td width="50%">Cname</td>
          <td width="50%"><input name="cname" type="text" id="cname" size="25" maxlength="20" /></td>
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
              <input type="submit" name="submit" id="submit" value="Delete Cname" />
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
