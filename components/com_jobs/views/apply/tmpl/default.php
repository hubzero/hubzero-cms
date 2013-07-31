<?php 
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

	/* Application Form */

	// load some classes
	$jconfig = JFactory::getConfig();
	$sitename = $jconfig->getValue('config.sitename');
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
    	<li><?php echo JText::_('COM_JOBS_PLEASE').' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=view').'?action=login">'.JText::_('COM_JOBS_ACTION_LOGIN').'</a> '.JText::_('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
    <?php } else if($this->emp && $this->allowsubscriptions) {  ?>
    	<li><a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <li><a class="shortlist btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('COM_JOBS_SHORTLIST'); ?></a></li>
    <?php } else if($this->admin) { ?>
    	<li><?php echo JText::_('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
        	<a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_ADMIN_DASHBOARD'); ?></a></li>
	<?php } else { ?>  
    	<li><a class="alljobs btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>"><?php echo JText::_('COM_JOBS_ALL_JOBS'); ?></a></li>
    <?php } ?>  
</ul>
</div><!-- / #content-header-extra -->
<?php
	if (!$seeker) { ?>
		<p class="warning"><?php echo JText::_('COM_JOBS_APPLY_TO_APPLY').' '.$sitename.' '.JText::_('COM_JOBS_APPLY_NEED_RESUME') ?></p>
        <p>
			<?php echo '<a href="'. JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=resume').'" class="add">'.JText::_('COM_JOBS_ACTION_CREATE_PROFILE').'</a>'; ?>
        </p>
	<?php 
	return;
	}
		$html = '';

		$job->title = trim(stripslashes($job->title));
		$appid = $application->status !=2 ? $application->id : 0;
		$html .= '<div class="main section">'."\n";

		if((!$this->admin && $juser->get('id') == $job->employerid) or ($this->admin && $job->employerid == 1) ) {
		 $html .= '<p class="warning">'.JText::_('COM_JOBS_APPLY_WARNING_OWN_AD').'</p>'."\n";
		}

		$html .= '<div id="applyinfo">'."\n";
		$html .= '<h3>'.$job->title.' - ';
		$html .= preg_match('/(.*)http/i', $job->companyWebsite) ? '<a href="'.$job->companyWebsite.'">'.$job->companyName.'</a>' : $job->companyName;
		$html .= ', '.$job->companyLocation.', '.$job->companyLocationCountry.'<span>'.JText::_('COM_JOBS_JOB_REFERENCE_CODE').': '.$job->code.'</span></h3>'."\n";
		$html .= '</div>'."\n";

		// message to employer
		$html .= ' <form id="hubForm" method="post" action="index.php?option='.$this->option.'">'."\n";
		$html .= '<fieldset>'."\n";
		$html .= '	  <input type="hidden"  name="task" value="saveapp" />'."\n";
		$html .= '	  <input type="hidden" id="code" name="code" value="'.$job->code.'" />'."\n";
		$html .= '	  <input type="hidden" id="jid" name="jid" value="'.$job->id.'" />'."\n";
		$html .= '	  <input type="hidden" id="appid" name="appid" value="'.$appid.'" />'."\n";
		$html .= '	  <input type="hidden" id="uid" name="uid" value="'.$juser->get('id').'" />'."\n";
		$html .= '	  <h3>'.JText::_('COM_JOBS_APPLY_MSG_TO_EMPLOYER').' <span class="opt">('.JText::_('COM_JOBS_OPTIONAL').')</span></h3>'."\n";
		$html .= '	  <label>'."\n";
		$html .= '	  <textarea name="cover" id="cover" rows="10" cols="15">'.$application->cover.'</textarea>'."\n";
		$html .= '	  </label>'."\n";
		$html .= '</fieldset>'."\n";
		$html .= '<div class="explaination">'."\n";
		$html .= '<p>'.JText::_('COM_JOBS_APPLY_HINT_COVER_LETTER').'</p>'."\n";
		$html .= '</div>'."\n";
		$html .= ' <div class="clear"></div>'."\n";

		$html .= ' <div class="subject custom">'."\n";
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
		$html .= '</div>'."\n";
		$html .= '<p class="submit"><input type="submit" name="submit" value="';
		$html .= $this->task=='editapp' ? JText::_('COM_JOBS_ACTION_SAVE_CHANGES_APPLICATION') : JText::_('COM_JOBS_ACTION_APPLY_THIS_JOB');
		$html .= '" />';
		$html .= '<span class="cancelaction">';
		$html .= '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=job&id='.$job->code).'">';
		$html .= JText::_('COM_JOBS_CANCEL').'</a></span></p>'."\n";
		$html .= ' </form>'."\n";
		$html .= '</div>'."\n";

		echo $html;
 ?>