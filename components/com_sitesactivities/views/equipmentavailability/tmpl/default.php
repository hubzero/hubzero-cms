<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>



<script type="text/javascript">
<?php 

	switch(JRequest::getInt('id'))
	{
		case(226): // University of CA, Los Angeles
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;

		case(228): // University of CA, Santa Barbara
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(280): // University of TX, Austin</a></li>
		echo 'var calendar_feeds = new Array("neesutexas%40gmail.com", "8anc9tsq190q18pq4vqpn3dvio%40group.calendar.google.com", "1kpetivm71citf74bi5g9dre90%40group.calendar.google.com", "bplt294898sqbbjt0h7ur8qvg8%40group.calendar.google.com");';
		break;
		
		case(205): // Rensselaer Polytechnic Institute</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(276): // University of CA, Davis</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(180): // Cornell University</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(191): // Lehigh University</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(275): // University of CA, Berkeley</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(236): // University of IL, Urbana</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(244): // University of Minnesota</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(274): // University at Buffalo, SUNY</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(277): // University of CA, San Diego</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(279): // University of Nevada, Reno</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		
		case(200): // Oregon State University</a></li>
		echo 'calendar_feeds = new Array("", "", "", "");';
		break;
		}

?>

</script>

<?php 
	if ($this->facilityName != '')
		echo '<script type="text/javascript" src="/components/com_sitesactivities/js/monthly_equip_cal.js"></script>';
?>


<div id="mainpage-facilities-header">
	<h2>NEES Site Activities</h2>
</div>


<?php echo $this->tabs;?>

<div id="mainpage-facilities-main">

	<div id="mainpage-facilities-lhs">
		<ul>
			<li class="facility-type-header-li">Field Experiments/Monitoring</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(226);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=226')?>">University of CA, Los Angeles</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(228);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=228')?>">University of CA, Santa Barbara</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(280);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=280')?>">University of TX, Austin</a></li>
				</ul>
			<li class="facility-type-header-li">Geotechnical Centrifuges</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(205);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=205')?>">Rensselaer Polytechnic Institute</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(276);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=276')?>">University of CA, Davis</a></li>
				</ul>
			<li class="facility-type-header-li">Large Scale Laboratories</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(180);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=180')?>">Cornell University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(191);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=191')?>">Lehigh University</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(275);?> "href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=275')?>">University of CA, Berkeley</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(236);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=236')?>">University of IL, Urbana</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(244);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=244')?>">University of Minnesota</a></li>
				</ul>
			<li class="facility-type-header-li">Shake Tables</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(274);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=274')?>">University at Buffalo, SUNY</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(277);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=277')?>">University of CA, San Diego</a></li>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(279);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=279')?>">University of Nevada, Reno</a></li>
				</ul>
			<li class="facility-type-header-li">Tsunami Wave Basins</li>
				<ul>
					<li class="facility-header-li"><a class="<?php echo SelectedStyleSheet(200);?>" href="<?php echo JRoute::_('/index.php?option=com_sitesactivities&view=equipmentavailability&id=200')?>">Oregon State University</a></li>
				</ul>
		</ul>
	</div>
	
	<div id="mainpage-facilities-rhs">

	<h2>
	<?php echo $this->facilityName . ' major equipment schedule'; ?>
	</h2>


		<?php if ($this->facilityName != ''){?>
			
			<div id="calendar" class="calendar">
				<div style="text-align: center; line-height: 200px; color: gray;">Loading...</div>
				</div>
	
				<div id="calendar-key" class="calendar-key">
						<div id="key-container"><div id="key-color-0" class="key-color"></div><div id="key-0" class="key"></div></div>
						<div id="key-container"><div id="key-color-1" class="key-color"></div><div id="key-1" class="key"></div></div>
						<div id="key-container"><div id="key-color-2" class="key-color"></div><div id="key-2" class="key"></div></div>
						<div id="key-container"><div id="key-color-3" class="key-color"></div><div id="key-3" class="key"></div></div>
				</div>
	
			</div>

		<?php }?>


	</div>

</div>


<div id="event-info" class="event-info">
  	<div id="event-info-header" class="event-info-header"></div>
  	<div id="event-info-body" class="event-info-body"></div>
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


