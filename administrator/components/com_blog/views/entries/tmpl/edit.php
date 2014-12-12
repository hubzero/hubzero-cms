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

$canDo = BlogHelperPermissions::getActions('entry');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));
JToolBarHelper::title(JText::_('COM_BLOG_TITLE') . ': ' . $text, 'blog.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::spacer();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('entry');
?>
<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		Joomla.submitform(pressbutton, document.getElementById('item-form'));
		return;
	}

	// do field validation
	if ($('#field-title').val() == ''){
		alert("<?php echo JText::_('COM_BLOG_ERROR_MISSING_TITLE'); ?>");
	} else if ($('#field-content').val() == ''){
		alert("<?php echo JText::_('COM_BLOG_ERROR_MISSING_CONTENT'); ?>");
	} else {
		<?php echo JFactory::getEditor()->save('text'); ?>

		Joomla.submitform(pressbutton, document.getElementById('item-form'));
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<div class="col width-50 fltlft">
				<div class="input-wrap" data-hint="<?php echo JText::_('COM_BLOG_FIELD_SCOPE_HINT'); ?>">
					<label for="field-scope"><?php echo JText::_('COM_BLOG_FIELD_SCOPE'); ?>:</label><br />
					<?php if (!$this->row->exists()) { ?>
						<select name="fields[scope]" id="field-scope">
							<option value="site"<?php if ($this->row->get('scope') == 'site' || $this->row->get('scope') == '') { echo ' selected="selected"'; } ?>>site</option>
							<option value="member"<?php if ($this->row->get('scope') == 'member') { echo ' selected="selected"'; } ?>>member</option>
							<option value="group"<?php if ($this->row->get('scope') == 'group') { echo ' selected="selected"'; } ?>>group</option>
						</select>
					<?php } else { ?>
						<input type="text" name="fields[scope]" id="field-scope" disabled="disabled" value="<?php echo $this->escape(stripslashes($this->row->get('scope'))); ?>" />
					<?php } ?>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-scope_id"><?php echo JText::_('COM_BLOG_FIELD_SCOPE_ID'); ?>:</label><br />
					<?php if (!$this->row->exists()) { ?>
						<input type="text" name="fields[scope_id]" id="field-scope_id" value="<?php echo $this->escape(stripslashes($this->row->get('scope_id'))); ?>" />
					<?php } else { ?>
						<input type="text" name="fields[scope_id]" id="field-scope_id" disabled="disabled" value="<?php echo $this->escape(stripslashes($this->row->get('scope_id'))); ?>" />
					<?php } ?>
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_BLOG_FIELD_TITLE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[title]" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_BLOG_FIELD_ALIAS_HINT'); ?>">
				<label for="field-alias"><?php echo JText::_('COM_BLOG_FIELD_ALIAS'); ?>:</label><br />
				<input type="text" name="fields[alias]" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				<span class="hint"><?php echo JText::_('COM_BLOG_FIELD_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="field-content"><?php echo JText::_('COM_BLOG_FIELD_CONTENT'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<?php echo JFactory::getEditor()->display('fields[content]', $this->escape($this->row->content('raw')), '', '', 50, 30, false, 'field-content'); ?>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_BLOG_FIELD_TAGS_HINT'); ?>">
				<label for="field-tags"><?php echo JText::_('COM_BLOG_FIELD_TAGS'); ?>:</label><br />
				<textarea name="tags" id="field-tags" cols="35" rows="3"><?php echo $this->escape(stripslashes($this->row->tags('string'))); ?></textarea>
				<span class="hint"><?php echo JText::_('COM_BLOG_FIELD_TAGS_HINT'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('COM_BLOG_FIELD_CREATOR'); ?>:</th>
					<td>
						<?php
						$editor = JUser::getInstance($this->row->get('created_by'));
						echo $this->escape(stripslashes($editor->get('name')));
						?>
						<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_BLOG_FIELD_CREATED'); ?>:</th>
					<td>
						<?php echo $this->row->get('created'); ?>
						<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
					</td>
				</tr>
				<tr>
					<th class="key"><?php echo JText::_('COM_BLOG_FIELD_HITS'); ?>:</th>
					<td>
						<?php echo $this->row->get('hits'); ?>
						<input type="hidden" name="fields[hits]" id="field-hits" value="<?php echo $this->escape($this->row->get('hits')); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<input class="option" type="checkbox" name="fields[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->row->get('allow_comments')) { echo ' checked="checked"'; } ?> />
				<label for="field-allow_comments"><?php echo JText::_('COM_BLOG_FIELD_ALLOW_COMMENTS'); ?></label>
			</div>

			<div class="input-wrap">
				<label for="field-state"><?php echo JText::_('COM_BLOG_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_STATE_PUBLIC'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_STATE_REGISTERED'); ?></option>
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_STATE_PRIVATE'); ?></option>
					<option value="-1"<?php if ($this->row->get('state') == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_BLOG_FIELD_STATE_TRASHED'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="field-publish_up"><?php echo JText::_('COM_BLOG_FIELD_PUBLISH_UP'); ?>:</label><br />
				<?php echo JHTML::_('calendar', ($this->row->get('publish_up') != '0000-00-00 00:00:00' ? $this->escape(JHTML::_('date', $this->row->get('publish_up'), 'Y-m-d H:i:s')) : ''), 'fields[publish_up]', 'field-publish_up'); ?>
			</div>

			<div class="input-wrap">
				<label for="field-publish_down"><?php echo JText::_('COM_BLOG_FIELD_PUBLISH_DOWN'); ?>:</label><br />
				<?php echo JHTML::_('calendar', ($this->row->get('publish_down') != '0000-00-00 00:00:00' ? $this->escape(JHTML::_('date', $this->row->get('publish_down'), 'Y-m-d H:i:s')) : ''), 'fields[publish_down]', 'field-publish_down'); ?>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="fields[access]" value="<?php echo $this->row->get('access', 0); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>