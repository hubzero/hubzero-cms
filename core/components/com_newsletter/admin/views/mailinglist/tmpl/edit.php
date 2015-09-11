<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') . ': ' . $text, 'list.png');
Toolbar::save();
Toolbar::cancel();
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<?php if (!$this->list->id) : ?>
		<p class="info"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_MUST_CREATE_BEFORE_ADD'); ?></p>
	<?php endif; ?>
	<fieldset class="adminform">
		<legend><span><?php echo $text; ?> <?php echo Lang::txt('COM_NEWSLETTER_LISTS'); ?></span></legend>

		<div class="input-wrap">
			<label for="field-name"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NAME'); ?>:</label><br />
			<input type="text" name="list[name]" id="field-name" value="<?php echo $this->escape($this->list->name); ?>" /></td>
		</div>

		<div class="input-wrap">
			<label for="field-private"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY'); ?>:</label><br />
			<select name="list[private]" id="field-private">
				<option value="0" <?php echo ($this->list->private == 0) ? 'selected="selected"': ''; ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PUBLIC'); ?></option>
				<option value="1" <?php echo ($this->list->private == 1) ? 'selected="selected"': ''; ?>><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PRIVATE'); ?></option>
			</select>
		</div>

		<div class="input-wrap">
			<label for="field-description"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_DESC'); ?>:</label><br />
			<textarea name="list[description]" id="field-description" rows="5"><?php echo $this->list->description; ?></textarea>
		</div>
	</fieldset>

	<input type="hidden" name="list[id]" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>