<?
// Allow the use for multiple Search Results
switch($resType) {
case "host":
	$fields = array("Hostname", "IP", "Zone", "External", "TXT", "Category", "Last Modified", "User");
	break;
case "cname":
	$fields = array("CNAME", "Hostname", "Zone", "External", "Diff. Zone", "Last Modified", "User");
	break;
default:
	$fields = false;
}
// Valid Result Type
if($fields) {
?>
<table width="90%" border="0" align="center">
  <tr>
<?
	foreach($fields as $field) {
		echo "    <td align=\"center\" valign=\"middle\" bgcolor=\"#CCCCCC\" class=\"style4\">$field</td>\n";
	}
	echo "  </tr>\n";
	foreach($res as $row) {
		if($resType == "host") {
?>
  <tr>
    <td align="left" valign="middle"><a href=<? echo "\"$BASE/editHost.php?hostToEdit=".$row['hostname']."&zone=".$row['zone']."\">".$row['hostname']; ?></a></td>
    <td align="left" valign="middle"><? echo $row['ip']; ?></td>
    <td align="center" valign="middle"><? echo $row['zone']; ?></td>
    <td align="center" valign="middle"><? echo yesNo($row['ext']); ?></td>
    <td align="left" valign="middle"><? echo $row['txt']; ?></td>
    <td align="center" valign="middle"><? echo $row['cat']; ?></td>
    <td align="center" valign="middle"><? echo $row['lastmod']; ?></td>
    <td align="center" valign="middle"><? echo $row['uname']; ?></td>
  </tr>
<?
		} else if($resType == "cname") {
?>
  <tr>
    <td align="left" valign="middle"><a href=<? echo "\"$BASE/editCname.php?cnameToEdit=".$row['cname']."&zone=".$row['zone']."\">".$row['cname']; ?></a></td>
    <td align="left" valign="middle"><? echo $row['hostname']; ?></td>
    <td align="center" valign="middle"><? echo $row['zone']; ?></td>
    <td align="center" valign="middle"><? echo yesNo($row['ext']); ?></td>
    <td align="center" valign="middle"><? echo yesNo($row['diffzone']); ?></td>
    <td align="center" valign="middle"><? echo $row['lastmod']; ?></td>
    <td align="center" valign="middle"><? echo $row['uname']; ?></td>
  </tr>
<?	
		}
		if(isset($resCount)) {
			$resCount++;
		}
	}
} else {
	echo "<p align=\"center\">Problem with the required fields - $resType.</p>\n";
}
?>
</table>
