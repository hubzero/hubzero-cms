<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

// No direct access.
defined('_HZEXEC_') or die();

Request::setVar('hidemainmenu', true);

$canDo = \Components\Redirect\Helpers\Redirect::getActions();

Toolbar::title(Lang::txt('COM_REDIRECT_MANAGER_LINK'), 'redirect');
// If not checked out, can save the item.
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
// This component does not support Save as Copy due to uniqueness checks.
// While it can be done, it causes too much confusion if the user does
// not change the Old URL.
if ($canDo->get('core.edit') && $canDo->get('core.create'))
{
	Toolbar::save2new();
}

if (empty($this->item->id))
{
	Toolbar::spacer();
	Toolbar::cancel('cancel');
}
else
{
	Toolbar::spacer();
	Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
}
Toolbar::spacer();
Toolbar::help('link');

// Include the HTML helpers.
Html::addIncludePath(dirname(JPATH_COMPONENT) . '/helpers/html');
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id=' . (int) $this->row->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo $this->row->isNew() ? Lang::txt('COM_REDIRECT_NEW_LINK') : Lang::txt('COM_REDIRECT_EDIT_LINK', $this->row->id); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_OLD_URL_DESC'); ?>">
					<label id="fields-old_url-lbl" for="fields-old_url"><?php echo Lang::txt('COM_REDIRECT_FIELD_OLD_URL_LABEL'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[old_url]" id="fields-old_url" value="<?php echo $this->escape($this->row->old_url); ?>" class="inputbox required" />
					<span class="hint"><?php echo Lang::txt('COM_REDIRECT_FIELD_OLD_URL_DESC'); ?></span>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>">
					<label id="fields-new_url-lbl" for="fields-new_url"><?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[new_url]" id="fields-new_url" value="<?php echo $this->escape($this->row->new_url); ?>" class="inputbox required" />
					<span class="hint"><?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?></span>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>">
					<label id="fields-comment-lbl" for="fields-comment"><?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL'); ?></label>
					<input type="text" name="fields[comment]" id="fields-comment" value="<?php echo $this->escape($this->row->comment); ?>" class="inputbox required" />
					<span class="hint"><?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('JGLOBAL_FIELD_ID_LABEL'); ?></th>
						<td>
							<?php echo $this->escape($this->row->id); ?>
							<input type="hidden" name="fields[id]" id="fields-id" value="<?php echo $this->escape($this->row->id); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_REDIRECT_FIELD_CREATED_DATE_LABEL'); ?></th>
						<td>
							<?php echo $this->escape($this->row->created_date); ?>
							<input type="hidden" name="fields[created_date]" id="fields-created_date" value="<?php echo $this->escape($this->row->created_date); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_REDIRECT_FIELD_UPDATED_DATE_LABEL'); ?></th>
						<td>
							<?php echo $this->escape($this->row->modified_date); ?>
							<input type="hidden" name="fields[modified_date]" id="fields-modified_date" value="<?php echo $this->escape($this->row->modified_date); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('JGLOBAL_HITS'); ?></th>
						<td>
							<?php echo $this->escape($this->row->hits); ?>
							<input type="hidden" name="fields[hits]" id="fields-hits" value="<?php echo $this->escape($this->row->hits); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_REDIRECT_OPTIONS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('JFIELD_PUBLISHED_DESC'); ?>">
					<label id="fields-published-lbl" for="fields-published"><?php echo Lang::txt('JSTATUS'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<select name="fields[published]" id="fields-published">
						<option value="1"<?php if ($this->row->published == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JENABLED'); ?></option>
						<option value="0"<?php if ($this->row->published == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JDISABLED'); ?></option>
						<option value="2"<?php if ($this->row->published == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JARCHIVED'); ?></option>
						<option value="-2"<?php if ($this->row->published == -2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

	<?php echo Html::input('token'); ?>
</form>
