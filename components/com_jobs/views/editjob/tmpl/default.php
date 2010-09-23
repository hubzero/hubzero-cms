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
	
	/* Post New Job / Edit Job Form */
	
	// load some classes
	$xhub =& Hubzero_Factory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	$juser 	  =& JFactory::getUser();
	
	$job = $this->job;
	$employer = $this->employer;
	$profile = $this->profile;
	$id = $this->jobid;
	
	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->startdate, '%Y-%m-%d') : '';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->closedate, '%Y-%m-%d') : '';
	
	$status = $this->task != 'addjob' ? $job->status : 4; // draft mode	
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($this->emp) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('JOBS_SHORTLIST'); ?></a></li>
     <?php } else { ?> 
     <li><?php echo JText::_('NOTICE_YOU_ARE_ADMIN'); ?>
        	<a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_ADMIN_DASHBOARD'); ?></a></li> 
     <?php } ?>      
	</ul>
</div><!-- / #content-header-extra -->

<?php
	if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
	<?php } 
		$html = '';
		
		$job->title = trim(stripslashes($job->title));
		$job->description = trim(stripslashes($job->description));
		$job->description = preg_replace('/<br\\s*?\/??>/i', "", $job->description);
		$job->description = JobsHtml::txt_unpee($job->description);
		$job->companyLocation = $id ? $job->companyLocation : $employer->companyLocation;
		$job->companyLocationCountry = $id ? $job->companyLocationCountry : htmlentities(Hubzero_Geo::getcountry($profile->get('countryresident')));
		$job->companyName = $id ? $job->companyName : $employer->companyName;
		$job->companyWebsite = $id ? $job->companyWebsite : $employer->companyWebsite;
		$usonly = (isset($this->config->parameters['usonly'])) ? $this->config->parameters['usonly'] : 0;
		
		$html .= '<div class="main section">'.n;
		$html .= $this->getError() ? '<p class="error">'.$this->getError().'</p>' : '';
					
		$html .= t.t.t.' <form id="hubForm" method="post" action="index.php?option='.$this->option.'">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('EDITJOB_OVERVIEW_INFO').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('EDITJOB_JOB_OVERVIEW').'</h3>'.n;
		$html .= t.t.t.'	  <input type="hidden"  name="task" value="savejob" />'.n;
		$html .= t.t.t.'	  <input type="hidden" id="id" name="id" value="'.$id.'" />'.n;	
		$html .= t.t.t.'	  <input type="hidden" name="status" value="'.$status.'" />'.n;
		$html .= t.t.t.'	  <input type="hidden" name="employerid" value="'.$this->uid.'" />'.n;
		$html .= t.t.t.'	  <label>'.JText::_('EDITJOB_JOB_TITLE').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.'	  <input name="title" maxlength="190" id="title" type="text" value="'.$job->title.'" /></label>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('EDITJOB_JOB_LOCATION').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.'	  <input name="companyLocation" maxlength="190" id="companyLocation" type="text" value="'.$job->companyLocation.'" /></label>'.n;
		if(!$usonly) {
			$html .= t.t.t.'	 <label>'.JText::_('EDITJOB_COUNTRY').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
			$html .= "\t\t\t\t".'<select name="companyLocationCountry" id="companyLocationCountry">'."\n";	
			$html .= "\t\t\t\t".' <option value="">'.JText::_('OPTION_SELECT_FROM_LIST').'</option>'."\n";
			$countries = Hubzero_Geo::getcountries();
				foreach($countries as $country) {
						$html .= "\t\t\t\t".' <option value="' . htmlentities($country['name']) . '"';
						if($country['name'] == $job->companyLocationCountry) {
							$html .= ' selected="selected"';
						}
						$html .= '>' . htmlentities($country['name']) . '</option>'."\n";
				}
			$html .= t.t.t.t.'</select></label>'.n;
		}
		else {
			$html .= t.t.t.'	  <p class="hint">'.JText::_('EDITJOB_US_ONLY').'</p>'.n;	
			$html .= t.t.t.'	  <input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />'.n;	
		}
		$html .= t.t.t.'	  <label>'.JText::_('EMPLOYER_COMPANY_NAME').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.'	  <input name="companyName" maxlength="120" id="companyName" type="text" value="'.$job->companyName.'" /></label>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('EMPLOYER_COMPANY_WEBSITE').': '.n;
		$html .= t.t.t.'	  <input name="companyWebsite" maxlength="190" id="companyWebsite" type="text" value="'.$job->companyWebsite.'" /></label>'.n;
		$html .= t.t.t.'	  <p class="hint">'.JText::_('EDITJOB_HINT_COMPANY').'</p>'.n;			
		$html .= t.t.t.'	 </fieldset>'.n;
				
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('EDITJOB_JOB_DESCRIPTION').' <br/> <span class="required">'.JText::_('REQUIRED').'</span></h3>'.n;
		$html .= t.t.t.'	  <label><p class="hint"> ('.JText::_('EDITJOB_PLEASE_USE').' <a href="/topics/Help:WikiFormatting" rel="external">'.JText::_('WIKI_FORMATTING').'</a> '.JText::_('EDITJOB_TO_COMPOSE').')</p> '.n;	
		$html .= t.t.t.'     <ul id="wiki-toolbar" class="hidden"></ul>'.n;
		$html .= t.t.t.'		<textarea name="description" id="description" rows="40" cols="35">';
		$html .= 					$job->description;
		$html .= '				</textarea>'.n;
		$html .= '</label>'.n;
		$html .= t.t.t.'	 </fieldset>'.n;
		
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('EDITJOB_DESC_INFO').'</p>'.n;
		$html .= JobsHtml::wikiHelp();
		$html .= t.'</div>'.n;	
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('EDITJOB_JOB_SPECIFICS').'</h3>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('EDITJOB_CATEGORY').': '.n;
		$html .= JobsHtml::formSelect('cid', $this->cats, $job->cid, '', '');
		$html .= t.t.t.'	  </label>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('EDITJOB_TYPE').': '.n;
		$html .= JobsHtml::formSelect('type', $this->types, $job->type, '', '');
		$html .= t.t.t.'	  </label>'.n;
		$html .= t.t.t.'<label>'.JText::_('EDITJOB_START_DATE').':'.n;
		$html .= t.t.t.t.'<input type="text" class="option level" name="startdate" id="startdate" size="10" maxlength="10" value="'.$startdate.'" /> <span class="hint">'.JText::_('EDITJOB_HINT_DATE_FORMAT'). '</span>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.JText::_('EDITJOB_APPLICATIONS_DUE').':'.n;
		$html .= t.t.t.t.'<input  type="text" class="option level" name="closedate" id="closedate" size="10" maxlength="10" value="'.$closedate.'" /> <span class="hint">'.JText::_('EDITJOB_HINT_DATE_FORMAT'). '&nbsp;&nbsp;&nbsp;&nbsp;'.JText::_('EDITJOB_HINT_DATE').'</span>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.JText::_('EDITJOB_EXTERNAL_URL').':'.n;
		$html .= t.t.t.t.'<input  type="text" name="applyExternalUrl" size="100" maxlength="250" value="'.$job->applyExternalUrl.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'<input type="checkbox" class="option" name="applyInternal"  size="10" maxlength="10" value="1" checked="checked" /> ';
		$html .= JText::_('EDITJOB_ALLOW_INTERNAL_APPLICATION').n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'	 </fieldset>'.n;
		
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('EDITJOB_SPECIFICS_INFO').'</p>'.n;
		$html .= t.'</div>'.n;						
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('EDITJOB_CONTACT_INFO').'<br /><span>('.JText::_('OPTIONAL').')</span></h3>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('EDITJOB_CONTACT_NAME').': '.n;
		$html .= t.t.t.'	  <input name="contactName" maxlength="100"  type="text" value="';
		$html .= $job->contactName ? $job->contactName : $profile->get('name');
		$html .='" /></label>'.n;	
		$html .= t.t.t.'	  <label>'.JText::_('EDITJOB_CONTACT_EMAIL').': '.n;
		$html .= t.t.t.'	  <input name="contactEmail" maxlength="100"  type="text" value="';
		$html .= $job->contactEmail ? $job->contactEmail : $profile->get('email');
		$html .= '" /></label>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('EDITJOB_CONTACT_PHONE').': '.n;
		$html .= t.t.t.'	  <input name="contactPhone" maxlength="100"  type="text" value="';
		$html .= $job->contactPhone ? $job->contactPhone : $profile->get('phone');
		$html .= '" /></label>'.n;
		$html .= t.'<p class="submit"><input type="submit" name="submit" value="';
		$html .= ($this->task=='addjob' or $job->status == 4) ? JText::_('ACTION_SAVE_PREVIEW') : JText::_('ACTION_SAVE');
		$html .= '" />';
		$html .= '<span class="cancelaction">';
		$html .= '<a href="'.JRoute::_('index.php?option='.$this->option.a.'task=dashboard').'">';
		$html .= JText::_('CANCEL').'</a></span></p>'.n;					
		$html .= t.t.t.'	 </fieldset>'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('EDITJOB_CONTACT_DETAILS').'</p>'.n;
		$html .= t.'</div>'.n;			
		$html .= t.t.t.' </form>'.n;
		$html .= t.'</div>'.n;
			
		echo $html;
?>