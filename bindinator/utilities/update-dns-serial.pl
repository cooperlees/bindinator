#!/usr/bin/perl -w

# A little script to increment the SOA serial number in our DNS Zone files.
# relies on the SOA serial number appearin on a line formatted as:
#
#    (white space)yyyymmddcc(white space);serial - date and time used by me
#
# Note: the comment is checked for also.
#
# if today > yyyymmdd then new serial = todays yyyymmdd00
#                                       (that is todays date + 00)
# else                     new serial = existing yyyymmddcc
#                                       where cc = cc+1
#                          except if cc+1 > 99 then cc = 99
# Robert Cawley - rjc - A long time ago
# Included by Cooper Lees with Bindinator :-)

# usage:
#        update-dns-serial.pl zonefile

## Signal Handler

$SIG{HUP} = $SIG{INT} = $SIG{TERM} =
    sub {
	unlink ${GENTBLS::Cleanfile}  if defined $GENTBLS::Cleanfile;
	unlink ${GENTBLS::Cleanfile2} if defined $GENTBLS::Cleanfile2;
	die "ERROR: Bailout after SIG $_[0]\n";
	};

## END handler

END
    {
    local($?, $!);
    unlink ${GENTBLS::Cleanfile}  if defined $GENTBLS::Cleanfile;
    unlink ${GENTBLS::Cleanfile2} if defined $GENTBLS::Cleanfile2;
    };

my $dnsfile = $ARGV[0] or die "Need to specify zone file as 1st argument\n";
my $dnstmp = $dnsfile . ".tmp";

# Set up a lock
# the locking prevents a vast chain of updaters running concurrently
# on this file

&lockit($dnsfile);

open(DNSIN, "<" . $dnsfile) or die "Failed to open dnsfile: $dnsfile\n";
open(DNSOUT, ">" . $dnstmp) or die "Failed to open tem dns output file: $dnstmp\n";


my $date = &get_date();
my $line;

while ($line = <DNSIN>)
    {
    chomp($line);
    $_ = $line;
    if ( /(\d\d\d\d\d\d\d\d)(\d\d)\s;serial - date and time used by me/i )
	{
	my $df = $1;
	my $cc = $2;
	if ($df < $date)
	    {
	    $df = $date;
	    $cc = "00";
	    }
	else
	    {
	    $cc++;
	    if ($cc > 99) { $cc = 99; }
	    }
	#my $serial = $df . $cc;
	$serial = sprintf("%08d%02d", $df, $cc);
	$line =~ s/\d\d\d\d\d\d\d\d\d\d/$serial/;
	}
    print DNSOUT "$line\n";
    }
close(DNSIN);
close(DNSOUT);

# OK, we now have original and updated versions.
# The next rigmarole is an attempt to safely replace the original with the 
# new updated version, and backout gracefully if anything fails along the way

my $dnssave = $dnsfile . ".backup";
# try to save original
if (link($dnsfile,$dnssave))
    {
    # that worked, now remove original
    unlink($dnsfile) || do
	{
	# that failed, so recover and get out
	unlink($dnssave);
	unlink($dnstmp);
	die "Error: Cannont update dns zone file: $dnsfile. (Permissions ??)\n";
	};
    # original removed, now link in new
    if (!link($dnstmp,$dnsfile))
	{
	# that failed, remove tmp new
	unlink($dnstmp);
	# and try to put original back
	link($dnssave,$dnsfile) || do
	   {
           # that failed too, tell user and get out
	   die "Error: Cannont update dns zone file: $dnsfile. (Permissions ??)\n" .
	       "       Original has also gone, backup at $dnssave\n";
	   };
	}

    # every thing has worked if we get here, remove tmp and backup

    unlink($dnstmp);
    unlink($dnssave);
    }
else
    {
    unlink($dnstmp);
    die "Error: Cannont update dns zone file: $dnsfile. (Permissions ??)\n";
    }

# All over red rover, remve the lock

close(LOCK);
unlink $GENTBLS::Cleanfile  if defined $GENTBLS::Cleanfile;
unlink $GENTBLS::Cleanfile2 if defined $GENTBLS::Cleanfile2;

exit();

sub get_date
    {
    # returns yyyymmdd

    my($mday,$mon,$year) = (localtime(time))[3,4,5];
    $year += 1900;
    $mon++;
#    return  "$year$mon$mday";
    return sprintf("%04d%02d%02d", $year, $mon, $mday);
    }

sub lockit
    {
    my ($lkbase) = @_;
    my $lockfile = $lkbase . "_l";
    my $templock = $lkbase . "_l" . $$ ;

    # set up the lock. Method stolen from mrtg.

    open(LOCK,">$templock") || die "ERROR: Creating templock $templock: $!";
    $GENTBLS::Cleanfile = $templock;
    if (!link($templock,$lockfile))
	{ # Lock file exists - deal with it.
	$GENTBLS::Cleanfile2 = $lockfile;
	my($nlink,$lockage) = (stat($lockfile))[3,9];
	$lockage = time() - $lockage;
	if ($nlink < 2 && $lockage > 60*60)
	    { #lockfile is alone and old
	    unlink($lockfile) || do
		{
		unlink $templock;
		die "ERROR: Can't unlink stale lockfile ($lockfile). Permissions?\n"
		};
	    link($templock,$lockfile) || do
		{
		unlink $templock;
		die "ERROR: Can't create lockfile ($lockfile).\n".
		    "Permission problem or another gen_tables locking succesfully?\n"
		};
	    }
	else
	    {
	    unlink $templock;
	    die  "ERROR: I guess another gen_tables is running. A lockfile ($lockfile) aged\n".
		 "$lockage seconds is hanging around. If you are sure that no other gen_tables\n".
		 "is running you can remove the lockfile\n";
	    }
	}
    # link just created, remember it!
    $GENTBLS::Cleanfile2 = $lockfile;
    }
