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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$dateFormat = '$Y-%m-%d';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'Y-M-d';
	$tz = false;
}

$canDo = JobsHelper::getActions('job');

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Job').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

$now = date("Y-m-d H:i:s");

$usonly = $this->config->get('usonly');
$this->row->companyLocationCountry = !$this->isnew ? $this->row->companyLocationCountry : htmlentities(JText::_('United States'));
$this->row->code = !$this->isnew ? $this->row->code : JText::_('N/A (new job)');

$startdate = ($this->row->startdate && $this->row->startdate !='0000-00-00 00:00:00') ? JHTML::_('date',$this->row->startdate, $dateFormat, $tz) : '';
$closedate = ($this->row->closedate && $this->row->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$this->row->closedate, $dateFormat, $tz) : '';
$opendate = ($this->row->opendate && $this->row->opendate !='0000-00-00 00:00:00') ? JHTML::_('date',$this->row->opendate, $dateFormat, $tz) : '';

$status = (!$this->isnew) ? $this->row->status : 4; // draft mode

$this->row->description = trim(stripslashes($this->row->description));
$this->row->description = preg_replace('/<br\\s*?\/??>/i', "", $this->row->description);
$this->row->description = JobsHtml::txt_unpee($this->row->description);
$employerid = ($this->task != 'edit') ? 1 : $this->job->employerid;

$expired = $this->subscription->expires && $this->subscription->expires < $now ? 1 : 0;

// Get the published status			
	switch ($this->row->status)
	{
		case 0:
			$alt   = 'Pending approval';
			$class = 'post_pending';
			break;
		case 1:
			$alt 	= $expired
					? JText::_('Expired/Invalid Subscription')
					: JText::_('Active');
			$class  = $expired
					? 'post_invalidsub'
					: 'post_active';
			break;
		case 2:
			$alt   = 'Deleted';
			$class = 'post_deleted';
			break;
		case 3:
			$alt   = 'Inactive';
			$class = 'post_inactive';
			break;
		case 4:
			$alt   = 'Draft';
			$class = 'post_draft';
			break;
		default:
			$alt   = '-';
			$class = '';
			break;
	}
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('jobForm');

	if (pressbutton == 'cancel') {
		form.task.value = 'cancel';
		form.submit();
		return;
	}
	
	// do field validation
	if (form.title.value == ''){
		alert('Job must have a title.');
	} else if (form.description.value == ''){
		alert('Job must have a description.');
	} else if (form.companyLocation.value == ''){
		alert('Job must have a location.');
	} else if (form.companyName.value == ''){
		alert('Job must have a company name.');
	} else {
		form.task.value = 'save';
		form.submit();
		return;
	}
}
</script>

<form action="index.php" method="post" id="jobForm" name="jobForm">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::sprintf('Job #%s', $this->row->code); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="title"><?php echo JText::_('Title'); ?>:</label></th>
						<td><input type="text" name="title" id="title" size="60" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="companyName"><?php echo JText::_('Company Name'); ?>:</label></th>
						<td><input type="text" name="companyName" id="companyName" size="60" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->companyName)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="companyWebsite"><?php echo JText::_('Company URL'); ?>:</label></th>
						<td><input type="text" name="companyWebsite" id="companyWebsite" size="60" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->companyWebsite)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="companyLocation"><?php echo JText::_('Company Location'); ?> <br />(<?php echo JText::_('City, State'); ?>):</label></th>
						<td><input type="text" name="companyLocation" id="companyLocation" size="60" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->companyLocation)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="companyLocationCountry"><?php echo JText::_('Country'); ?>:</label></th>
						<td>
						<?php if ($usonly) { ?>
							<?php echo JText::_('United States'); ?>
							<p class="hint"><?php echo JText::_('Only US-based jobs can be advertised on this site.'); ?></p>
							<input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />
						<?php } else {
							$out  = "\t\t\t\t".'<select name="companyLocationCountry" id="companyLocationCountry">'."\n";
							$out .= "\t\t\t\t".' <option value="">(select from list)</option>'."\n";
							//$countries = getcountries();
							ximport('Hubzero_Geo');
							$countries = Hubzero_Geo::getcountries();
							foreach ($countries as $country)
							{
								$out .= "\t\t\t\t".' <option value="' . htmlentities($country['name']) . '"';
								if ($country['name'] == $this->row->companyLocationCountry) {
									$out .= ' selected="selected"';
								}
								$out .= '>' . $this->escape($country['name']) . '</option>'."\n";
							}
							$out .= "\t\t\t".'</select>'."\n";
						 	echo $out;
						 ?>
						 <?php } ?>
						</td>
					</tr>
					 <tr>
						<th class="key"><label for="description"><?php echo JText::_('Job Description'); ?>:</label></th>
						<td>
							<p class="hint"><?php echo JText::_('Wiki formatting is enabled.'); ?></p>
							<textarea name="description" id="description"  cols="55" rows="30"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Job Specifics'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="cid"><?php echo JText::_('Job Category'); ?>:</label></th>
						<td><?php echo JobsHtml::formSelect('cid', $this->cats, $this->row->cid, '', ''); ?></td>
					</tr>
					<tr>
						<th class="key"><label for="type"><?php echo JText::_('Job Type'); ?>:</label></th>
						<td><?php echo JobsHtml::formSelect('type', $this->types, $this->row->type, '', ''); ?></td>
					</tr>
					<tr>
						<th class="key"><label for="startdate"><?php echo JText::_('Position Start Date'); ?>:</label></th>
						<td>
							<p class="hint"><?php echo JText::_('Date format: yyyy-mm-dd.'); ?></p>
							<input type="text" name="startdate" id="startdate" size="60" maxlength="10" value="<?php echo $startdate; ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><label for="closedate"><?php echo JText::_('Applications Due'); ?>:</label></th>
						<td>
							<p class="hint"><?php echo JText::_('Date format: yyyy-mm-dd.'); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('- Will default to \'ASAP\' when left blank'); ?></p>
							<input type="text" name="closedate" id="closedate" size="60" maxlength="10" value="<?php echo $closedate; ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><label for="applyExternalUrl"><?php echo JText::_('External URL <br />for a job application <br />(optional)'); ?>:</label></th>
						<td>
							<p class="hint"><?php echo JText::_('Include http://'); ?></p>
							<input type="text" name="applyExternalUrl" size="60" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->applyExternalUrl)); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><label><?php echo JText::_('Allow internal application'); ?>:</label></th>
						<td><input type="checkbox" class="option" name="applyInternal"  size="10" maxlength="10" value="1" <?php if ($this->row->applyInternal) { echo 'checked="checked"'; } ?>  /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Contact Information') . ' ' . JText::_('(optional)'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="contactName"><?php echo JText::_('Contact Name'); ?>:</label></th>
						<td><input type="text" name="contactName" id="contactName" size="60" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->contactName)); ?>" /></td>
					</tr>
					 <tr>
						<th class="key"><label for="contactEmail"><?php echo JText::_('Contact Email'); ?>:</label></th>
						<td><input type="text" name="contactEmail" id="contactEmail" size="60" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->contactEmail)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="contactPhone"><?php echo JText::_('Contact Phone'); ?>:</label></th>
						<td><input type="text" name="contactPhone" id="contactPhone" size="60" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->contactPhone)); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
<?php if ($this->row->id) { ?>
		<table class="meta" summary="<?php echo JText::_('Metadata for this item'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('Added'); ?>:</th>
					<td><?php echo $this->row->added; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Added by'); ?>:</th>
					<td><?php echo $this->row->addedBy; if ($this->job->employerid == 1) { echo ' ' . JText::_('(admin subscription)'); } ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Last changed'); ?>:</th>
					<td>
						<?php echo ($this->job->edited && $this->job->edited !='0000-00-00 00:00:00') ? $this->job->edited : 'N/A'; ?>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('Last changed by'); ?>:</th>
					<td>
						<?php echo ($this->job->editedBy) ? $this->job->editedBy : 'N/A'; ?>
					</td>
				</tr>
			<?php if (isset($this->subscription->id)) { ?>
				<tr>
					<th><?php echo JText::_('User subscription'); ?>:</th>
					<td>
						<?php echo $this->subscription->code;
						if (!$this->job->inactive) { echo ' ' . JText::_('(active') . ' ' . JText::_(', expires') . ' ' . $this->subscription->expires . ')';  } ?>
					</td>
				</tr>
			<?php } ?>
				<tr>
					<th><?php echo JText::_('Job Ad Status'); ?>:</th>
					<td><?php echo $alt; ?></td>
				</tr>
			<?php if ($opendate) { ?>
				<tr>
					<th><?php echo JText::_('Job Ad Published'); ?>:</th>
					<td><?php echo $this->row->opendate; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
<?php } ?>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Manage this Job'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
				<?php if (!$this->isnew) { ?>
					<tr>
						<td class="key"><label><?php echo JText::_('Change Status / Take Action'); ?>:</label></td>
						<td><input type="radio" name="action" value="message" /><?php echo JText::_('No action / Send message to author'); ?></td>
					</tr>
					<tr>
						<th></th>
						<td>
							<?php if ($this->row->status != 1) { ?>
							<input type="radio" name="action" value="publish" /> <?php echo JText::_('Publish Ad'); ?>
							<?php } else { ?>
							<input type="radio" name="action" value="unpublish" /> <?php echo JText::_('Unpublish Ad'); ?>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th></th>
						<td><input type="radio" name="action" value="delete" /> <?php echo JText::_('Delete Ad'); ?></td>
					</tr>
					<tr>
						<th></th>
						<td><?php echo JText::_('Message to author'); ?>: <br /><textarea name="message" id="message"  cols="30" rows="5"></textarea></td>
					</tr>
				<?php } else { ?>
					<tr>
						<td><?php echo JText::_('This is a new job ad. Please save it as draft before admin option become available.'); ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="isnew" value="<?php echo $this->isnew; ?>" />
			<input type="hidden" name="employerid" value="<?php echo $employerid; ?>" />
			<input type="hidden" name="status" value="<?php echo $status; ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<?php echo JHTML::_('form.token'); ?>
</form>
