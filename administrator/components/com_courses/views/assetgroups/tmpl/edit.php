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

JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_ASSET_GROUPS') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

JHTML::_('behavior.modal');

jimport('joomla.html.editor');
$editor = JEditor::getInstance();
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
	if ($('#field-title').val() == '') {
		alert('<?php echo JText::_('COM_COURSES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
jQuery(document).ready(function($){
	document.assetform = $.fancybox;
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
			<input type="hidden" name="fields[unit_id]" value="<?php echo $this->row->get('unit_id'); ?>" />
			<input type="hidden" name="unit" value="<?php echo $this->row->get('unit_id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_COURSES_FIELD_PARENT_HINT'); ?>">
				<label for="field-parent"><?php echo JText::_('COM_COURSES_FIELD_PARENT'); ?>:</label><br />
				<select name="fields[parent]" id="field-parent">
					<option value="0"<?php if (0 == $this->row->get('parent')) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_NONE'); ?></option>
					<?php foreach ($this->assetgroups as $assetgroup) { ?>
						<option value="<?php echo $assetgroup->get('id'); ?>"<?php if ($assetgroup->get('id') == $this->row->get('parent')) { echo ' selected="selected"'; } ?>><?php echo $assetgroup->treename . $this->escape(stripslashes($assetgroup->get('title'))); ?></option>
					<?php } ?>
				</select>
				<span class="hint"><?php echo JText::_('COM_COURSES_FIELD_PARENT_HINT'); ?></span>
			</div>
			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_COURSES_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				<span class="hint"><?php echo JText::_('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
			</div>
			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_COURSES_FIELD_DESCRIPTION'); ?>:</label><br />
				<textarea name="fields[description]" id="field-description" cols="40" rows="5"><?php echo $this->escape(stripslashes($this->row->get('description'))); ?></textarea>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_COURSES_FIELDSET_ASSETS'); ?></span></legend>
			<?php if ($this->row->get('id')) { ?>
				<iframe width="100%" height="400" name="assets" id="assets" frameborder="0" src="index.php?option=<?php echo $this->option; ?>&amp;controller=assets&amp;tmpl=component&amp;scope=asset_group&amp;scope_id=<?php echo $this->row->get('id'); ?>&amp;course_id=<?php echo $this->offering->get('course_id'); ?>"></iframe>
			<?php } else { ?>
				<p><?php echo JText::_('COM_COURSES_ENTRY_MUST_BE_SAVED_BEFORE_ASSETS'); ?></p>
			<?php } ?>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_UNIT_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('unit_id')); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_ID'); ?></th>
					<td><?php echo $this->escape($this->row->get('id')); ?></td>
				</tr>
			<?php if ($this->row->get('created')) { ?>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_CREATED'); ?></th>
					<td><?php echo $this->escape($this->row->get('created')); ?></td>
				</tr>
			<?php } ?>
			<?php if ($this->row->get('created_by')) { ?>
				<tr>
					<th><?php echo JText::_('COM_COURSES_FIELD_CREATOR'); ?></th>
					<td><?php
					$creator = JUser::getInstance($this->row->get('created_by'));
					echo $this->escape(stripslashes($creator->get('name'))); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_COURSES_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_COURSES_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_UNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_PUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_TRASHED'); ?></option>
				</select>
			</div>
		</fieldset>

		<?php
			JPluginHelper::importPlugin('courses');
			$dispatcher = JDispatcher::getInstance();

			if ($plugins = $dispatcher->trigger('onAssetgroupEdit'))
			{
				$data = $this->row->get('params');

				foreach ($plugins as $plugin)
				{
					$p = JPluginHelper::getPlugin('courses', $plugin['name']);
					$default = new JRegistry($p->params);

					$param = new JParameter(
						(is_object($data) ? $data->toString() : $data),
						JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . $plugin['name'] . DS . $plugin['name'] . '.xml'
					);
					foreach ($default->toArray() as $k => $v)
					{
						if (substr($k, 0, strlen('default_')) == 'default_')
						{
							$param->def(substr($k, strlen('default_')), $default->get($k, $v));
						}
					}
					$out = $param->render('params', 'onAssetgroupEdit');

					if (!$out)
					{
						continue;
					}
					?>
					<fieldset class="adminform eventparams" id="params-<?php echo $plugin['name']; ?>">
						<legend><span><?php echo JText::sprintf('COM_COURSES_FIELDSET_PARAMETERS', $plugin['title']); ?></span></legend>
						<div class="input-wrap">
							<?php echo $out; ?>
						</div>
					</fieldset>
					<?php
				}
			}
		?>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
