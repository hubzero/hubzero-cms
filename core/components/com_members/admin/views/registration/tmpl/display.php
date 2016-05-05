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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Admin::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION'), 'users');
if ($canDo->get('core.edit'))
{
	Toolbar::preferences($this->option);
	Toolbar::save();
	Toolbar::cancel();
}
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_AREA'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_CREATE_ACCOUNT'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_PROXY_CREATE_ACCOUNT'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_UPDATE_ACCOUNT'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_EDIT_ACCOUNT'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($this->params as $field => $values)
{
	if (substr($field, 0, strlen('registration')) == 'registration')
	{
		$title = $values->title;
		$value = $values->value;

		$create = strtoupper(substr($value, 0, 1));
		$proxy  = strtoupper(substr($value, 1, 1));
		$update = strtoupper(substr($value, 2, 1));
		$edit   = strtoupper(substr($value, 3, 1));

		$field = str_replace('registration', '', $values->name);
?>
				<tr>
					<td><?php echo $title; ?></td>
					<td>
						<?php if ($create != '-') : ?>
							<select name="settings[<?php echo $field; ?>][create]">
								<option value="O"<?php if ($create == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($create == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($create == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($create == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][create]" value="-">
						<?php endif; ?>
					</td>
					<td>
						<?php if ($proxy != '-') : ?>
							<select name="settings[<?php echo $field; ?>][proxy]">
								<option value="O"<?php if ($proxy == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($proxy == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($proxy == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($proxy == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][proxy]" value="-">
						<?php endif; ?>
					</td>
					<td>
						<?php if ($update != '-') : ?>
							<select name="settings[<?php echo $field; ?>][update]">
								<option value="O"<?php if ($update == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($update == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($update == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($update == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][update]" value="-">
						<?php endif; ?>
					</td>
					<td>
						<?php if ($edit != '-') : ?>
							<select name="settings[<?php echo $field; ?>][edit]">
								<option value="O"<?php if ($edit == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($edit == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($edit == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($edit == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][edit]" value="-">
						<?php endif; ?>
					</td>
				</tr>
<?php
	}
}
?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php echo Html::input('token'); ?>
	</fieldset>
</form>