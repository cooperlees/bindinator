<table width="90%" border="0" align="center">
  <tr>
<?
	echo "    <td align=\"center\" colspan=\"2\" valign=\"middle\" bgcolor=\"#CCCCCC\" class=\"style4\">$revDNSname</td>\n";
	echo "  </tr>\n";
if($res->numRows() > 0) {
	while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$ip =  split("\.", $row['ip'], 4);
		if($fqdn = getZoneFQDN($db, $row['zone'])) {
?>
  <tr>
    <td align="right" valign="middle" width="10%"><? echo $ip[3]; ?></a></td>
    <td align="right" valign="middle"><? echo $row['hostname'].".$fqdn"; ?></td>
  </tr>
<?
		} else {
			writeToLog("!--> ERROR: Unable to get FQDN for ".$row['zone'].". Host ".$row['hostnamename']." will not be in the reverse file printout.");
		}
	}
} else {
?>
	<td align="center" valign="middle" colspan="2">No Hosts in this Subnet</td>
<?
}
?>
</table>
