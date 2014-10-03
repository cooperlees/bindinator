<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Search Cname</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="index.php">Home</a> &gt; Search Cname</p>
      <p class="style1">Search Cname</p>
<?
if(isset($_GET['hostname']) || isset($_GET['cname'])) {
	// Are we searching a by hostname ?
	$hName = false; # Default = false
	// Check for searching via Hostname or CNAME
	// Convert to lower case ...
	if(isset($_GET['hostname'])) {
		$hName = true;
		$name = strtolower($_GET['hostname']);
	} else if(isset($_GET['cname'])) {
		$name = strtolower($_GET['cname']);
	} else {
		$name = "!;@NOWAY4UHOMO##$%^%^"; //Invalid Hostname
	}

	if(validHostname($name)) {
		$wildCardReg = "^.*%.*";
		if($hName) {
			if(ereg($wildCardReg, $name)) {
				$query = "SELECT * FROM CNAMES WHERE
				hostname LIKE '$name' ORDER BY cname";
			} else {
				$query = "SELECT * FROM CNAMES WHERE 
				hostname = '$name' ORDER BY cname";
			}
		} else {
			if(ereg($wildCardReg, $name)) {
				$query = "SELECT * FROM CNAMES WHERE
				cname LIKE '$name' ORDER BY hostname";
			} else {
				$query = "SELECT * FROM CNAMES WHERE 
				cname = '$name' ORDER BY zone";
			}
		}
		if($res = getResSet($db, $query)) {
			$resCount=0;
			$resType="cname";
			include('include/resultTable.php');
		} else {
			echo "<p align=\"center\">Problem obtaining the cname $name</p>";
		}
		if ($resCount < 1 && $res) {
			echo "<p align=\"center\">Sorry your serach of $name returned 0 entries.</p>";
		}
	} else {
		echo "<p align=\"center\">You did not specify a valid cname.</p>";
	}
		echo "<p align=\"center\"><a href=\"searchCname.php\">Search Again</a></p>\n";
} else {
?>
      <p>Enter the CNAME or Hostname you wish to search. <br />
        The search will return the <br />
        - CNAMES for the Hostname you specify or <br />
        - CNAME you specify will return what they point at for all ZONES. </p>
      <p>Wildcard (%) searching is supported.</p>
      <form id="searchCname" name="searchCname" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr align="center">
          <td colspan="2"><div align="center" class="style1">Search for a CNAME</div></td>
          </tr>
        <tr>
          <td width="50%" align="left" valign="middle">CNAME<br />
            <span class="style3">- Not fully qualified</span></td>
          <td width="50%" align="left" valign="middle">
            <input name="cname" type="text" id="cname" size="35" maxlength="30" />
          </td>
        </tr>
        <tr align="center" valign="middle">
          <td colspan="2"><input type="submit" name="submit" id="submit" value="Search" />
            <input type="reset" name="reset" id="reset" value="Reset" /></td>
        </tr>
      </table>
      </form>
      <form id="searchHCnames" name="searchHCnames" method="get" action="<? echo $_SERVER['PHP_SELF']; ?>">
      <table width="90%" border="0">
        <tr align="center">
          <td colspan="2"><div align="center" class="style1">Search a Hosts CNAMES</div></td>
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
