#!/usr/bin/php
<?
// Populate System category Databases

// Connect
$link = mysql_connect('localhost', 'root', '');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo "--> Connected successfully\n";

$categories = array("server", "its-admin", "its-user", "user-server", "user-workstation", "printer", "network-equip", "other", "vm", "zones");
$db = array("dhcp", "dns");

// Fill the Two DBs
foreach ($db as $aDB) {
	foreach ($categories as $aCat) {
		$query = "INSERT INTO CATEGORIES (name) VALUES ('$aCat')";
		if (!mysql_db_query($aDB, $query)) {
			echo "!--> Error with $query\n".mysql_error()."\n";
		}
	}
}

echo "--> Finished populating category Database\n";
mysql_close($link);
?>
