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

$canDo = CollectionsHelperPermissions::getActions('collection');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_COLLECTIONS') . ': ' . $text, 'collection.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('collection');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if ($('#field-title').val() == '') {
		alert('<?php echo JText::_('COM_COLLECTIONS_ERROR_MISSING_TITLE'); ?>');
	} else if ($('#field-object_type').val() == '') {
		alert('<?php echo JText::_('COM_COLLECTIONS_ERROR_MISSING_OBJECT_TYPE'); ?>');
	} else if ($('#field-object_id').val() == '') {
		alert('<?php echo JText::_('COM_COLLECTIONS_ERROR_MISSING_OBJECT_ID'); ?>');
	} else {
		<?php echo JFactory::getEditor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-object_type"><?php echo JText::_('COM_COLLECTIONS_FIELD_OWNER_TYPE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[object_type]" id="field-object_type">
						<!-- <option value="site"<?php if ($this->row->get('object_type') == 'site' || $this->row->get('object_type') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COLLECTIONS_FIELD_OWNER_TYPE_SITE'); ?></option> -->
						<option value="member"<?php if ($this->row->get('object_type') == 'member') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COLLECTIONS_FIELD_OWNER_TYPE_MEMBER'); ?></option>
						<option value="group"<?php if ($this->row->get('object_type') == 'group') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COLLECTIONS_FIELD_OWNER_TYPE_GROUP'); ?></option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-title"><?php echo JText::_('COM_COLLECTIONS_FIELD_OWNER_ID'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[object_id]" id="field-object_id" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('object_id'))); ?>" />
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_COLLECTIONS_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_COLLECTIONS_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_COLLECTIONS_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				<span class="hint"><?php echo JText::_('COM_COLLECTIONS_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_COLLECTIONS_FIELD_DESCRIPTION'); ?></label><br />
				<?php echo JFactory::getEditor()->display('fields[description]', $this->escape($this->row->description('raw')), '', '', 35, 10, false, 'field-description', null, null, array('class' => 'minimal no-footer')); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$editor = JUser::getInstance($this->row->get('created_by'));
						echo $this->escape(stripslashes($editor->get('name')));
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo $this->row->get('created'); ?>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_LIKES'); ?>:</th>
					<td>
						<?php echo $this->row->get('positive', 0); ?>
						<input type="hidden" name="fields[positive]" id="field-positive" value="<?php echo $this->escape($this->row->get('positive', 0)); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_POSTS'); ?>:</th>
					<td>
						<?php echo $this->row->count('post'); ?>
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_COLLECTIONS_FIELD_FOLLOWERS'); ?>:</th>
					<td>
						<?php echo $this->row->count('followers'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_COLLECTIONS_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('JTRASHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-access"><?php echo JText::_('COM_COLLECTIONS_FIELD_ACCESS'); ?>:</label><br />
				<select name="fields[access]" id="field-access">
					<option value="0"<?php if ($this->row->get('access') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COLLECTIONS_ACCESS_PUBLIC'); ?></option>
					<option value="1"<?php if ($this->row->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COLLECTIONS_ACCESS_REGISTERED'); ?></option>
					<option value="4"<?php if ($this->row->get('access') == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COLLECTIONS_ACCESS_PRIVATE'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>