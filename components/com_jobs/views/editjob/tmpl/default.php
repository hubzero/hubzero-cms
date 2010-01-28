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
	
	/* Post New Job Form */
	
	// load some classes
	$xhub =& XFactory::getHub();
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

<?php if($this->emp) {  ?>
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Employer Dashboard'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('Candidate Shortlist'); ?></a></li>  
	</ul>
</div><!-- / #content-header-extra -->
 <?php } ?>  

<?php
		if($this->error) { echo $this->error; }
		$html = '';
		
		$job->title = trim(stripslashes($job->title));
		$job->description = trim(stripslashes($job->description));
		$job->description = preg_replace('/<br\\s*?\/??>/i', "", $job->description);
		$job->description = JobsHtml::txt_unpee($job->description);
		$job->companyLocation = $id ? $job->companyLocation : $employer->companyLocation;
		$job->companyLocationCountry = $id ? $job->companyLocationCountry : htmlentities(getcountry($profile->get('countryresident')));
		$job->companyName = $id ? $job->companyName : $employer->companyName;
		$job->companyWebsite = $id ? $job->companyWebsite : $employer->companyWebsite;
		$usonly = (isset($this->config->parameters['usonly'])) ? $this->config->parameters['usonly'] : 0;
		
		$html .= '<div class="main section">'.n;
		$html .= $this->getError() ? '<p class="error">'.$this->getError().'</p>' : '';
					
		//$html .= t.'<div class="aside">'.n;
		//$html .= t.'<p>'.JText::_('TEXT_ADD_WISH').'</p>'.n;
		//$html .= t.'</div><!-- / .aside -->'.n;
		//$html .= t.'<div class="subject">'.n;
		$html .= t.t.t.' <form id="hubForm" method="post" action="index.php?option='.$this->option.'">'.n;
		$html .= t.'<div class="explaination">'.n;
		//$html .= t.'<div class="exp_wrap">'.n;
		$html .= t.t.'<p>'.JText::_('Please specify job title and location, as well as provide some general information about the hiring company.').'</p>'.n;

		//$html .= t.t.'<p>'.JText::_('Please specify job title and location, as well as provide some general information about the hiring company.').'</p>'.n;
		//if($this->task == 'addjob') { 
		//$html .= t.t.'<h3>'.JText::_('What is Next?').'</h3>'.n;
		//$html .= t.t.'<h4>'.JText::_('Review Ad and Confirm').'</h4>'.n;
		//$html .= t.t.'<p>'.JText::_('Lorem ipsum about payment process').'</p>'.n;
		//}
		//$html .= t.'</div>'.n;
		$html .= t.'</div>'.n;
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('Job Overview').'</h3>'.n;
		$html .= t.t.t.'	  <input type="hidden"  name="task" value="savejob" />'.n;
		$html .= t.t.t.'	  <input type="hidden" id="id" name="id" value="'.$id.'" />'.n;	
		$html .= t.t.t.'	  <input type="hidden" name="status" value="'.$status.'" />'.n;
		$html .= t.t.t.'	  <input type="hidden" name="employerid" value="'.$this->uid.'" />'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Job Title').': <span class="required">required</span>'.n;
		$html .= t.t.t.'	  <input name="title" maxlength="190" id="title" type="text" value="'.$job->title.'" /></label>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Job Location (City, State)').': <span class="required">required</span>'.n;
		$html .= t.t.t.'	  <input name="companyLocation" maxlength="190" id="companyLocation" type="text" value="'.$job->companyLocation.'" /></label>'.n;
		if(!$usonly) 
		{
		$html .= t.t.t.'	  <label>'.JText::_('Country').': <span class="required">required</span>'.n;
		$html .= "\t\t\t\t".'<select name="companyLocationCountry" id="companyLocationCountry">'."\n";

		$html .= "\t\t\t\t".' <option value="">(select from list)</option>'."\n";
		$countries = getcountries();
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
		$html .= t.t.t.'	  <p class="hint">'.JText::_('Only US-based jobs can be advertised on this site.').'</p>'.n;	
		$html .= t.t.t.'	  <input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />'.n;	
		}
		//$html .= t.t.t.'	  <p class="hint">'.JText::_('Please inlcude country name or its common abbreviation, e.g. West Lafayette, IN, USA').'</p>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Company Name').': <span class="required">required</span>'.n;
		$html .= t.t.t.'	  <input name="companyName" maxlength="120" id="companyName" type="text" value="'.$job->companyName.'" /></label>'.n;
		//$html .= t.t.'<div class="group">'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Company Website').': '.n;
		$html .= t.t.t.'	  <input name="companyWebsite" maxlength="190" id="companyWebsite" type="text" value="'.$job->companyWebsite.'" /></label>'.n;
		$html .= t.t.t.'	  <p class="hint">'.JText::_('Main company url, rather than application link').'</p>'.n;	
		//$html .= t.t.'</div>'.n;
		
		
		$html .= t.t.t.'	 </fieldset>'.n;
		
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('Job Description').' <br/> <span class="required">required</span></h3>'.n;
		$html .= t.t.t.'	  <label><p class="hint"> ('.JText::_('Please use').' <a href="/topics/Help:WikiFormatting" rel="external">'.JText::_('Wiki Formatting').'</a> '.JText::_('to compose the job description').')</p> '.n;	
		$html .= t.t.t.'     <ul id="wiki-toolbar" class="hidden"></ul>'.n;
		$html .= t.t.t.'<textarea name="description" id="description" rows="40" cols="35">';
		$html .= $job->description;
		$html .= '</textarea>'.n;
		$html .= '</label>'.n;
		$html .= t.t.t.'	 </fieldset>'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('Use this space to describe the advertised position, job requirements and expectations from a candidate.').'</p>'.n;
		$html .= JobsHtml::wikiHelp();
		$html .= t.'</div>'.n;	
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('Job Specifics').'</h3>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Job Category').': '.n;
		$html .= JobsHtml::formSelect('cid', $this->cats, $job->cid, '', '');
		$html .= t.t.t.'	  </label>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Job Type').': '.n;
		$html .= JobsHtml::formSelect('type', $this->types, $job->type, '', '');
		$html .= t.t.t.'	  </label>'.n;
		$html .= t.t.t.'<label>'.JText::_('Position Start Date').':'.n;
		$html .= t.t.t.t.'<input type="text" class="option level" name="startdate" id="startdate" size="10" maxlength="10" value="'.$startdate.'" /> <span class="hint">'.JText::_('Date format: yyyy-mm-dd'). '</span>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.JText::_('Applications Due').':'.n;
		$html .= t.t.t.t.'<input  type="text" class="option level" name="closedate" id="closedate" size="10" maxlength="10" value="'.$closedate.'" /> <span class="hint">'.JText::_('Date format: yyyy-mm-dd'). '&nbsp;&nbsp;&nbsp;&nbsp;'.JText::_('- Will default to \'ASAP\' when left blank').'</span>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.JText::_('External URL for a job application (optional)').':'.n;
		$html .= t.t.t.t.'<input  type="text" name="applyExternalUrl" size="100" maxlength="250" value="'.$job->applyExternalUrl.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'<input type="checkbox" class="option" name="applyInternal"  size="10" maxlength="10" value="1" checked="checked" /> ';
		$html .= JText::_('Allow users to apply to your ad via this hub.').n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'	 </fieldset>'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('Please choose most appropriate job category and/or type, helping users find your ad easier. Specify a starting date for the job, as well as a deadline for applications. You can choose to have users apply through this site or via an external URL. Make sure you provide at least one application option.').'</p>'.n;
		$html .= t.'</div>'.n;				
		
		$html .= t.t.t.'	 <fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('Contact Information').'<br /><span>'.JText::_('(optional)').'</span></h3>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Contact Name').': '.n;
		$html .= t.t.t.'	  <input name="contactName" maxlength="100"  type="text" value="';
		$html .= $job->contactName ? $job->contactName : $profile->get('name');
		$html .='" /></label>'.n;	
		$html .= t.t.t.'	  <label>'.JText::_('Contact Email').': '.n;
		$html .= t.t.t.'	  <input name="contactEmail" maxlength="100"  type="text" value="';
		$html .= $job->contactEmail ? $job->contactEmail : $profile->get('email');
		$html .= '" /></label>'.n;
		$html .= t.t.t.'	  <label>'.JText::_('Contact Phone').': '.n;
		$html .= t.t.t.'	  <input name="contactPhone" maxlength="100"  type="text" value="';
		$html .= $job->contactPhone ? $job->contactPhone : $profile->get('phone');
		$html .= '" /></label>'.n;
		$html .= t.'<p class="submit"><input type="submit" name="submit" value="';
		$html .= ($this->task=='addjob' or $job->status == 4) ? JText::_('Save as draft and Preview') : JText::_('Save');
		$html .= '" />';
		$html .= '<span class="cancelaction">';
		$html .= '<a href="'.JRoute::_('index.php?option='.$this->option.a.'task=dashboard').'">';
		$html .= JText::_('CANCEL').'</a></span></p>'.n;					
		$html .= t.t.t.'	 </fieldset>'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('Specify a person to contact for more information about this job.').'</p>'.n;
		$html .= t.'</div>'.n;	
		
		
		$html .= t.t.t.' </form>'.n;
		$html .= t.'</div>'.n;
		//$html .= '</div><div class="clear"></div>'.n;	
		
		echo $html;

?>