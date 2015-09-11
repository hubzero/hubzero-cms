<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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