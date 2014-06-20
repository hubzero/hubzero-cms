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

JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_CERTIFICATE') . ': ' . $text, 'courses.png');
JToolBarHelper::cancel();

JHTML::_('behavior.framework');
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
	if ($('#upload').val() == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_FILE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_COURSES_UPLOAD'); ?></span></legend>

			<div class="input-wrap">
				<input type="file" name="upload" id="upload" size="17" />
			</div>
			<div class="input-wrap">
				<input type="submit" value="<?php echo JText::_('COM_COURSES_UPLOAD'); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<p><?php echo JText::_('COM_COURSES_CERTIFICATE_HELP'); ?></p>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="certificate" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="course" value="<?php echo $this->row->get('course_id'); ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="upload" />

	<?php echo JHTML::_('form.token'); ?>
</form>
