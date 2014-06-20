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

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

$canDo = CoursesHelper::getActions();

JToolBarHelper::title(JText::_('COM_COURSES') .': ' . $text . ' ' . JText::_('COM_COURSES_STUDENT'), 'courses.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();

$profile = \Hubzero\User\Profile::getInstance($this->row->get('user_id'));

$js = '';

$role_id = 0;
$roles = $this->offering->roles();
foreach ($roles as $role)
{
	if ($role->alias == 'student')
	{
		$role_id = $role->id;
		break;
	}
}
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
	if ($('offering_id').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_OFFERING'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="offering" value="<?php echo $this->row->get('offering_id'); ?>" />
			<input type="hidden" name="section" value="<?php echo $this->row->get('section_id'); ?>" />
			<input type="hidden" name="fields[role_id]" value="<?php echo $this->row->get('role_id'); ?>" />
			<input type="hidden" name="fields[user_id]" value="<?php echo $this->row->get('user_id'); ?>" />

			<div class="input-wrap">
				<label for="field-offering_id"><?php echo JText::_('COM_COURSES_OFFERING'); ?>:</label><br />
				<select name="fields[offering_id]" id="field-offering_id" onchange="changeDynaList('section_id', offeringsections, document.getElementById('offering_id').options[document.getElementById('offering_id').selectedIndex].value, 0, 0);">
					<option value="-1"><?php echo JText::_('COM_COURSES_NONE'); ?></option>
					<?php
						require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
						$model = CoursesModelCourses::getInstance();
						if ($model->courses()->total() > 0)
						{
							foreach ($model->courses() as $course)
							{
							?>
							<optgroup label="<?php echo $this->escape(stripslashes($course->get('alias'))); ?>">
								<?php
								$j = 0;
								foreach ($course->offerings() as $i => $offering)
								{
									foreach ($offering->sections() as $section)
									{
										$js .= 'offeringsections[' . $j++ . "] = new Array( '" . $offering->get('id') . "','" . addslashes($section->get('id')) . "','" . addslashes($section->get('title')) . "' );\n\t\t";
									}
									?>
								<option value="<?php echo $this->escape(stripslashes($offering->get('id'))); ?>"<?php if ($offering->get('id') == $this->row->get('offering_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('alias'))); ?></option>
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
				<label for="field-section_id"><?php echo JText::_('COM_COURSES_SECTION'); ?>:</label><br />
				<select name="fields[section_id]" id="field-section_id">
					<option value="-1"><?php echo JText::_('COM_COURSES_SELECT'); ?></option>
					<?php
					foreach ($this->offering->sections() as $k => $section)
					{
					?>
					<option value="<?php echo $this->escape(stripslashes($section->get('id'))); ?>"<?php if ($section->get('id') == $this->row->get('section_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-enrolled"><?php echo JText::_('COM_COURSES_FIELD_ENROLLED'); ?></label><br />
				<?php echo JHTML::_('calendar', $this->row->get('enrolled'), 'fields[enrolled]', 'field-enrolled', "%Y-%m-%d", array('class' => 'inputbox')); ?>
			</div>
			<div class="input-wrap">
				<label for="field-token"><?php echo JText::_('COM_COURSES_FIELD_SERIAL_NUM'); ?></label><br />
				<input type="text" name="fields[token]" id="field-token" value="<?php echo $this->escape($this->row->get('token')); ?>" />
			</div>
		</fieldset>

		<?php /*<fieldset class="adminform">
			<legend><span><?php echo JText::_('Progress'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="paramlist_key"><label for="enrolled">Key:</label></th>
						<td>
							--
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>*/ ?>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_USER_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('user_id')); ?></td>
				</tr>
			<?php if ($profile) { ?>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_NAME'); ?></th>
					<td><?php echo $this->escape(stripslashes($profile->get('name'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_USERNAME'); ?></th>
					<td><?php echo $this->escape(stripslashes($profile->get('username'))); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_EMAIL'); ?></th>
					<td><?php echo $this->escape(stripslashes($profile->get('email'))); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<script type="text/javascript">
		var offeringsections = new Array;
		<?php echo $js; ?>
	</script>

	<?php echo JHTML::_('form.token'); ?>
</form>
