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
      <p class="style3"><a name="top" id="top"></a><a href="../index.php">Home</a> &gt; <a href="index.php">Administration</a> &gt; Delete Category</p>
      <p class="style1">Bindinator Backend Administration</p>
<?
// Check if hosts still classified as this category
function checkForHosts($db, $name) {
	global $BASE;

	$cQuery = "SELECT COUNT(*) FROM A_RECORDS WHERE cat = '$name'";
	$dQuery = "SELECT * FROM A_RECORDS WHERE cat = '$name'";

	$cRes = getRes($db, $cQuery);
	$cRow = $cRes->fetchRow(MDB2_FETCHMODE_ASSOC);
	if($cRow['count(*)'] > 0) {
		if($res = getResSet($db, $dQuery)) {
			$resType="host";
			echo "<p class=\"style1\" align=\"center\">- Hosts exist in category $name -</p>\n";
			include('../include/resultTable.php');
			return true;
		}
	}
	return false;
}

if(isset($_GET['cat'])) {
	$nQuery = "SELECT name FROM CATEGORIES WHERE id = ".$_GET['cat'];
	$query = "DELETE FROM CATEGORIES WHERE id = ".$_GET['cat'];

	# Get Name of Category
	if($nRes = getRes($db, $nQuery)) {
		$nRow = $nRes->fetchRow(MDB2_FETCHMODE_ASSOC);
		$name = $nRow['name'];
		# Check if Hosts still exist in Category
		if(!checkForHosts($db, $name)) {
			if($affected = insertSet($db, $query)) {
				$msg = "--> Deleted category $name by ".$_SERVER['PHP_AUTH_USER'];
				echo "<p align=\"center\">$msg</p>\n";
				writeToLog($msg);
			} else {
				$err = "!--> ERROR: Problem deleting category $name";
				echo "<p align=\"center\">$err</p>\n";
				writeToLog($err);
			}
		}
	} else { 
		$err = "!--> ERROR: Prob getting name of category with id ".$_GET['zone']; 
		echo "<p align=\"center\">$err</p>\n";
		writeToLog($err);
	}
} else {
?>
      <form name="delCat" id="delCat" action="<? echo $_SERVER['PHP_SELF']; ?>" method="get">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center" class="style1">Delete Zone<br />
            <span class="style3">This form will remove a category as long as there are no hosts still in this category.</span></div></td>
          </tr>
        <tr>
          <td width="50%">Category:<br />
            <span class="style3">- Please select from list</span></td>
          <td width="50%"><select name="cat" id="cat">
<?
	// Load Categories from Database
	$res = getResSet($db, "SELECT * FROM CATEGORIES ORDER BY name");
	foreach($res as $row) {
		echo "            <option value=\"".$row['id']."\">".$row['name']."</option>\n";
	}
?>
          </select>
          </td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
            <input type="submit" name="submit" id="submit" value="Delete Category" />
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
