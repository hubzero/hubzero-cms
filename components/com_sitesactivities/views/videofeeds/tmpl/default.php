<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript" src="/components/com_sitesactivities/js/siteactivities.js"></script>

<div id="mainpage-facilities-header">
	<h2>Site Activities</h2>
</div>

<?php echo $this->tabs;?>


<div id="mainpage-facilities-main" style="width:1100px; min-width: 1100px">

	<div id="mainpage-facilities-lhs">
		<ul>
			<li class="facility-type-header-li">Field Experiments/Monitoring</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(226);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=226')?>">University of CA, Los Angeles</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(228);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=228')?>">University of CA, Santa Barbara</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(280);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=280')?>">University of TX, Austin</a></li>
				</ul>
			<li class="facility-type-header-li">Geotechnical Centrifuges</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(205);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=205')?>">Rensselaer Polytechnic Institute</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(276);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=276')?>">University of CA, Davis</a></li>
				</ul>
			<li class="facility-type-header-li">Large Scale Laboratories</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(180);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=180')?>">Cornell University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(191);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=191')?>">Lehigh University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(275);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=275')?>">University of CA, Berkeley</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(236);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=236')?>">University of IL, Urbana</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(244);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=244')?>">University of Minnesota</a></li>
				</ul>
			<li class="facility-type-header-li">Shake Tables</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(274);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=274')?>">University at Buffalo, SUNY</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(277);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=277')?>">University of CA, San Diego</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(279);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=279')?>">University of Nevada, Reno</a></li>
				</ul>
			<li class="facility-type-header-li">Tsunami Wave Basins</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(200);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=200')?>">Oregon State University</a></li>
				</ul>
		</ul>
	</div>
	
	<div id="mainpage-facilities-rhs" style="min-width:780px;">

            <h2><?php echo $this->facilityName; ?> live video feeds</h2>
            <hr style="padding-bottom:10px;"/>
            
            <?php
            if ($this->feed_count > 0)
            {
            ?>

                <div style="float:right;">

                    <div id="flashHTML">

                        <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="https://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="320" height="240" id="stream" align="middle">
                            <param name="allowScriptAccess" value="sameDomain" />
                            <param name="movie" value="/components/com_sitesactivities/flash/stream.swf?streamURL=<?php echo $this->first_href?>/jpeg" />
                            <param name="quality" value="high" />
                            <param name="bgcolor" value="#cccccc" />
                            <embed src="/components/com_sitesactivities/flash/stream.swf?streamURL=<?php echo $this->first_href?>/jpeg&timeout=600" quality="high" bgcolor="#cccccc" width="320" height="240" name="stream" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="https://www.macromedia.com/go/getflashplayer" />
                        </object>

                    </div>

                    <div id="flash-title" style="width:320px"><?php echo $this->first_name; ?></div>

                </div>

                <div style="width:350px;float:left;">
                    <?php echo $this->href_thumbs; ?>
                </div>

            <?php
            }
            else
            {
            ?>

            <h3>No feeds available</h3>

            <?php
            }
            ?>

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


