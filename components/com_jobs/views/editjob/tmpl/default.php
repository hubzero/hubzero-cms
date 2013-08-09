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

	$dateFormat = "%Y-%m-%d";
	$tz = 0;

	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$dateFormat = "Y-m-d";
		$tz = false;
	}

	/* Post New Job / Edit Job Form */

	// load some classes
	$jconfig = JFactory::getConfig();
	$sitename = $jconfig->getValue('config.sitename');
	$juser 	  =& JFactory::getUser();

	$job = $this->job;
	$employer = $this->employer;
	$profile = $this->profile;
	$id = $this->jobid;

	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? JHTML::_('date', $job->startdate, $dateFormat, $tz) : '';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? JHTML::_('date', $job->closedate, $dateFormat, $tz) : '';

	$status = $this->task != 'addjob' ? $job->status : 4; // draft mode	
	
	$countries = Hubzero_Geo::getcountries();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
	<?php if($this->emp) {  ?>
		<li><a class="myjobs btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
		<li><a class="shortlist btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resumes&filterby=shortlisted'); ?>"><?php echo JText::_('COM_JOBS_SHORTLIST'); ?></a></li>
	 <?php } else { ?> 
	 	<li>
			<!-- <?php echo JText::_('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?> -->
			<a class="myjobs btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_ADMIN_DASHBOARD'); ?></a>
		</li> 
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
		$job->companyLocationCountry = $id ? $job->companyLocationCountry : $this->escape(Hubzero_Geo::getcountry($profile->get('countryresident')));
		$job->companyName = $id ? $job->companyName : $employer->companyName;
		$job->companyWebsite = $id ? $job->companyWebsite : $employer->companyWebsite;
		$usonly = (isset($this->config->parameters['usonly'])) ? $this->config->parameters['usonly'] : 0;
?>
<div class="main section">
	<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
		<div class="explaination">
			<p><?php echo JText::_('COM_JOBS_EDITJOB_OVERVIEW_INFO'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_JOBS_EDITJOB_JOB_OVERVIEW'); ?></legend>
			
			<input type="hidden" name="task" value="savejob" />
			<input type="hidden" name="code" value="<?php echo $job->code; ?>" />
			<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="status" value="<?php echo $status; ?>" />
			<input type="hidden" name="employerid" value="<?php echo $this->uid; ?>" />
			
			<label for="title">
				<?php echo JText::_('COM_JOBS_EDITJOB_JOB_TITLE'); ?>: <span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span>
				<input name="title" maxlength="190" id="title" type="text" value="<?php echo $this->escape($job->title); ?>" />
			</label>
			
			<label for="companyLocation">
				<?php echo JText::_('COM_JOBS_EDITJOB_JOB_LOCATION'); ?>: <span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span>
				<input name="companyLocation" maxlength="190" id="companyLocation" type="text" value="<?php echo $this->escape(stripslashes($job->companyLocation)); ?>" />
			</label>
		<?php if (!$usonly && !empty($countries)) { ?>
			<label for="companyLocationCountry">
				<?php echo JText::_('COM_JOBS_EDITJOB_COUNTRY'); ?>: <span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span>
				<select name="companyLocationCountry" id="companyLocationCountry">
					<option value=""><?php echo JText::_('COM_JOBS_OPTION_SELECT_FROM_LIST'); ?></option>
				<?php 				
				foreach ($countries as $country) 
				{
					$selected = $job->companyLocationCountry ? $job->companyLocationCountry : 'United States';
					?>
					<option value="<?php echo $this->escape($country['name']); ?>"<?php if (strtoupper($country['name']) == strtoupper($selected)) { echo ' selected="selected"'; } ?>><?php echo $this->escape($country['name']); ?></option>
					<?php 
				}				
				?>
				</select>
			</label>
		<?php } else { ?>
			<p class="hint"><?php echo JText::_('COM_JOBS_EDITJOB_US_ONLY'); ?></p>
			<input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />
		<?php } ?>
			<label>
				<?php echo JText::_('COM_JOBS_EMPLOYER_COMPANY_NAME'); ?>: <span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span>
				<input name="companyName" maxlength="120" id="companyName" type="text" value="<?php echo $this->escape(stripslashes($job->companyName)); ?>" />
			</label>
			<label>
				<?php echo JText::_('COM_JOBS_EMPLOYER_COMPANY_WEBSITE'); ?>: 
				<input name="companyWebsite" maxlength="190" id="companyWebsite" type="text" value="<?php echo $this->escape(stripslashes($job->companyWebsite)); ?>" />
			</label>
			<p class="hint"><?php echo JText::_('COM_JOBS_EDITJOB_HINT_COMPANY'); ?></p>
		</fieldset>

		<div class="explaination">
			<p><?php echo JText::_('COM_JOBS_EDITJOB_DESC_INFO'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_JOBS_EDITJOB_JOB_DESCRIPTION'); ?> <span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span></legend>
			<label>
				&nbsp;
			<?php 
				ximport('Hubzero_Wiki_Editor');
				$editor =& Hubzero_Wiki_Editor::getInstance();
				echo $editor->display('description', 'description', $job->description, 'no-image-macro no-file-macro', '10', '25');
			?>
			</label>
		</fieldset>

		<div class="explaination">
			<p><?php echo JText::_('COM_JOBS_EDITJOB_SPECIFICS_INFO'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_JOBS_EDITJOB_JOB_SPECIFICS'); ?></legend>
			
			<label>
				<?php echo JText::_('COM_JOBS_EDITJOB_CATEGORY'); ?>: 
				<?php echo JobsHtml::formSelect('cid', $this->cats, $job->cid, '', ''); ?>
			</label>
			<label>
				<?php echo JText::_('COM_JOBS_EDITJOB_TYPE'); ?>: 
				<?php echo JobsHtml::formSelect('type', $this->types, $job->type, '', ''); ?>
			</label>
			<label for="startdate">
				<?php echo JText::_('COM_JOBS_EDITJOB_START_DATE'); ?>:
				<input type="text" class="option level" name="startdate" id="startdate" size="10" maxlength="10" value="<?php echo $startdate; ?>" /> <span class="hint"><?php echo JText::_('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?></span>
			</label>
			<label for="closedate">
				<?php echo JText::_('COM_JOBS_EDITJOB_CLOSE_DATE'); ?>:
				<input  type="text" class="option level" name="closedate" id="closedate" size="10" maxlength="10" value="<?php echo $closedate; ?>" /> <span class="hint"><?php echo JText::_('COM_JOBS_EDITJOB_HINT_DATE_FORMAT'); ?></span>
			</label>
			<label for="applyExternalUrl">
				<?php echo JText::_('COM_JOBS_EDITJOB_EXTERNAL_URL'); ?>:
				<input  type="text" name="applyExternalUrl" id="applyExternalUrl" size="100" maxlength="250" value="<?php echo $this->escape(stripslashes($job->applyExternalUrl)); ?>" />
			</label>
			<label for="applyInternal">
				<input type="checkbox" class="option" name="applyInternal" id="applyInternal" value="1"<?php echo $job->applyInternal ? ' checked="checked" ' : ''; ?> /> 
				<?php echo JText::_('COM_JOBS_EDITJOB_ALLOW_INTERNAL_APPLICATION'); ?>
			</label>
		</fieldset>
		
		<div class="explaination">
			<p><?php echo JText::_('COM_JOBS_EDITJOB_CONTACT_DETAILS'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_JOBS_EDITJOB_CONTACT_INFO'); ?> <span>(<?php echo JText::_('COM_JOBS_OPTIONAL'); ?>)</span></legend>
			
			<label for="contactName">
				<?php echo JText::_('COM_JOBS_EDITJOB_CONTACT_NAME'); ?>: 
				<input name="contactName" id="contactName" maxlength="100"  type="text" value="<?php echo $job->contactName ? $this->escape(stripslashes($job->contactName)) : $this->escape(stripslashes($profile->get('name'))); ?>" />
			</label>
			<label for="contactEmail">
				<?php echo JText::_('COM_JOBS_EDITJOB_CONTACT_EMAIL'); ?>:
				<input name="contactEmail" id="contactEmail" maxlength="100"  type="text" value="<?php echo $job->contactEmail ? $this->escape(stripslashes($job->contactEmail)) : $this->escape(stripslashes($profile->get('email'))); ?>" />
			</label>
			<label for="contactPhone">
				<?php echo JText::_('COM_JOBS_EDITJOB_CONTACT_PHONE'); ?>: 
				<input name="contactPhone" id="contactPhone" maxlength="100"  type="text" value="<?php echo $job->contactPhone ? $this->escape(stripslashes($job->contactPhone)) : $this->escape(stripslashes($profile->get('phone'))); ?>" />
			</label>
		</fieldset>
		<p class="submit">
			<input type="submit" name="submit" value="<?php echo ($this->task=='addjob' or $job->status == 4) ? JText::_('COM_JOBS_ACTION_SAVE_PREVIEW') : JText::_('COM_JOBS_ACTION_SAVE'); ?>" />
			<span class="cancelaction">
				<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_CANCEL'); ?></a>
			</span>
		</p>
	</form>
</div>
