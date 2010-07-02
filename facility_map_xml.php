<?php

ob_clean();
header("Content-type: text/xml");

$output = <<<ENDXML
<?xml version="1.0" encoding="UTF-8"?>
<datapacket>
	<row id="10" name="University of California at Berkeley" file="/components/com_sites/images/img_prefacility_Berkeley.jpg" url="/sites/site/275" location="Berkeley, CA" x="28" y="126" factype="Large Scale Laboratory"/>
	<row id="20" name="Cornell University" file="/components/com_sites/images/img_prefacility_Cornell.jpg" url="/sites/site/180" location="Ithaca, NY" x="384" y="117" factype="Large Scale Laboratory"/>
    <row id="30" name="University of Texas at Austin" file="/components/com_sites/images/img_prefacility_UTexas.jpg" url="/sites/site/280" location="Austin, TX" x="214" y="246" factype="Field Experiments/ Monitoring"/>
	<row id="40" name="University of California at San Diego" file="/components/com_sites/images/img_prefacility_UCSD.jpg" url="/sites/site/277" location="San Diego, CA" x="51" y="185" factype="Shake Table"/>
	<row id="60" name="University of Nevada, Reno" file="/components/com_sites/images/img_prefacility_UNR.jpg" url="/sites/site/279" location="Reno, NV" x="49" y="113" factype="Shake Table"/>
	<row id="70" name="University of Buffalo SUNY" file="/components/com_sites/images/img_prefacility_Buffalo.jpg" url="/sites/site/274" location="Buffalo, NY" x="367" y="113" factype="Shake Table"/>
	<row id="80" name="Rensselaer Polytechnic Institute" file="/components/com_sites/images/img_prefacility_RPI.jpg" url="/sites/site/205" location="Troy, NY" x="404" y="108" factype="Geotechnical Centrifuge"/>
	<row id="90" name="University of California at Los Angeles" file="/components/com_sites/images/img_prefacility_UCLA.jpg" url="/sites/site/226" location="Los Angeles, CA" x="47" y="172" factype="Field Experiments/ Monitoring"/>
	<row id="100" name="University of Minnesota" file="/components/com_sites/images/img_prefacility_UMN.jpg" url="/sites/site/244" location="Minneapolis, MN" x="256" y="95" factype="Large Scale Laboratory"/>
	<row id="110" name="University of California at Santa Barbara" file="/components/com_sites/images/img_prefacility_UCSB.jpg" url="/sites/site/228" location="Santa Barbara, CA" x="33" y="163" factype="Field Experiments/ Monitoring"/>
	<row id="120" name="Lehigh University" file="/components/com_sites/images/img_prefacility_Lehigh.jpg" url="/sites/site/191" location="Bethlehem, PA" x="396" y="135" factype="Large Scale Laboratory"/>
	<row id="130" name="University of Illinois at Urbana-Champaign" file="/components/com_sites/images/img_prefacility_UIUC.jpg" url="/sites/site/236" location="Urbana, IL" x="291" y="150" factype="Large Scale Laboratory"/>
	<row id="140" name="Oregon State University" file="/components/com_sites/images/img_prefacility_OrSt.jpg" url="/sites/site/200" location="Corvallis, OR" x="49" y="54" factype="Tsunami Wave Basin"/>
	<row id="150" name="University of California at Davis" file="/components/com_sites/images/img_prefacility_UCDavis.jpg" url="/sites/site/276" location="Davis, CA" x="32" y="118" factype="Geotechnical Centrifuge"/>
</datapacket>
ENDXML;

echo($output);
?>
