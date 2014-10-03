<?
# Include Header
include('header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator - Version <? echo $VERSION; ?> - BIND Web Frontend</title>
<link href="bindinator.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="js/mouseOver.js"></script>
<style type="text/css">
<!--
.style8 {font-size: 9px}
-->
</style>
</head>

<body onload="MM_preloadImages('images/menu/add_host_mo.jpg','images/menu/edit_host_mo.jpg','images/menu/add_cname_mo.jpg','images/menu/edit_cname_mo.jpg','images/menu/del_host_mo.jpg','images/menu/del_cname_mo.jpg','images/menu/list_zone_mo.jpg','images/menu/zone_stats_mo.jpg','images/menu/search_cname_mo.jpg','images/menu/search_cname_mo.jpg','images/menu/admin_button_mo.jpg')">
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td colspan="3" align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td width="33%"><a href="zoneStats.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Zone_Stats','','images/menu/zone_stats_mo.jpg',1)"><img src="images/menu/zone_stats.jpg" alt="Zone Stats" name="Zone_Stats" width="200" height="65" border="0" id="Zone_Stats" /></a></td>
    <td width="33%" rowspan="6" align="center" valign="middle"><div align="center">
      <p><img src="images/bindinator_globe.jpg" alt="Bindinator Globe" width="350" height="250" /></p>
      <p><a href="admin/index.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Admin_Area','','images/menu/admin_button_mo.jpg',1)"><img src="images/menu/admin_button.jpg" alt="Admin Area" name="Admin_Area" width="200" height="65" border="0" id="Admin_Area" /></a><br />
      </p>
    </div></td>
    <td width="33%"><div align="right"><a href="addHost.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Add_Host','','images/menu/add_host_mo.jpg',1)"><img src="images/menu/add_host.jpg" alt="Add Host" name="Add_Host" width="200" height="65" border="0" id="Add_Host" /></a></div></td>
  </tr>
  <tr>
    <td width="33%"><a href="zoneList.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('List_Zone','','images/menu/list_zone_mo.jpg',1)"><img src="images/menu/list_zone.jpg" alt="List a Zone" name="List_Zone" width="200" height="65" border="0" id="List_Zone" /></a></td>
    <td width="33%"><div align="right"><a href="editHost.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Edit_Host','','images/menu/edit_host_mo.jpg',1)"><img src="images/menu/edit_host.jpg" alt="Edit Host" name="Edit_Host" width="200" height="65" border="0" id="Edit_Host" /></a></div></td>
  </tr>
  <tr>
    <td width="33%"><a href="searchHost.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Search_Host','','images/menu/search_host_mo.jpg',1)"><img src="images/menu/search_host.jpg" alt="Search Host" name="Search_Host" width="200" height="65" border="0" id="Search_Host" /></a></td>
    <td width="33%"><div align="right"><a href="addCname.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Add_Cname','','images/menu/add_cname_mo.jpg',1)"><img src="images/menu/add_cname.jpg" alt="Add Cname" name="Add_Cname" width="200" height="65" border="0" id="Add_Cname" /></a></div></td>
  </tr>
  <tr>
    <td width="33%"><a href="searchCname.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Search_Cname','','images/menu/search_cname_mo.jpg',1)"><img src="images/menu/search_cname.jpg" alt="Search Cname" name="Search_Cname" width="200" height="65" border="0" id="Search_Cname" /></a></td>
    <td width="33%"><div align="right"><a href="editCname.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Edit_Cname','','images/menu/edit_cname_mo.jpg',1)"><img src="images/menu/edit_cname.jpg" alt="Edit Cname" name="Edit_Cname" width="200" height="65" border="0" id="Edit_Cname" /></a></div></td>
  </tr>
  <tr>
    <td width="33%">&nbsp;</td>
    <td width="33%"><div align="right"><a href="delHost.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Del_Host','','images/menu/del_host_mo.jpg',1)"><img src="images/menu/del_host.jpg" alt="Delete Host" name="Del_Host" width="200" height="65" border="0" id="Del_Host" /></a></div></td>
  </tr>
  <tr>
    <td width="33%">Welcome <strong><? echo $_SERVER['PHP_AUTH_USER']."</strong> to <strong>Bindinator $VERSION</strong>"; ?></td>
    <td width="33%"><div align="right"><a href="delCname.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Del_Cname','','images/menu/del_cname_mo.jpg',1)"><img src="images/menu/del_cname.jpg" alt="Delete Cname" name="Del_Cname" width="200" height="65" border="0" id="Del_Cname" /></a></div></td>
  </tr>
  <tr>
    <td colspan="3" bgcolor="#000000"><div align="center" class="style7"><font color="#FFFFFF">Copyright &copy; Bindinator Developers <? echo date("Y"); ?></font></div></td>
  </tr>
</table>
<? include('footer.php'); ?>
</body>
</html>
