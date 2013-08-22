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

$canDo = CoursesHelper::getActions('course');

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . $text . ' ]</small></small>', 'courses.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

jimport('joomla.html.editor');

$editor =& JEditor::getInstance();

$document =& JFactory::getDocument();
$document->addStyleSheet('components' . DS . $this->option . DS . 'assets' . DS . 'css' . DS . 'classic.css');
/*$paramsClass = 'JParameter';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$paramsClass = 'JRegistry';
}
$gparams = new $paramsClass($this->offering->params);

$membership_control = $gparams->get('membership_control', 1);

$display_system_users = $gparams->get('display_system_users', 'global');*/
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
	if ($('field-alias').value == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_INFORMATION'); ?>');
	} else if ($('field-title').value == '') {
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
			
			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="section_id"><?php echo JText::_('Section'); ?>:</label></th>
						<td>
							<select name="fields[section_id]" id="section_id">
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
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-code"><?php echo JText::_('Code'); ?>:</label></th>
						<td><input type="text" name="fields[code]" id="field-code" value="<?php echo $this->escape(stripslashes($this->row->get('code'))); ?>" size="50" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Availability'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field-created">Starts:</label></th>
						<td>
							<?php //echo JHTML::_('calendar', $this->row->get('expires'), 'fields[created]', 'created', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
							<input type="text" name="fields[created]" id="field-created" class="datetime-field" value="<?php echo $this->escape(stripslashes($this->row->get('created'))); ?>" />
							<span class="hint">When the section will become available for enrollment</span>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-expires">Expires:</label></th>
						<td>
							<?php //echo JHTML::_('calendar', $this->row->get('expires'), 'fields[expires]', 'expires', "%Y-%m-%d", array('class' => 'calendar-field inputbox')); ?>
							<input type="text" name="fields[expires]" id="field-expires" class="datetime-field" value="<?php echo $this->escape(stripslashes($this->row->get('expires'))); ?>" />
							<span class="hint">When section will close (materials no longer accessible)</span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Redeemed'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
				<?php if ($this->row->get('redeemed_by')) { ?>
					<tr>
						<th class="key"><label for="field-redeemed">Redeemed:</label></th>
						<td>
							<?php echo $this->escape(stripslashes($this->row->get('redeemed'))); ?>
							<input type="hidden" name="fields[redeemed]" id="field-redeemed" class="datetime-field" value="<?php echo $this->escape(stripslashes($this->row->get('redeemed'))); ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-redeemed_by">Redeemed by:</label></th>
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
		<table class="meta" summary="<?php echo JText::_('COM_COURSES_META_SUMMARY'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('ID'); ?></th>
					<td colspan="3"><?php echo $this->escape($this->row->get('id')); ?></td>
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
			<img src="<?php echo 'index.php?option=com_courses&controller=codes&task=qrcode&no_html=1&code=' . $this->row->get('code'); ?>" alt="QR Code" />
		</fieldset>
	</div>
	<div class="clr"></div>

	<script src="/media/system/js/jquery.js"></script>
	<script src="/media/system/js/jquery.ui.js"></script>
	<script src="/media/system/js/jquery.noconflict.js"></script>
	<script src="components/com_courses/assets/js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function(jq){
			var $ = jq;
			$('.datetime-field').datetimepicker({  
				duration: '',
				showTime: true,
				constrainInput: false,
				stepMinutes: 1,
				stepHours: 1,
				altTimeField: '',
				time24h: true,
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:00'
			});
		});
	</script>

	<?php echo JHTML::_('form.token'); ?>
</form>
