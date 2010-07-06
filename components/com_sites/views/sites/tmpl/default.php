<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>



<div id="mainpage-facilities-header">
	<h2 class="contentheading">NEES Sites Map</h2>
</div>

<div id="mainpage-facilities-main">
	<div id="mainpage-facilities-lhs">
		<ul>
			<li class="facility-type-header-li">Field Experiments/Monitoring</li>
				<ul>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=226')?>">University of CA, Los Angeles</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=228')?>">University of CA, Santa Barbara</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=280')?>">University of TX, Austin</a></li>
				</ul>
			<li class="facility-type-header-li">Geotechnical Centrifuges</li>
				<ul>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=205')?>">Rensselaer Polytechnic Institute</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=276')?>">University of CA, Davis</a></li>
				</ul>
			<li class="facility-type-header-li">Large Scale Laboratories</li>
				<ul>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=180')?>">Cornell University</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=191')?>">Lehigh University</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=275')?>">University of CA, Berkeley</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=236')?>">University of IL, Urbana</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=244')?>">University of Minnesota</a></li>
				</ul>
			<li class="facility-type-header-li">Shake Tables</li>
				<ul>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=274')?>">University at Buffalo, SUNY</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=277')?>">University of CA, San Diego</a></li>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=279')?>">University of Nevada, Reno</a></li>
				</ul>
			<li class="facility-type-header-li">Tsunami Wave Basins</li>
				<ul>
					<li class="facility-header-li"><a href="<?php echo JRoute::_('/index.php?option=com_sites&view=site&id=200')?>">Oregon State University</a></li>
				</ul>
		</ul>
	</div>
	
	<div id="mainpage-facilities-rhs">
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="https://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="322" height="462" id="map" align="middle">
		<param name="allowScriptAccess" value="sameDomain" />
		<param name="movie" value="/components/com_sites/flash/facility_map.swf" />
		<param name="quality" value="best" />
		<param name="bgcolor" value="#b3b3b3" />
		<embed src="/components/com_sites/flash/facility_map.swf" quality="best" bgcolor="#b3b3b3" width="462" height="322" name="map" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="https://www.macromedia.com/go/getflashplayer" />
		</object>
	</div>

</div>


