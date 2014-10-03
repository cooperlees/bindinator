<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Backend Administration</title>
<link href="../bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="../images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="../index.php">Home</a> &gt; Administration</p>
      <p class="style1">Bindinator Backend Administration</p>
<?
function doSystemCall ($cmd, $err, $msg) {
	$retVal = null;
	echo nl2br(shell_exec($cmd)); //, null, $retVal));
	if($retVal != 0) {
		$err .= " - Returned $retVal";
		echo "<p>$err</p>\n";
		writeToLog($err);
	} else {
		echo "<p align=\"center\">$msg</p>\n";
		writeToLog($msg);
	}
}

if(isset($_POST['updateNow'])) {
	$updateFileCmd = 'cd ../utilities/; ./updateDNS.bash';
	$err = "!--> ERROR: Problem updating DNS.";
	$msg="--> User ".$_SERVER['PHP_AUTH_USER']." sucessfully updated DNS.";
	doSystemCall($updateFileCmd, $err, $msg);
} 
else {
?>
      </p>
      <p><span class="style1">Zone Administration</span><br />
        <a href="addZone.php">Add Zone</a> | <a href="editZone.php">Edit Zone</a> | <a href="delZone.php">Delete Zone</a></p>
      <p><span class="style1">Category Administration</span><br />
        <a href="addCat.php">Add Category</a> | <a href="delCat.php">Delete Category</a></p>
      <p><strong class="style1">Update DNS Now</strong><br />
        This will generate files Now and Update DNS (Manually)<br />
      <form name="updateDNSForm" id="updateDNSForm" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
        <input type="submit" name="updateNow" id="updateNow" value="Update DNS" />
      </form>
      </p>
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
