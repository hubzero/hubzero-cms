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

JToolBarHelper::title(JText::_('COM_COURSES').': ' . $text . ' ' . JText::_('COM_COURSES_STUDENTS'), 'courses.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();

$base = str_replace('/administrator', '', rtrim(JURI::getInstance()->base(true), '/'));

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
	if (document.getElementById('acmembers').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_USER_INFO'); ?>');
		return false;
	} else if (document.getElementById('offering_id').value == '-1') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_OFFERING'); ?>');
		return false;
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
			<input type="hidden" name="offering" value="<?php echo $this->offering->get('id'); ?>" />
			<input type="hidden" name="fields[role_id]" value="<?php echo $this->row->get('role_id', $role_id); ?>" />
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="fields[student]" value="1" />

			<div class="input-wrap">
				<label for="acmembers"><?php echo JText::_('COM_COURSES_FIELD_USER'); ?></label><br />
				<input type="text" name="fields[user_id]" data-options="members,multi," id="acmembers" class="autocomplete" value="" autocomplete="off" data-css="" data-script="<?php echo $base; ?>/administrator/index.php" />
				<span class="hint"><?php echo JText::_('COM_COURSES_FIELD_USER_HINT'); ?></span>

				<script type="text/javascript" src="<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.js"></script>
				<script type="text/javascript">var plgAutocompleterCss = "<?php echo $base; ?>/plugins/hubzero/autocompleter/autocompleter.css";</script>
				<?php
				/*JPluginHelper::importPlugin('hubzero');
				$dispatcher = JDispatcher::getInstance();

				$mc = $dispatcher->trigger('onGetMultiEntry', array(
					array(
						'members',   // The component to call
						'fields[user_id]',        // Name of the input field
						'acmembers', // ID of the input field
						'',          // CSS class(es) for the input field
						'' // The value of the input field
					)
				));
				if (count($mc) > 0) {
					echo $mc[0] . '<span class="hint">' . JText::_('COM_COURSES_FIELD_USER_HINT') . '</span>';
				} else { ?>
				<input type="text" name="fields[user_id]" id="acmembers" value="" size="35" />
				<span class="hint"><?php echo JText::_('COM_COURSES_FIELD_USER_HINT'); ?></span>
				<?php }*/ ?>
			</div>
			<div class="input-wrap">
				<label for="offering_id"><?php echo JText::_('COM_COURSES_OFFERING'); ?>:</label><br />
					<select name="fields[offering_id]" id="offering_id" onchange="changeDynaList('section_id', offeringsections, document.getElementById('offering_id').options[document.getElementById('offering_id').selectedIndex].value, 0, 0);">
						<option value="-1"><?php echo JText::_('COM_COURSES_NONE'); ?></option>
			<?php
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
				$model = CoursesModelCourses::getInstance();
				if ($model->courses()->total() > 0)
				{
					$j = 0;
					foreach ($model->courses() as $course)
					{
			?>
						<optgroup label="<?php echo $this->escape(stripslashes($course->get('alias'))); ?>">
			<?php
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
				<label for="section_id"><?php echo JText::_('COM_COURSES_SECTION'); ?></label><br />
				<select name="fields[section_id]" id="section_id">
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
				<label for="enrolled"><?php echo JText::_('COM_COURSES_FIELD_ENROLLED'); ?></label><br />
				<?php echo JHTML::_('calendar', $this->row->get('enrolled'), 'fields[enrolled]', 'enrolled', 'Y-m-d H:i:s', array('class' => 'inputbox')); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>
	<script type="text/javascript">
	var offeringsections = new Array;
	<?php echo $js; ?>

	jQuery(document).ready(function($){
		if (jQuery.uniform) {
			$('#offering_id').on('change', function(e){
				$.uniform.update();
			});
		}
	});
	</script>

	<?php echo JHTML::_('form.token'); ?>
</form>
