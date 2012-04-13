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

	/* Post New Job / Edit Job Form */

	// load some classes
	$xhub =& Hubzero_Factory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	$juser 	  =& JFactory::getUser();

	$job = $this->job;
	$employer = $this->employer;
	$profile = $this->profile;
	$id = $this->jobid;

	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->startdate, '%Y-%m-%d',0) : '';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->closedate, '%Y-%m-%d',0) : '';

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
		$html .= t.t.t.' <form id="hubForm" method="post" action="index.php?option='.$this->option.'">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('EDITJOB_OVERVIEW_INFO').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('EDITJOB_JOB_OVERVIEW').'</h3>'.n;
		$html .= t.t.t.'	  <input type="hidden"  name="task" value="savejob" />'.n;
		$html .= t.t.t.'	  <input type="hidden"  name="code" value="'.$job->code.'" />'.n;
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
						$selected = $job->companyLocationCountry ? $job->companyLocationCountry : 'United States'; 
						$html .= "\t\t\t\t".' <option value="' . htmlentities($country['name']) . '"';
						if($country['name'] == strtoupper($selected) || $country['name'] == $selected) {
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
		ximport('Hubzero_Wiki_Editor');
		$editor =& Hubzero_Wiki_Editor::getInstance();
		$html .= $editor->display('description', 'description', $job->description, 'no-image-macro no-file-macro', '10', '25');
		$html .= t.t.t.'	 </fieldset>'.n;

		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('EDITJOB_DESC_INFO').'</p>'.n;
		//$html .= JobsHtml::wikiHelp();
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
		$html .= t.t.t.'<label>'.JText::_('EDITJOB_CLOSE_DATE').':'.n;
		$html .= t.t.t.t.'<input  type="text" class="option level" name="closedate" id="closedate" size="10" maxlength="10" value="'.$closedate.'" /> <span class="hint">'.JText::_('EDITJOB_HINT_DATE_FORMAT') . '</span>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.JText::_('EDITJOB_EXTERNAL_URL').':'.n;
		$html .= t.t.t.t.'<input  type="text" name="applyExternalUrl" size="100" maxlength="250" value="'.$job->applyExternalUrl.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'<input type="checkbox" class="option" name="applyInternal" value="1" ';
		$html .= $job->applyInternal ? 'checked="checked" ' : '';
		$html .= ' /> ';
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