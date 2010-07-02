<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<div id="mainpage-facilities-header">
	<h2 class="contentheading">NEES Site Activities Map</h2>
</div>

<div id="mainpage-facilities-main">
	<div id="mainpage-facilities-lhs">
		<ul>
			<li class="facility-type-header-li">Field Experiments/Monitoring</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(226);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=226')?>">University of CA, Los Angeles</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(228);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=228')?>">University of CA, Santa Barbara</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(280);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=280')?>">University of TX, Austin</a></li>
				</ul>
			<li class="facility-type-header-li">Geotechnical Centrifuges</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(205);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=205')?>">Rensselaer Polytechnic Institute</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(276);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=276')?>">University of CA, Davis</a></li>
				</ul>
			<li class="facility-type-header-li">Large Scale Laboratories</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(180);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=180')?>">Cornell University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(191);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=191')?>">Lehigh University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(275);?> "href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=275')?>">University of CA, Berkeley</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(236);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=236')?>">University of IL, Urbana</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(244);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=244')?>">University of Minnesota</a></li>
				</ul>
			<li class="facility-type-header-li">Shake Tables</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(274);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=274')?>">University at Buffalo, SUNY</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(277);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=277')?>">University of CA, San Diego</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(279);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=279')?>">University of Nevada, Reno</a></li>
				</ul>
			<li class="facility-type-header-li">Tsunami Wave Basins</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(200);?> href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=siteactivities&id=200')?>">Oregon State University</a></li>
				</ul>
		</ul>
	</div>
	
	<div id="mainpage-facilities-rhs">
		<H2>site goes here!</H2>
	</div>

</div>


<?php 

	function SelectedStyleSheet($id)
	{
		$rv = '';
		
		if( JRequest::getInt('id') == $id )
			$rv = ' facility-header-li-selected ';
		else
			$rv = '';
			
		return $rv;
	}

?>