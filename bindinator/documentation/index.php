<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Bindinator Documentation - Version 0.1</title>
<link href="../bindinator.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style8 {color: #FF0000}
.style9 {color: #0000FF}
-->
</style>
</head>

<body>
<table width="90%" border="0" align="center" id="mainTable">
  <tr>
    <td align="center" valign="middle" bgcolor="#000000"><div align="center"><img src="../images/bindinator_logo_long.jpg" alt="Bindinator" width="350" height="125" /></div></td>
  </tr>
  <tr>
    <td><div align="center">
      <p class="style3"><a name="top" id="top"></a><a href="../index.php">Home</a> &gt; Documentation</p>
      <p class="style1">Bindinator Documentation Version 0.1<br />
        <span class="style4">Based on Bindinator Version 0.6</span></p>
      <table width="50%" border="0">
        <tr>
          <td colspan="2" align="center" valign="middle" bgcolor="#CCCCCC"><div align="center" class="style4">Table Of Contents</div></td>
          </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center">1.0</div></td>
          <td><a href="#intro">Introduction</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle" class="style3"><div align="center">1.1</div></td>
          <td class="style3"><a href="#developers">Developers</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center"></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center">2.0</div></td>
          <td><a href="#install">Installation</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle" class="style3"><div align="center">2.1</div></td>
          <td class="style3"><a href="#terminology">Bindinator Terminology</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle" class="style3"><div align="center">2.2</div></td>
          <td class="style3"><a href="#dbinfo">Database Information</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle" class="style3"><div align="center">2.3</div></td>
          <td class="style3"><a href="#fsinfo">File System Information</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle" class="style3"><div align="center">2.4</div></td>
          <td class="style3"><a href="#dependencies">Dependencies</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle" class="style3"><div align="center">2.5</div></td>
          <td class="style3"><a href="#default_info">Default Install</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center"></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center">3.0</div></td>
          <td><a href="#usage">System Usage</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle" class="style3"><div align="center">3.1</div></td>
          <td class="style3"><a href="#musts">Bindinator Musts</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center"></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center">4.0</div></td>
          <td><a href="#bugs">Known Bugs / Features</a></td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center"></div></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="100" align="left" valign="middle"><div align="center">5.0</div></td>
          <td><a href="#future">Future Wishes</a></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="95%" border="0">
        <tr>
          <td align="center" valign="middle" bgcolor="#CCCCCC" class="style1"><a name="intro" id="intro"></a>1.0 Introduction</td>
          </tr>
        <tr>
          <td align="left" valign="top"><p>Bindinator is a <a href="http://php.net/" target="_blank">PHP</a> / <a href="http://mysql.com/" target="_blank">MYSQL</a> Web front end for the <a href="http://www.isc.org/products/BIND/" target="_blank">Berkely Internet Naming Deamon</a> (<a href="http://www.isc.org/products/BIND/">BIND</a>).  Bindinator generates forward and reverse BIND Zone files. It allows easy management of &quot;Internal&quot; and &quot;External&quot; BIND DNS Views through a simplified web front end for a less technical DNS administrator. Bndinator generates two files per zone, one for the internal and a less informative external zone file. Bindinator allows categorisation and organises zone files so even to a human they are more readable than hand hacked BIND zone files.</p>
            <p>This project is the only one that generates organised zone files from a database driven datastore. It allows tighter management and an audit trail (via sysloging) and a 1 area solution for a definitive store of your DNS information. With more key information being stored in the database, this allows for multiple view files to be built and kept 'in sync' and both up to date, clean and organised.</p></td>
          </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong><a name="developers" id="developers"></a>1.1 Developers</strong></td>
          </tr>

        <tr>
          <td><p align="left"><strong>Lead Developer:</strong><br />
</p>
            
            <p align="left"><a href="mailto:me@cooperlees.com">Cooper Lees</a> - Cooper is a UNIX Systems Administrator by day and a Computer/Internet Scientist graduating from the <a href="http://www.uow.edu.au/" target="_blank">University of Wollongong, Australia</a> in 2006. Since February 2007 Cooper has learnt many skills being a young keen administrator.<br />
                <br />
              Fav Linux Distro: Gentoo<br />
              Fav Scripting Language: Python / PHP (Depending if Web based or not)<br />
              Main Computing Platform: Apple Macbook Pro / iMac</p>
            <p align="center">For more information don't hesitate to visit <a href="http://cooperlees.com/" target="_blank">cooperlees.com</a> or email <a href="mailto:me@cooperlees.com">Cooper</a>.</p></td>
        </tr>
        <tr>
          <td><p align="left"><strong>Other Developers:</strong></p>
            <p align="left">N/A at the moment</p>            
            </td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="95%" border="0">
        <tr>
          <td align="center" valign="middle" bgcolor="#CCCCCC" class="style1"><a name="install" id="intro2"></a>2.0 Installation</td>
        </tr>
        <tr>
          <td align="left" valign="top"><p>The follow section of the documentation will discuss Bindinator terms, general information and how it works.</p>              </td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong><a name="terminology" id="developers2"></a>2.1 Terminology</strong></td>
        </tr>
        <tr>
          <td align="left" valign="middle"><p align="left">There are a few terms used to describe elements of Bindinator. They will be defined here to reduce any ambiguity.</p>
            <p align="left"><strong>Category Header Templates/Files</strong> - Bindinator has template files for each category defined. This is what is appended to the Zone file to destinguish each category. This <em><strong>MUST</strong></em> be a valid BIND commented out area of text.</p>
            <p align="left"><a href="http://bind-users.info/" target="_blank">Common DNS Terms</a> - Refer to 3rd party site. These terms are handy to understand when working with BIND.</p>
            <p align="left"><strong>RNDC</strong> - Name Server control utility. This superseeds the ndc utility of older BIND servers. (man rndc)</p>
            <p align="left"><strong>Zone Template</strong> - The files used to add hand edited content. Certain things are required in the 'template' such as SOA (Star of Authoriaty), MX records etc. to make a valid working DNS zone file. Bindinator will not work without a valid template for each zone managed by Bindinator.</p></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong><a name="dbinfo" id="dbinfo"></a>2.2 Database Information</strong></td>
        </tr>
        <tr>
          <td align="left" valign="middle">Bindinator is written with the <a href="http://pear.php.net/package/MDB2" target="_blank">PEAR MDB2</a> abstraction layer class. This has been written to allow for portability to other databases if you choose not to use the default MYSQL. However this has not been tested. SQL statements may need to be altered for different DBMS (e.g. sqlite, Postgresql, Oracle etc.).</td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong><a name="fsinfo" id="fsinfo"></a>2.3 File System Information</strong></td>
        </tr>
        <tr>
          <td align="left" valign="middle"><p>Bindinator has two main areas where it will live on the file system.</p>
            <p><strong>/var/bindinator</strong> - Directory where templates and generated zone files live before being rsync'd to the DNS Server.</p>
            <p><strong>Web Root</strong> (where you base your Apache or Apache Virtualhost) - That is where all the php scripts live. Defined during the install procedure.<br />
            Sub-directories:</p>
            <ul>
              <li>utilities: where all the CLI scripts live and config files + sample apache config files.</li>
            </ul></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong><a name="dependencies" id="dependencies"></a>2.4 Dependencies</strong></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#FFFFFF"><p>Bindinator requires the following pieces of software to function:</p>
            <ul>
              <li>Apache (a web server): Tested on apache - Could and should work on light-httpd</li>
              <li>PHP (with the following modules / support)</li>
              <li>PHP LDAP</li>
              <li>PHP PEAR</li>
              <li>PHP PEAR DB</li>
              <li>PHP CLI Support (this will vary on Distro - Centos was a RPM install with yum)</li>
              <li>PHP MYSQL Support</li>
              <li>PHP PEAR MDB2 Driver (for your database - i.e. MYSQL or Oracle etc.)</li>
            </ul></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong><a name="default_install" id="default_install"></a>2.5 Default Install</strong></td>
        </tr>
        <tr>
          <td align="left" valign="middle"><p>Below is the default install procedure:</p>
            <ol>
              <li> Unpack the tgz to a location of your choice (<span class="style8">tar -xvzpf bindinator-VERSION.tgz</span>)</li>
              <li>From the command line run (as root or use sudo) <br />
                - 
                <span class="style8">cd bindinator; sudo php ./install.php</span></li>
              <li>Follow the on screen prompts and fill in the data asked for.<br />
              - Information you will need is: <br />
                --
                webroot path (where you would like the php files etc. to live), <br />
                -- 
                Bindinator Data dir (default is /var/bindinator), <br />
                -- 
                MYSQL username and password and <br />
                --
                Apache User (user who apache runs as).</li>
              <li>Configure Apache AUTH to allow access to the files / virtualhost etc. - At least BASIC auth is suggested as the logged in name is used for logging as well as your use for access control.</li>
              <li>[<span class="style9">OPTIONAL</span>] If you wish Bindinator to do more than generate the files you will need to set up SSH key auth between the user apache runs as and a user on the remote system that can RNDC and write over existing zone files on the DNS server.</li>
            </ol></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="95%" border="0">
        <tr>
          <td align="center" valign="middle" bgcolor="#CCCCCC" class="style1"><a name="usage" id="intro3"></a>3.0 System Usage</td>
        </tr>
        <tr>
          <td align="left" valign="top"><p>As with any system there are caveats and some things you should know. So that will all be below.</p></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong><a name="musts" id="developers3"></a>3.1 Bindinator Musts</strong></td>
        </tr>
        <tr>
          <td align="left" valign="middle"><p align="left">Below are some known areas you must follow and do:</p>
            <p align="center"><strong>Admin Area Musts</strong></p>
            <ul>
              <li><em>Add Category:</em> Each category requires a category header file located in the utilities/conf/catHeaders and MUST be called &quot;Category Name&quot;.txt</li>
              <li><em>Add Zone:</em> Each zone needs  a template with the zone's name (the common name not fqname) located within the 'template' dir within the bindinator data dir specified in utilities/conf/genZoneFiles.conf (default = /var/bindinator).</li>
              </ul>
            <p align="center"><strong>System Musts</strong></p>
            <ul>
              <li>Due to BIND Zone Serial syntax (YYYYMMDDCC) there can only be 99 changes to a DNS zone a day. This should not be a problem just wanted to make users aware. If chosen to be cron'd don't allow it to run an inappropriate number of times - e.g. between 9 - 5 run it 4 times an hour (8 x 4 = 32) + times people push the update DNS button.</li>
            </ul></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><strong>3.2 Web Interface</strong></td>
        </tr>
        <tr>
          <td align="left" valign="middle"><ul>
            <li>Users are required to log in - This needs to be controlled by a AUTH_Mechanism</li>
            <li>Only specified users should be able to access admin area. These users need to follow the MUSTS for admin area above.</li>
            </ul></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="95%" border="0">
        <tr>
          <td align="center" valign="middle" bgcolor="#CCCCCC" class="style1"><a name="bugs" id="intro4"></a>4.0 Known Bugs</td>
        </tr>
        <tr>
          <td align="left" valign="top"><p>As with any system there are certain things that can cause undesirable results. Noted below are some know 'bugs'.</p></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><div align="center"><strong>BUGS</strong></div></td>
        </tr>
        <tr>
          <td align="left" valign="middle"><ul>
            <li>Limited error checking on some insert set DB queries.</li>
          </ul></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table width="95%" border="0">
        <tr>
          <td align="center" valign="middle" bgcolor="#CCCCCC" class="style1"><a name="future" id="intro5"></a>5.0 Future Wishes</td>
        </tr>
        <tr>
          <td align="left" valign="top"><p>There are always things that can be improved. Here are some wants for Bindinator to make it more 'complete.' These may or may not happen. If you would like to assist with these, please feel free to contact or share your thoughts on the bindinator forums @ <a href="http://bindinator.sourceforge.net/" target="_blank">http://bindinator.sourceforge.net/</a></p></td>
        </tr>
        <tr>
          <td align="left" valign="middle" bgcolor="#CCCCCC"><div align="center">List of Known Areas for Improvement:</div></td>
        </tr>
        <tr>
          <td align="left" valign="middle"><ul>
            <li>Auto category Header file generation</li>
            <li>Auto generic zone template generation</li>
            <li>More Object oriented code with storing A_RECORDS and CNAME data.</li>
            <li>SSH Keygen through install.php</li>
          </ul></td>
        </tr>
      </table>
      <p>Written by <a href="mailto:me@cooperlees.com" target="_blank">Cooper Lees</a><br />
        <span class="style3">Last Updated: 20080501</span></p>
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
