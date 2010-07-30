<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript" src="/components/com_sitesactivities/js/siteactivities.js"></script>

<div id="mainpage-facilities-header">
	<h2>Site Activities</h2>
</div>

<?php echo $this->tabs;?>

<div id="mainpage-facilities-main">

	<div id="mainpage-facilities-lhs">
		<ul>
			<li class="facility-type-header-li">Field Experiments/Monitoring</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(226);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=226')?>">University of CA, Los Angeles</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(228);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=228')?>">University of CA, Santa Barbara</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(280);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=280')?>">University of TX, Austin</a></li>
				</ul>
			<li class="facility-type-header-li">Geotechnical Centrifuges</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(205);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=205')?>">Rensselaer Polytechnic Institute</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(276);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=276')?>">University of CA, Davis</a></li>
				</ul>
			<li class="facility-type-header-li">Large Scale Laboratories</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(180);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=180')?>">Cornell University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(191);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=191')?>">Lehigh University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(275);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=275')?>">University of CA, Berkeley</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(236);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=236')?>">University of IL, Urbana</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(244);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=244')?>">University of Minnesota</a></li>
				</ul>
			<li class="facility-type-header-li">Shake Tables</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(274);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=274')?>">University at Buffalo, SUNY</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(277);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=277')?>">University of CA, San Diego</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(279);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=279')?>">University of Nevada, Reno</a></li>
				</ul>
			<li class="facility-type-header-li">Tsunami Wave Basins</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(200);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments&id=200')?>">Oregon State University</a></li>
				</ul>
		</ul>
	</div>
	
	<div id="mainpage-facilities-rhs">

	<h2><?php echo $this->facilityName . ' experiments';?></h2>
        <hr>

        <span style="font-weight:bold;">Current Site Status: </span><?php echo $this->translatedStatus; ?>

        <?php
        if($this->canedit)
        {
            echo '<a href="' . JRoute::_('/index.php?option=com_sitesactivities&view=editsitestatus&id=' . $this->facilityID) . '">[change]</a>';
        }
        ?>

        <?php 
        if ($this->feedcount > 0 )
        {?>
            <h3> Experiment Video Feeds </h3>
            <hr/>

            <div style="width: 60%; height:100px">
                <div class="loading" style="width:49%; float:left">
                    <img class="block" src="<?php echo $this->first_href?>/jpeg" width="105" height="85" title="" onerror="ImageReload()" alt="" />
                </div>

                <div style="width:50%; float:right; color:#FF6600; font-size:20px; padding-bottom: 5px;">
                    <?php echo $this->feedcount;?> Active Feeds
                </div>

                <div style="width:50%; float:right;">
                    <a style="margin-top:10px; text-align:center;" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=videofeeds&id=' . $this->facilityID)?>">View Video Feeds</a>
                </div>
            </div>

        <?php 
        }
        ?>

            <h3> Experiments Summary </h3>
            <hr/>
            <div style="width:250px; border: solid 1px #aaa; padding-top:2px; margin-top:5px;">
                <table style="border:none">
                    <tr><th style="background-color: #ddd;">Phase</th><th style="background-color: #ddd;">Experiments</th></tr>
                    <?php echo $this->phasetable; ?>

                </table>
            </div>

            <h3>Experiment List</h3>
            <hr style="margin-bottom:25px">
            <?php echo $this->explist_act; ?>

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


