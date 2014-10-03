<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Add DNS Zone</title>
<link href="../bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="../images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="../index.php">Home</a> &gt; <a href="index.php">Administration</a> &gt; Add Category</p>
      <p class="style1">Bindinator Backend Administration</p>
<?
if(isset($_GET['name'])) {
	if(validCatName($_GET['name'])) {
		$query = "INSERT INTO CATEGORIES (name) VALUES
		('".$_GET['name']."')";
		# ADD To DB
		if($addected = insertSet($db, $query)) {
			# Auto Generate Cate Header -> In future ...
			$msg = "Added Category ".$_GET['name']." - user ".$_SERVER['PHP_AUTH_USER'];
			echo "<p align=\"center\">$msg</p>\n";
			echo "<p align=\"center\">Please create a category header file in the utilities/catHeaders folder. File must be called ".$_GET['name'].".txt</p>\n";
			writeToLog("--> $msg", false);
		} else {
			$err = "!--> ERROR: Error inserting category to DB. Please check logs.";
			echo "<p align=\"center\">$err</p>\n";
			writeToLog($err, true);
		}
	} else {
		$err = "!--> ERROR: Invalid Category Name - '".$_GET['name']."'";
		echo "<p align=\"center\">$err</p>\n";
		writeToLog($err, true);
	}
} else {
?>
      <form name="addZone" id="addZone" action="<? echo $_SERVER['PHP_SELF']; ?>" method="get">
      <table width="90%" border="0">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><div align="center"><span class="style1">Add DNS Zone<br />
          </span><span class="style3">This will add a new category that systems can be classified for the dyncamically generated zone files</span></div></td>
          </tr>
        <tr>
          <td width="50%">Category Name:<br />
            <span class="style3">- The name of the category of system</span></td>
          <td width="50%"><input name="name" type="text" id="name" size="35" maxlength="30" /></td>
        </tr>
        <tr>
          <td colspan="2"><div align="center">
            <input type="submit" name="submit" id="submit" value="Add Category" />
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
