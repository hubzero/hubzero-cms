<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_JEXEC') or die;

JRequest::setVar('hidemainmenu', true);

$canDo = \Components\Redirect\Helpers\Redirect::getActions();

JToolBarHelper::title(JText::_('COM_REDIRECT_MANAGER_LINK'), 'redirect');
// If not checked out, can save the item.
if ($canDo->get('core.edit'))
{
	JToolBarHelper::apply();
	JToolBarHelper::save();
}
// This component does not support Save as Copy due to uniqueness checks.
// While it can be done, it causes too much confusion if the user does
// not change the Old URL.
if ($canDo->get('core.edit') && $canDo->get('core.create'))
{
	JToolBarHelper::save2new();
}

if (empty($this->item->id))
{
	JToolBarHelper::spacer();
	JToolBarHelper::cancel('cancel');
}
else
{
	JToolBarHelper::spacer();
	JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
}
JToolBarHelper::spacer();
JToolBarHelper::help('link');

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=edit&id=' . (int) $this->row->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo empty($this->row->id) ? JText::_('COM_REDIRECT_NEW_LINK') : JText::sprintf('COM_REDIRECT_EDIT_LINK', $this->row->id); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_REDIRECT_FIELD_OLD_URL_DESC'); ?>">
				<label id="fields-old_url-lbl" for="fields-old_url"><?php echo JText::_('COM_REDIRECT_FIELD_OLD_URL_LABEL'); ?> <span class="required"><?php echo JText::_('JREQUIRED'); ?></span></label>
				<input type="text" name="fields[old_url]" id="fields-old_url" value="<?php echo $this->escape($this->row->old_url); ?>" class="inputbox required" />
				<span class="hint"><?php echo JText::_('COM_REDIRECT_FIELD_OLD_URL_DESC'); ?></span>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>">
				<label id="fields-new_url-lbl" for="fields-new_url"><?php echo JText::_('COM_REDIRECT_FIELD_NEW_URL_LABEL'); ?> <span class="required"><?php echo JText::_('JREQUIRED'); ?></span></label>
				<input type="text" name="fields[new_url]" id="fields-new_url" value="<?php echo $this->escape($this->row->new_url); ?>" class="inputbox required" />
				<span class="hint"><?php echo JText::_('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?></span>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>">
				<label id="fields-comment-lbl" for="fields-comment"><?php echo JText::_('COM_REDIRECT_FIELD_COMMENT_LABEL'); ?></label>
				<input type="text" name="fields[comment]" id="fields-comment" value="<?php echo $this->escape($this->row->comment); ?>" class="inputbox required" />
				<span class="hint"><?php echo JText::_('COM_REDIRECT_FIELD_COMMENT_DESC'); ?></span>
			</div>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
					<td>
						<?php echo $this->escape($this->row->id); ?>
						<input type="hidden" name="fields[id]" id="fields-id" value="<?php echo $this->escape($this->row->id); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_REDIRECT_FIELD_CREATED_DATE_LABEL'); ?></th>
					<td>
						<?php echo $this->escape($this->row->modified_date); ?>
						<input type="hidden" name="fields[created_date]" id="fields-created_date" value="<?php echo $this->escape($this->row->created_date); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_REDIRECT_FIELD_UPDATED_DATE_LABEL'); ?></th>
					<td>
						<?php echo $this->escape($this->row->modified_date); ?>
						<input type="hidden" name="fields[modified_date]" id="fields-modified_date" value="<?php echo $this->escape($this->row->modified_date); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo JText::_('JGLOBAL_HITS'); ?></th>
					<td>
						<?php echo $this->escape($this->row->hits); ?>
						<input type="hidden" name="fields[hits]" id="fields-hits" value="<?php echo $this->escape($this->row->hits); ?>" />
					</td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_REDIRECT_OPTIONS'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?>">
				<label id="fields-published-lbl" for="fields-published"><?php echo JText::_('JSTATUS'); ?> <span class="required"><?php echo JText::_('JREQUIRED'); ?></span></label>
				<select name="fields[published]" id="fields-published">
					<option value="1"<?php if ($this->row->published == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('JENABLED'); ?></option>
					<option value="0"<?php if ($this->row->published == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('JDISABLED'); ?></option>
					<option value="2"<?php if ($this->row->published == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('JARCHIVED'); ?></option>
					<option value="-2"<?php if ($this->row->published == -2) { echo ' selected="selected"'; } ?>><?php echo JText::_('JTRASHED'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

	<?php echo JHtml::_('form.token'); ?>
</form>
