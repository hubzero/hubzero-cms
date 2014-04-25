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
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? JText::_('EDIT') : JText::_('NEW'));

$canDo = CoursesHelper::getActions();

JToolBarHelper::title(JText::_('COM_COURSES').': ' . JText::_('Coupon Codes') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

JHTML::_('behavior.calendar');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if ($('#field-alias').val() == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else if ($('#field-title').val() == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_COURSES_DETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
			<input type="hidden" name="section" value="<?php echo $this->row->get('section_id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />

			<div class="input-wrap">
				<label for="field-section_id"><?php echo JText::_('Section'); ?>: <span class="required"><?php echo JText::_('required'); ?></span></label><br />
				<select name="fields[section_id]" id="field-section_id">
					<option value="-1"><?php echo JText::_('Select section...'); ?></option>
					<?php
						require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
						$model = CoursesModelCourses::getInstance();
						if ($model->courses()->total() > 0)
						{
							foreach ($model->courses() as $course)
							{
					?>
								<optgroup label="<?php echo $this->escape(stripslashes($course->get('title'))); ?>">
					<?php
								$j = 0;
								foreach ($course->offerings() as $i => $offering)
								{
					?>
									<optgroup label="&nbsp; &nbsp; <?php echo $this->escape(stripslashes($offering->get('title'))); ?>">
						<?php 
									foreach ($offering->sections() as $section)
									{
						?>
										<option value="<?php echo $this->escape(stripslashes($section->get('id'))); ?>"<?php if ($section->get('id') == $this->row->get('section_id')) { echo ' selected="selected"'; } ?>>&nbsp; &nbsp; <?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
						<?php
									}
						?>
									</optgroup>
					<?php
								}
					?>
								</optgroup>
					<?php 
							}
						}
					?>
							</select>
			</div>
			<div class="input-wrap">
				<label for="field-code"><?php echo JText::_('Code'); ?>: <span class="required"><?php echo JText::_('required'); ?></span></label><br />
				<input type="text" name="fields[code]" id="field-code" value="<?php echo $this->escape(stripslashes($this->row->get('code'))); ?>" />
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Availability'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo JText::_('When the section will become available for enrollment'); ?>">
				<label for="field-created">Starts:</label><br />
				<?php echo JHTML::_('calendar', $this->row->get('created'), 'fields[created]', 'field-created'); ?>
				<span class="hint">When the section will become available for enrollment</span>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('When section will close (materials no longer accessible)'); ?>">
				<label for="field-expires">Expires:</label><br />
				<?php echo JHTML::_('calendar', $this->row->get('expires'), 'fields[expires]', 'field-expires'); ?>
				<span class="hint">When section will close (materials no longer accessible)</span>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Redeemed'); ?></span></legend>

			<table class="admintable">
				<tbody>
				<?php if ($this->row->get('redeemed_by')) { ?>
					<tr>
						<th><label for="field-redeemed">Redeemed:</label></th>
						<td>
							<?php echo $this->escape(stripslashes($this->row->get('redeemed'))); ?>
							<input type="hidden" name="fields[redeemed]" id="field-redeemed" class="datetime-field" value="<?php echo $this->escape(stripslashes($this->row->get('redeemed'))); ?>" />
						</td>
					</tr>
					<tr>
						<th><label for="field-redeemed_by">Redeemed by:</label></th>
						<td>
							<?php echo $this->escape(stripslashes($this->row->redeemer()->get('name'))) . ' (' . $this->escape(stripslashes($this->row->redeemer()->get('username'))) . ')'; ?>
							<input type="hidden" name="fields[redeemed_by]" id="field-redeemed_by" value="<?php echo $this->escape(stripslashes($this->row->get('redeemed_by'))); ?>" />
						</td>
					</tr>
				<?php } else { ?>
					<tr>
						<td>
							<?php echo JText::_('Code has not been redeemed.') ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
		<?php if ($this->row->get('created')) { ?>
				<tr>
					<th><?php echo JText::_('Created'); ?></th>
					<td>
						<?php echo $this->escape($this->row->get('created')); ?>
					</td>
				</tr>
			<?php if ($this->row->get('created_by')) { ?>
				<tr>
					<th><?php echo JText::_('Creator'); ?></th>
					<td><?php 
					$creator = JUser::getInstance($this->row->get('created_by'));
					echo $this->escape(stripslashes($creator->get('name'))); ?></td>
				</tr>
			<?php } ?>
		<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('QR Code'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
				<img src="<?php echo 'index.php?option=com_courses&controller=codes&task=qrcode&no_html=1&code=' . $this->row->get('code'); ?>" alt="QR Code" />
			<?php } else { ?>
				<p class="warning"><?php echo JText::_('New entries must be saved before QR code can be generated.'); ?></p>
			<?php } ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
