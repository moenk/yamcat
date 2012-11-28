<?php
//
//	YAMCAT - Yet Another Metadata CATalog
//
//	Development by moenk for GeoInformationScienceLab HU Berlin
//
//	Please only adjust settings in this file!
//

// change here to brand YAMCAT to your own needs
$title="Geodata Infrastructure of the Geography Department";
$subtitle="Humboldt-Universität zu Berlin";

// Imprint
$imprint="http://www.geographie.hu-berlin.de/impressum";

// how this website is called and are urls rewritten to, with trailing slash!
$domainroot="http://gdi.geo.hu-berlin.de/";

// where all shapes with metadata are stored, with trailing slash
$geodatapath="/data/gdi_geohu/";
//$geodatapath="/data/ftp/";

// url to mapserver-cgi, leave empty if you don't have one
$mapserverurl="http://gdi.geo.hu-berlin.de/cgi-bin/mapserv";

// Google Analytic UA, leave empty or uncomment if none
$googleanalytics="UA-12345678-1";

// the usual mysql stuff
$hostname='localhost';
$user='geouser';
$pass='geopass';
$dbase='geobase';

// Self Registration: 1 to enable, 0 to disable
$registration = 1;

// Show Contact information on details page: 1 to enable, 0 to disable
$showcontact = 1;

// sets the SMTP server
$mailhost = "mailhost.cms.hu-berlin.de"; 

// may be '' or 'tls' or 'ssl'
$mailsecure = 'tls'; 

//  set the SMTP port to 25 - but change if not, e.g. for SSL!
$mailport = 25; 

// SMTP account username
$mailusername = "username";
 
// SMTP account password
$mailpassword   = "password!";        

// working sender mail address for emails 
$mailfrom='user@domain.com';

?>
