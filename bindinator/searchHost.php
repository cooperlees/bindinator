<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Search Host</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Search Host</p>
      <p class="style1">Search Host</p>
<?
if(isset($_GET['hostname'])) {
	// Check if Valid Hostname - Convert to lower case ...
	$_GET['hostname'] = strtolower($_GET['hostname']);
	if(validHostname($_GET['hostname'])) {
		if(ereg("^.*%.*", $_GET['hostname'])) {
			$query = "SELECT * FROM A_RECORDS WHERE
			hostname LIKE '".$_GET['hostname']."' ORDER BY hostname";
		} else {
			$query = "SELECT * FROM A_RECORDS WHERE 
			hostname = \"".$_GET['hostname']."\" ORDER BY zone";
		}
		if($res = getResSet($db, $query)) {
			$resCount=0;
			$resType="host";
			include('include/resultTable.php');
		} else {
			echo "<p align=\"center\">Problem obtaining that hostname ".$_GET['hostname']."</p>";
		}
		if ($resCount < 1 && $res) {
			echo "<p align=\"center\">Sorry your serach of ".$_GET['hostname']." returned 0 entries.</p>";
		}
	} else {
		echo "<p align=\"center\">You did not specify a valid hostname.</p>";
	}
		echo "<p align=\"center\"><a href=\"searchHost.php\">Search Again</a></p>\n";
} else {
?>
      <p>Enter the hostname you wish to search. <br />
        You will be presented with all hostnames from different zones that match your query.</p>
      <p>Wildcard (%) searching is supported.</p>
      <form id="searchHost" name="searchHost" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr align="center">
          <td colspan="2"><div align="center" class="style1">Search</div></td>
          </tr>
        <tr>
          <td width="50%" align="left" valign="middle">Hostname<br />
            <span class="style3">- Not fully qualified</span></td>
          <td width="50%" align="left" valign="middle">
            <input name="hostname" type="text" id="hostname" size="35" maxlength="30" />
          </td>
        </tr>
        <tr align="center" valign="middle">
          <td colspan="2"><input type="submit" name="submit" id="submit" value="Search" />
            <input type="reset" name="reset" id="reset" value="Reset" /></td>
          </tr>
      </table>
      </form>
<?
}
?>  
      <p align="center" class="style7">Back to <a href="#top">Top</a> or <a href="index.php">Home</a></p>
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
