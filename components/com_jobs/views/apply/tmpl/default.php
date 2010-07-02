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
	
	/* Application Form */
	
	// load some classes
	$xhub =& XFactory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	$juser 	  =& JFactory::getUser();
	
	$job = $this->job;
	$seeker = $this->seeker;
	$application = $this->application;	
	$owner = ($juser->get('id') == $job->employerid or $this->admin) ? 1 : 0;	
	
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($juser->get('guest')) { ?> 
    	<li><?php echo JText::_('PLEASE').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=view').'?action=login">'.JText::_('ACTION_LOGIN').'</a> '.JText::_('ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
    <?php } else if($this->emp && $this->allowsubscriptions) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('JOBS_SHORTLIST'); ?></a></li>
    <?php } else if($this->admin) { ?>
    	<li><?php echo JText::_('NOTICE_YOU_ARE_ADMIN'); ?>
        	<a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_ADMIN_DASHBOARD'); ?></a></li>
	<?php } else { ?>  
    	<li><a class="alljobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=browse'); ?>"><?php echo JText::_('JOBS_ALL_JOBS'); ?></a></li>
    <?php } ?>  
</ul>
</div><!-- / #content-header-extra -->
<?php
	if (!$seeker) { ?>
		<p class="warning"><?php echo JText::_('APPLY_TO_APPLY').' '.$hubShortName.' '.JText::_('APPLY_NEED_RESUME') ?></p>
        <p>
			<?php echo '<a href="'. JRoute::_('index.php?option=com_members'.a.'id='.$juser->get('id').a.'active=resume').'" class="add">'.JText::_('ACTION_CREATE_PROFILE').'</a>'; ?>
        </p>
	<?php 
	return; 
	}		
		$html = '';
		
		$job->title = trim(stripslashes($job->title));
		$appid = $application->status!=2 ? $application->id : 0;
		$html .= '<div class="main section">'.n;
		
		if((!$this->admin && $juser->get('id') == $job->employerid) or ($this->admin && $job->employerid == 1) ) {
		 $html .= t.'<p class="warning">'.JText::_('APPLY_WARNING_OWN_AD').'</p>'.n;
		}
				
		$html .= t.'<div id="applyinfo">'.n;	
		$html .= t.t.'<h3>'.$job->title.' - ';
		$html .= preg_match('/(.*)http/i', $job->companyWebsite) ? '<a href="'.$job->companyWebsite.'">'.$job->companyName.'</a>' : $job->companyName;
		$html .= ', '.$job->companyLocation.', '.$job->companyLocationCountry.'<span>'.JText::_('JOB_REFERENCE_CODE').': '.$job->code.'</span></h3>'.n;
		$html .= t.'</div>'.n;
				
		// message to employer
		$html .= t.t.' <form id="hubForm" method="post" action="index.php?option='.$this->option.'">'.n;
		$html .= t.t.t.'<fieldset>'.n;
		$html .= t.t.t.'	  <input type="hidden"  name="task" value="saveapp" />'.n;
		$html .= t.t.t.'	  <input type="hidden" id="code" name="code" value="'.$job->code.'" />'.n;	
		$html .= t.t.t.'	  <input type="hidden" id="jid" name="jid" value="'.$job->id.'" />'.n;	
		$html .= t.t.t.'	  <input type="hidden" id="appid" name="appid" value="'.$appid.'" />'.n;	
		$html .= t.t.t.'	  <input type="hidden" id="uid" name="uid" value="'.$juser->get('id').'" />'.n;	
		$html .= t.t.t.'	  <h3>'.JText::_('APPLY_MSG_TO_EMPLOYER').' <span class="opt">('.JText::_('OPTIONAL').')</span></h3>'.n;
		$html .= t.t.t.'	  <label>'.n;
		$html .= t.t.t.'	  <textarea name="cover" id="cover" rows="10" cols="15">'.$application->cover.'</textarea>'.n;
		$html .= t.t.t.'	  </label>'.n;
		$html .= t.t.t.'</fieldset>'.n;
		$html .= t.t.t.'<div class="explaination">'.n;
		$html .= t.t.t.t.'<p>'.JText::_('APPLY_HINT_COVER_LETTER').'</p>'.n;
		$html .= t.t.t.'</div>'.n;		
		$html .= t.t.t.' <div class="clear"></div>'.n; 	
		
		$html .= t.t.t.' <div class="subject custom">'.n; 	
		// profile info
		if($seeker) {
			JPluginHelper::importPlugin( 'members','resume' );
			$dispatcher =& JDispatcher::getInstance();
			// show seeker info
			$out   = $dispatcher->trigger( 'showSeeker', array($seeker, $this->emp, $this->admin, 'com_members', $list=0) );
			if (count($out) > 0) {
				$html .= $out[0];
			}
		}
		$html .= t.t.t.'</div>'.n;			
		$html .= t.'<p class="submit"><input type="submit" name="submit" value="';
		$html .= $this->task=='editapp' ? JText::_('ACTION_SAVE_CHANGES_APPLICATION') : JText::_('ACTION_APPLY_THIS_JOB');
		$html .= '" />';
		$html .= '<span class="cancelaction">';
		$html .= '<a href="'.JRoute::_('index.php?option='.$this->option.a.'task=job'.a.'id='.$job->code).'">';
		$html .= JText::_('CANCEL').'</a></span></p>'.n;
		$html .= t.t.t.' </form>'.n;
		$html .= '</div>'.n;
		
		echo $html;
 ?>