<p align="center" class="style7"><? system('uptime'); ?></p>
<p align="center" class="style7">Please email bugs to <a href="mailto:me@cooperlees.com">Cooper</a></p>
<?
# IF DB connection disconnect
if($db) {
	disconnectDB($db);
}
?>
