<?php 
/**
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license	GNU General Public License, version 2 (GPLv2) 
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

	/* Intro */	
	$xhub =& XFactory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	
	$promoline = $this->config->get('promoline') ? $this->config->get('promoline') : '';
	$infolink = $this->config->get('infolink') ? $this->config->get('infolink') : '';
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($this->guest) { ?> 
    	<li><?php echo JText::_('PLEASE').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=view').'?action=login">'.JText::_('ACTION_LOGIN').'</a> '.JText::_('ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
    <?php } else if($this->emp && $this->allowsubscriptions) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('JOBS_SHORTLIST'); ?></a></li>
    <?php } else if($this->admin) { ?>
    	<li><?php echo JText::_('NOTICE_YOU_ARE_ADMIN'); ?>
        	<a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_ADMIN_DASHBOARD'); ?></a></li>
	<?php } else { ?>  
    	<li><a class="myresume" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=addresume'); ?>"><?php echo JText::_('JOBS_MY_RESUME'); ?></a></li>
    <?php } ?>  
</ul>
</div><!-- / #content-header-extra -->
<?php if($this->msg) { ?>
	<p class="help"><?php echo $this->msg; ?></p>
<?php } ?>  

<?php if($this->allowsubscriptions) { ?>
<div id="introduction" class="section">
	<div class="aside">
		<h3><?php echo JText::_('JOBS_QUESTIONS_LINK'); ?></h3>
		<p><?php echo '<a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a> '.JText::_('ABOUT_THE_PROCESS'); ?></p>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="three columns first">
			<p class="intronote"><?php echo JText::_('TIP_ENJOY_COMMUNITY_EXPOSURE').' '.$hubShortName.'. '.JText::_('TIP_SERVICES_FREE'); echo ' '.JText::_('TIP_EMPLOYERS_SUBSCRIPTION_REQUIRED'); ?></p>
		</div>
		<div class="three columns second">
			<h3><?php echo JText::_('EMPLOYERS'); ?></h3>           
			<ul>
            	<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes'); ?>"><?php echo JText::_('ACTION_BROWSE_RESUMES'); ?></a></li>
				<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=addjob'); ?>"><?php echo JText::_('ACTION_POST_JOB'); ?></a></li>                
			</ul>
             <?php if($promoline) { ?> 
            <p class="promo"><?php echo $promoline; ?></p>   
            <?php } ?>     
		</div>
        <div class="three columns third">
			<h3><?php echo JText::_('SEEKERS'); ?></h3>
           
			<ul>
           		<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=browse'); ?>"><?php echo JText::_('ACTION_BROWSE_JOBS'); ?></a></li>
				<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=addresume'); ?>"><?php echo JText::_('ACTION_POST_RESUME'); ?></a></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->
<?php } ?>