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

$canDo = JobsHelper::getActions('job');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_JOBS') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('job');

$now = JFactory::getDate()->toSql();

$usonly = $this->config->get('usonly');
$this->row->companyLocationCountry = !$this->isnew ? $this->row->companyLocationCountry : JText::_('COM_JOBS_USA');
$this->row->code = !$this->isnew ? $this->row->code : JText::_('COM_JOBS_ISNEW');

$startdate = ($this->row->startdate && $this->row->startdate !='0000-00-00 00:00:00') ? JHTML::_('date', $this->row->startdate, 'Y-M-d') : '';
$closedate = ($this->row->closedate && $this->row->closedate !='0000-00-00 00:00:00') ? JHTML::_('date', $this->row->closedate, 'Y-M-d') : '';
$opendate  = ($this->row->opendate  && $this->row->opendate  !='0000-00-00 00:00:00')  ? JHTML::_('date', $this->row->opendate, 'Y-M-d')  : '';

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
			$alt   = JText::_('COM_JOBS_STATUS_PENDING');
			$class = 'post_pending';
			break;
		case 1:
			$alt 	= $expired
					? JText::_('COM_JOBS_STATUS_EXPIRED')
					: JText::_('COM_JOBS_STATUS_ACTIVE');
			$class  = $expired
					? 'post_invalidsub'
					: 'post_active';
			break;
		case 2:
			$alt   = JText::_('COM_JOBS_STATUS_DELETED');
			$class = 'post_deleted';
			break;
		case 3:
			$alt   = JText::_('COM_JOBS_STATUS_INACTIVE');
			$class = 'post_inactive';
			break;
		case 4:
			$alt   = JText::_('COM_JOBS_STATUS_DRAFT');
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
	var form = document.getElementById('item-form');

	if (pressbutton == 'cancel') {
		form.task.value = 'cancel';
		form.submit();
		return;
	}

	// do field validation
	if (form.title.value == ''){
		alert('<?php echo JText::_('COM_JOBS_ERROR_MISSING_TITLE'); ?>');
	} else if (form.description.value == ''){
		alert('<?php echo JText::_('COM_JOBS_ERROR_MISSING_DESCRIPTION'); ?>');
	} else if (form.companyLocation.value == ''){
		alert('<?php echo JText::_('COM_JOBS_ERROR_MISSING_LOCATION'); ?>');
	} else if (form.companyName.value == ''){
		alert('<?php echo JText::_('COM_JOBS_ERROR_MISSING_COMPANY'); ?>');
	} else {
		form.task.value = 'save';
		form.submit();
		return;
	}
}
</script>

<form action="index.php" method="post" id="item-form" name="adminForm">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_JOBS_FIELDSET_COMPANY'); ?></span></legend>

			<div class="input-wrap">
				<label for="companyName"><?php echo JText::_('COM_JOBS_FIELD_NAME'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="companyName" id="companyName" size="60" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->companyName)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="companyWebsite"><?php echo JText::_('COM_JOBS_FIELD_URL'); ?>:</label><br />
				<input type="text" name="companyWebsite" id="companyWebsite" size="60" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->companyWebsite)); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_JOBS_FIELD_LOCATION_HINT'); ?>">
				<label for="companyLocation"><?php echo JText::_('COM_JOBS_FIELD_LOCATION'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="companyLocation" id="companyLocation" size="60" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->companyLocation)); ?>" />
				<span class="hint"><?php echo JText::_('COM_JOBS_FIELD_LOCATION_HINT'); ?></span>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_JOBS_FIELDSET_JOB'); ?></span></legend>

			<div class="input-wrap">
				<label for="cid"><?php echo JText::_('COM_JOBS_FIELD_CATEGORY'); ?>:</label><br />
				<?php echo JobsHtml::formSelect('cid', $this->cats, $this->row->cid, '', ''); ?>
			</div>
			<div class="input-wrap">
				<label for="type"><?php echo JText::_('COM_JOBS_FIELD_TYPE'); ?>:</label><br />
				<?php echo JobsHtml::formSelect('type', $this->types, $this->row->type, '', ''); ?>
			</div>
			<div class="input-wrap">
				<label for="companyLocationCountry"><?php echo JText::_('COM_JOBS_FIELD_COUNTRY'); ?>:</label><br />
				<?php if ($usonly) { ?>
					<?php echo JText::_('COM_JOBS_USA'); ?>
					<p class="hint"><?php echo JText::_('COM_JOBS_USA_HINT'); ?></p>
					<input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />
				<?php } else {
					$out  = "\t\t\t\t".'<select name="companyLocationCountry" id="companyLocationCountry">'."\n";
					$out .= "\t\t\t\t".' <option value="">' . JText::_('COM_JOBS_SELECT') . '</option>'."\n";
					//$countries = getcountries();

					$countries = \Hubzero\Geocode\Geocode::countries();
					foreach ($countries as $country)
					{
						$out .= "\t\t\t\t".' <option value="' . $this->escape($country->name) . '"';
						if ($country->name == $this->row->companyLocationCountry)
						{
							$out .= ' selected="selected"';
						}
						$out .= '>' . $this->escape($country->name) . '</option>'."\n";
					}
					$out .= "\t\t\t".'</select>'."\n";
					echo $out;
				?>
				<?php } ?>
			</div>
			<div class="input-wrap">
				<label for="title"><?php echo JText::_('COM_JOBS_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="title" id="title" maxlength="200" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_JOBS_FIELD_DESCRIPTION_HINT'); ?>">
				<label for="description"><?php echo JText::_('COM_JOBS_FIELD_DESCRIPTION'); ?>:</label><br />
				<textarea name="description" id="description"  cols="55" rows="30"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
				<span class="hint"><?php echo JText::_('COM_JOBS_FIELD_DESCRIPTION_HINT'); ?></span>
			</div>
			<div class="input-wrap">
				<label for="startdate"><?php echo JText::_('COM_JOBS_FIELD_STARTDATE'); ?>:</label><br />
				<?php echo JHTML::_('calendar', $startdate, 'startdate', 'startdate'); ?>
			</div>
			<div class="input-wrap">
				<label for="closedate"><?php echo JText::_('COM_JOBS_FIELD_DUEDATE'); ?>:</label><br />
				<?php echo JHTML::_('calendar', $closedate, 'closedate', 'closedate'); ?>
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_JOBS_FIELD_EXTERNAL_URL_HINT'); ?>">
				<label for="applyExternalUrl"><?php echo JText::_('COM_JOBS_FIELD_EXTERNAL_URL'); ?>:</label><br />
				<input type="text" name="applyExternalUrl" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->applyExternalUrl)); ?>" />
				<span class="hint"><?php echo JText::_('COM_JOBS_FIELD_EXTERNAL_URL_HINT'); ?></span>
			</div>
			<div class="input-wrap">
				<input type="checkbox" class="option" name="applyInternal" value="1" <?php if ($this->row->applyInternal) { echo 'checked="checked"'; } ?>  />
				<label><?php echo JText::_('COM_JOBS_FIELD_APPLY_INTERNAL'); ?></label>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_JOBS_FIELDSET_CONTACT_INFO'); ?></span></legend>

			<div class="input-wrap">
				<label for="contactName"><?php echo JText::_('COM_JOBS_FIELD_CONTACT_NAME'); ?>:</label>
				<input type="text" name="contactName" id="contactName" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->contactName)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="contactEmail"><?php echo JText::_('COM_JOBS_FIELD_CONTACT_EMAIL'); ?>:</label></th>
				<input type="text" name="contactEmail" id="contactEmail" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->contactEmail)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="contactPhone"><?php echo JText::_('COM_JOBS_FIELD_CONTACT_PHONE'); ?>:</label></th>
				<input type="text" name="contactPhone" id="contactPhone" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->contactPhone)); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
<?php if ($this->row->id) { ?>
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_JOBS_FIELD_CREATED'); ?>:</th>
					<td><?php echo $this->row->added; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_JOBS_FIELD_CREATOR'); ?>:</th>
					<td><?php echo $this->row->addedBy; if ($this->job->employerid == 1) { echo ' ' . JText::_('COM_JOBS_ADMIN'); } ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_JOBS_FIELD_MODIFIED'); ?>:</th>
					<td>
						<?php echo ($this->job->edited && $this->job->edited !='0000-00-00 00:00:00') ? $this->job->edited : JText::_('COM_JOBS_NOT_APPLICABLE'); ?>
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_JOBS_FIELD_MODIFIER'); ?>:</th>
					<td>
						<?php echo ($this->job->editedBy) ? $this->job->editedBy : JText::_('COM_JOBS_NOT_APPLICABLE'); ?>
					</td>
				</tr>
			<?php if (isset($this->subscription->id)) { ?>
				<tr>
					<th><?php echo JText::_('COM_JOBS_FIELD_USER_SUBSCRIPTION'); ?>:</th>
					<td>
						<?php echo $this->subscription->code;
						if (!$this->job->inactive) { echo ' ' . JText::sprintf('COM_JOBS_FIELD_USER_SUBSCRIPTION_EXPIRES', $this->subscription->expires); } ?>
					</td>
				</tr>
			<?php } ?>
				<tr>
					<th><?php echo JText::_('COM_JOBS_FIELD_STATUS'); ?>:</th>
					<td><?php echo $alt; ?></td>
				</tr>
			<?php if ($opendate) { ?>
				<tr>
					<th><?php echo JText::_('COM_JOBS_FIELD_AD_PUBLISHED'); ?>:</th>
					<td><?php echo $this->row->opendate; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
<?php } ?>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_JOBS_FIELDSET_MANAGE'); ?></span></legend>

			<?php if (!$this->isnew) { ?>
				<fieldset>
					<legend><span><?php echo JText::_('COM_JOBS_FIELDSET_TAKE_ACTION'); ?>:</span></legend>

					<div class="input-wrap">
						<input type="radio" name="action" value="message" /><?php echo JText::_('COM_JOBS_FIELD_ACTION_NONE'); ?><br />
						<?php if ($this->row->status != 1) { ?>
							<input type="radio" name="action" value="publish" /> <?php echo JText::_('COM_JOBS_FIELD_ACTION_PUBLISH'); ?>
						<?php } else { ?>
							<input type="radio" name="action" value="unpublish" /> <?php echo JText::_('COM_JOBS_FIELD_ACTION_UNPUBLISH'); ?>
						<?php } ?>
						<br />
						<input type="radio" name="action" value="delete" /> <?php echo JText::_('COM_JOBS_FIELD_ACTION_DELETE'); ?><br />
					</div>
				</fieldset>

				<div class="input-wrap">
					<?php echo JText::_('COM_JOBS_FIELD_MESSAGE'); ?>: <br />
					<textarea name="message" id="message"  cols="30" rows="5"></textarea>
				</div>
			<?php } else { ?>
				<p><?php echo JText::_('COM_JOBS_WARNING_MUST_SAVE_FIRST'); ?></p>
			<?php } ?>

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
