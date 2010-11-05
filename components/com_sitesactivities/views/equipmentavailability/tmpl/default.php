<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>



<script type="text/javascript">
<?php 

	switch(JRequest::getInt('id'))
	{
		case(226): // University of CA, Los Angeles
		echo 'calendar_feeds = new Array("", "", "", "", "");';
		break;
		
		case(228): // University of CA, Santa Barbara
		echo 'calendar_feeds = new Array("kv2gnvav0rkd32ndiehfvjm9kc%40group.calendar.google.com", "o39kavbe9nt9fp4dd1p96fdcfo%40group.calendar.google.com", "", "", "");';
		break;
		
		case(280): // University of TX, Austin</a></li>
		echo 'var calendar_feeds = new Array("neesutexas%40gmail.com", "8anc9tsq190q18pq4vqpn3dvio%40group.calendar.google.com", "1kpetivm71citf74bi5g9dre90%40group.calendar.google.com", "bplt294898sqbbjt0h7ur8qvg8%40group.calendar.google.com", "");';
		break;
		
		case(205): // Rensselaer Polytechnic Institute</a></li>
		echo 'calendar_feeds = new Array("nqfqltefehor89k1c3gai3j43o%40group.calendar.google.com", "stegt1efagjbo16ubqqd5kgj9s%40group.calendar.google.com", "jkgh8le97tj7tgn3kom62m53nk%40group.calendar.google.com", "76scj79cuhm0q8th2cm1k9an8g%40group.calendar.google.com", "");';
		break;
		
		case(276): // University of CA, Davis</a></li>
		echo 'calendar_feeds = new Array("", "", "", "", "");';
		break;
		
		case(180): // Cornell University</a></li>
		echo 'calendar_feeds = new Array("v6b55vtiobtdcpdifkpegdndoo%40group.calendar.google.com", "7bqa08obg9kcaqt7u267etsfv4%40group.calendar.google.com", "paj81rc709p6rmps7ms4mqaqgg%40group.calendar.google.com", "", "");';
		break;
		
		case(191): // Lehigh University</a></li>
		echo 'calendar_feeds = new Array("nees.lehigh%40gmail.com", "", "", "", "");';
		break;
		
		case(275): // University of CA, Berkeley</a></li>
		echo 'calendar_feeds = new Array("", "", "", "", "");';
		break;
		
		case(236): // University of IL, Urbana</a></li>
		echo 'calendar_feeds = new Array("", "", "", "", "");';
		break;
		
		case(244): // University of Minnesota</a></li>
		echo 'calendar_feeds = new Array("umn.edu_pcnb98trnprtvjv3bqoqdq65m0%40group.calendar.google.com", "umn.edu_6ob322ak5ul96ohj3j63dj51n8%40group.calendar.google.com", "umn.edu_o8i7ha131jdd43eih5qnsqs23k%40group.calendar.google.com", "", "");';
		break;
		
		case(274): // University at Buffalo, SUNY</a></li>
		echo 'calendar_feeds = new Array("9kt8ojejguobcfo1faojfb64m4%40group.calendar.google.com", "38a6d84vfka9pjbf4h5tntu00k%40group.calendar.google.com", "h1g452gocf1mreva0t4nntkp64%40group.calendar.google.com", "aos33igesvrt5j9agrk6896s4c%40group.calendar.google.com", "");';
		break;
		
		case(277): // University of CA, San Diego</a></li>
		echo 'calendar_feeds = new Array("diq0ts367mubj296lo8pf8aeis%40group.calendar.google.com", "bm1p1lvothi3hv5k8jhajh9i48%40group.calendar.google.com", "mdk8o5kafhsf8a11t75pqj82h0%40group.calendar.google.com", "", "");';
		break;
		
		case(279): // University of Nevada, Reno</a></li>
		echo 'calendar_feeds = new Array("88l9i1b9thu75vnv5gfuia91tc%40group.calendar.google.com", "9oc7d3sh7882h5d9grddcv64b0%40group.calendar.google.com", "omul7bkov7uirqgpi012f0f7uk%40group.calendar.google.com", "u545n97h69t804992i0pdt86gs%40group.calendar.google.com", "");';
		break;
		
		case(200): // Oregon State University</a></li>
		echo 'calendar_feeds = new Array("aeu07ou26kq36tqvmctvohjvh0%40group.calendar.google.com", "25krh9agm64tctn5srfnfq3g3k%40group.calendar.google.com", "", "", "");';
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

        <?php if($this->canedit) {?>
            <a href="/kb/siteactivities/sitecalendarsetup">How do I setup my calendar? </a>
        <?php } ?>
            
		<?php if ($this->facilityName != ''){?>
			
			<div id="calendar" class="calendar">
				<div style="text-align: center; line-height: 200px; color: gray;">Loading...</div>
				</div>
	
				<div id="calendar-key" class="calendar-key">
                                    <div id="key-container"><div id="key-color-0" class="key-color"></div><div id="key-0" class="key"></div></div><br/>
                                    <div id="key-container"><div id="key-color-1" class="key-color"></div><div id="key-1" class="key"></div></div><br/>
                                    <div id="key-container"><div id="key-color-2" class="key-color"></div><div id="key-2" class="key"></div></div><br/>
                                    <div id="key-container"><div id="key-color-3" class="key-color"></div><div id="key-3" class="key"></div></div><br/>
                                    <div id="key-container"><div id="key-color-4" class="key-color"></div><div id="key-4" class="key"></div></div><br/>
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


