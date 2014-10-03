#!/bin/bash

## Fix Bindinator Permissions

errorCheck()
{
	if [ $? != 0 ]; then
		echo $@
		exit 1
	fi
}

# Check Args
if [ $# -ne 3 ]; then
	echo "!-> Not enough arguments. $0 APACHE_USER WEBDIR DATADIR"
	exit 1
fi

APACHE_USER=$1
WEBDIR=$2
DATADIR=$3

# Set Folder Permissions
for i in $WEBDIR $DATADIR 
do
	echo $i
	chown -R $APACHE_USER $i
	chmod 775 $i
done

# Make Web dir and Data Dir Permissions Correct
chmod -R 664 ${WEBDIR}/*.php
errorCheck "!-> ERROR with chmoding php files in ${WEBDIR}"
chmod -R 664 ${WEBDIR}/*.css
errorCheck "!-> ERROR with chmoding css files in ${WEBDIR}"

# Make CLI Script Executable
chmod -v 775 ${WEBDIR}/utilities/genZoneFiles.php
chmod -v 775 ${WEBDIR}/utilities/parseZoneFile.php
chmod -v 775 ${WEBDIR}/utilities/updateDNS.bash
chmod -v 775 ${WEBDIR}/utilities/update-dns-serial.pl

