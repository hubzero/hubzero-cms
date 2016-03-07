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

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') . ': ' . $this->list->name, 'list.png');

//add toolbar buttons
Toolbar::custom('doaddemail', 'save', '', 'COM_NEWSLETTER_TOOLBAR_SUBMIT', false);
Toolbar::cancel('cancelemail');
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS'); ?></legend>

		<table class="admintable">
			<tbody>
				<tr>
					<th width="200px"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_MAILINGLIST'); ?>:</th>
					<td><strong><?php echo $this->list->name; ?></strong></td>
				</tr>
				<tr>
					<th width="200px"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION'); ?>:</th>
					<td>
						<select name="email_confirmation">
							<option value="-1"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NULL'); ?></option>
							<option value="1"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_YES'); ?></option>
							<option value="0"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_CONFIRMATION_OPTION_NO'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_FILE'); ?>:</th>
					<td>
						<input type="file" name="email_file" />
					</td>
				</tr>
				<?php if (!empty($this->groups)): ?>
				<tr>
					<td colspan="2">
						<span style="display:block;text-align:center;font-weight:bold;font-size:18px"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR'); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP'); ?>:</th>
					<td>
						<select name="email_group">
							<option value=""><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_GROUP_OPTION_NULL'); ?></option>
							<?php foreach ($this->groups as $group) : ?>
								<option value="<?php echo $group->gidNumber; ?>"><?php echo $group->description; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td colspan="2">
						<span style="display:block;text-align:center;font-weight:bold;font-size:18px"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_AND_OR'); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_RAW'); ?>:</th>
					<td>
						<textarea name="email_box" rows="10" cols="100"><?php echo $this->emailBox; ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="mid" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="task" value="doimportemail" />

	<?php echo Html::input('token'); ?>
</form>
