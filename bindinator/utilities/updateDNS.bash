#!/bin/bash

####################################
# Cooper Lees - me@cooperlees.com
# Purpose: Shell script to co-ordinate
# the updating of BIND DNS Zone File
# Designed to be used with CRON
# Last Updated: 20080423
####################################

# Check the process return - echo and LOG to Syslog
errorCheck()
{
	ERRCODE=$?
	if [ $ERRCODE -ne 0 ]; then
		echo $@
		logger $@
		exit $ERRCODE
	fi
}

## CONF VARS ##
BASE=$(pwd)
OUTPUTDIR="/var/bindinator" # DEFAULT DIR
PATH=$PATH:/usr/sbin:/usr/local/sbin
VERBOSE=1
DNSSERVER=""

## GENERATE FOREWARD ZONES IF REQUIRED ##
# Generate all Internal Zone file that have changed
if [ $VERBOSE -eq 0 ]; then
	php ${BASE}/genZoneFiles.php
else
	php ${BASE}/genZoneFiles.php -v 2>&1
fi
errorCheck "!--> [updateDNS]: Error generating Internal Zone Files"

# Generate all External Zone file that have changed
if [ $VERBOSE -eq 0 ]; then
	php ${BASE}/genZoneFiles.php -e
else
	php ${BASE}/genZoneFiles.php -ev 2>&1
fi
errorCheck "!--> [updateDNS]: Error generating External Zone Files"

# Check if there are files in (foreward) zones dir - if there is generate and update DNS
ls ${OUTPUTDIR}/zones/*.zone > /dev/null 2>&1
if [ $? -eq 0 ]; then
	# Generate Reverse Files Always - Need a way to check for updates of forward zones - Return Code ?
#	for ZONENAME in '10.10.0.0' '137.157.0.0'
	for ZONENAME in '137.157.0.0'
	do
		if [ $VERBOSE -eq 0 ]; then
			php ${BASE}/genZoneFiles.php -r ${ZONENAME}
		else
			php ${BASE}/genZoneFiles.php -v -r ${ZONENAME}
		fi
		errorCheck "!--> [updateDNS]: Error generating Reverse Zone File for ${ZONENAME}"
	done

	# Update Serials of all Generated Zones - Need to move this to the genzone.php 
#	for file in $(ls ${OUTPUTDIR}/zones/*.zone) $(ls ${OUTPUTDIR}/revzones/*.zone)
#	do
#		echo "SERIAL UPDATE ${file}" #DEBUG
#		${BASE}/update-dns-serial.pl ${file}
#	done

	## COPY AND UPDATE DNS SERVER ##
	
	# Rsync Zone Files that Changed 
	# check if files exist in dir ...
#	rsync -qaHxzv -e ssh ${OUTPUTDIR}/zones/*.zone root@${DNSSERVER}:/var/named/chroot/var/named/pri/Ansto #2>&1 > /tmp/dnsZoneSync
#	errorCheck "!--> [updateDNS]: Error rsyncing Files ..."
	
	# Sync Reverse Zones
	# check if files exist in dir ...
#	rsync -qaHxzv -e ssh ${OUTPUTDIR}/revzones/*.zone root@${DNSSERVER}:/var/named/chroot/var/named/pri/Ansto/reverse #2>&1 > /tmp/dnsRevZoneSync
#	errorCheck "!--> [updateDNS]: Error rsyncing Reverse Files ..."
	
	# Tell DNS Server to Reload Zones + Notify
#	ssh root@${DNSSERVER} 'rndc reload' 
#	RNDC_RET=$?
	# If verbose show last few lines of messages on DNSSERVER
#	if [ $VERBOSE -eq 1 ]; then
#		sleep 2
#		ssh root@${DNSSERVER} 'tail -100 /var/log/messages'
#	fi
#	if [ ${RNDC_RET} -ne 0 ]; then
#		echo "!--> [updateDNS]: Error with 'rndc reload' on $DNSSERVER - Returned ${RNDC_RET}"
#	fi
	
	# Cleanup Generated Zone Files
#	rm -vrf ${OUTPUTDIR}/zones/*.zone 
#	rm -vrf ${OUTPUTDIR}/revzones/*.zone
else 
	if [ $VERBOSE -ne 0 ]; then
		msg="--> No foreward zone files - No updates."
		echo "${msg}"
		echo $msg | logger
	fi
fi
