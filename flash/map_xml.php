<?php
error_reporting(E_ALL);
ini_set('display_errors',TRUE);

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

//spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

require_once("/www/neeshub/api/org/phpdb/propel/central/classes/lib/data/Facility.php");
$nawifac = FacilityPeer::findAll();


foreach ( $nawifac as $p ) {
  // @var $p Facility 
  $ind[$p->getId()] = $p->getNawiStatus();
}


print <<<ENDXML

<?xml version="1.0" encoding="UTF-8"?>
<datapacket>
	<row id="1"  name="University of California at Berkeley"       file="/components/com_sites/images/img_prefacility_Berkeley.jpg" url="?facid=275" location="Berkeley, CA"      x="28"  y="126" status="$ind[275]"/>
	<row id="2"  name="Cornell University"                         file="/components/com_sites/images/img_prefacility_Cornell.jpg"  url="?facid=180" location="Ithaca, NY"        x="384" y="117" status="$ind[180]"/>
	<row id="3"  name="University of Texas at Austin"              file="/components/com_sites/images/img_prefacility_UTexas.jpg"   url="?facid=280" location="Austin, TX"        x="214" y="246" status="$ind[280]"/>
	<row id="4"  name="University of California at San Diego"      file="/components/com_sites/images/img_prefacility_UCSD.jpg"     url="?facid=277" location="San Diego, CA"     x="51"  y="185" status="$ind[277]"/>
	<row id="6"  name="University of Nevada, Reno"                 file="/components/com_sites/images/img_prefacility_UNR.jpg"      url="?facid=279" location="Reno, NV"          x="49"  y="113" status="$ind[279]"/>
	<row id="7"  name="University of Buffalo SUNY"                 file="/components/com_sites/images/img_prefacility_Buffalo.jpg"  url="?facid=274" location="Buffalo, NY"       x="367" y="113" status="$ind[274]"/>
	<row id="8"  name="Rensselaer Polytechnic Institute"           file="/components/com_sites/images/img_prefacility_RPI.jpg"      url="?facid=205" location="Troy, NY"          x="404" y="108" status="$ind[205]"/>
	<row id="9"  name="University of California at Los Angeles"    file="/components/com_sites/images/img_prefacility_UCLA.jpg"     url="?facid=226" location="Los Angeles, CA"   x="47"  y="172" status="$ind[226]"/>
	<row id="10" name="University of Minnesota"                    file="/components/com_sites/images/img_prefacility_UMN.jpg"      url="?facid=244" location="Minneapolis, MN"   x="256" y="95"  status="$ind[244]"/>
	<row id="11" name="University of California at Santa Barbara"  file="/components/com_sites/images/img_prefacility_UCSB.jpg"     url="?facid=228" location="Santa Barbara, CA" x="33"  y="163" status="$ind[228]"/>
	<row id="12" name="Lehigh University"                          file="/components/com_sites/images/img_prefacility_Lehigh.jpg"   url="?facid=191" location="Bethlehem, PA"     x="396" y="135" status="$ind[191]"/>
	<row id="13" name="University of Illinois at Urbana-Champaign" file="/components/com_sites/images/img_prefacility_UIUC.jpg"     url="?facid=236" location="Urbana, IL"        x="291" y="150" status="$ind[236]"/>
	<row id="14" name="Oregon State University"                    file="/components/com_sites/images/img_prefacility_OrSt.jpg"     url="?facid=200" location="Corvallis, OR"     x="49"  y="54"  status="$ind[200]"/>
	<row id="15" name="University of California at Davis"          file="/components/com_sites/images/img_prefacility_UCDavis.jpg"  url="?facid=276" location="Davis, CA"         x="32"  y="118" status="$ind[276]"/>
</datapacket>

ENDXML;

?>