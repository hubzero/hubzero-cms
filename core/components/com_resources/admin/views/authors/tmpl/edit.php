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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('contributor');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_AUTHORS') . ': ' . $text, 'forum.png');
Toolbar::spacer();
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<table class="admintable">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_RESOURCE'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_NAME'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_ORGANIZATION'); ?></th>
							<th scope="col"><?php echo Lang::txt('COM_RESOURCES_COL_ROLE'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$i = 0;
					foreach ($this->rows as $row)
					{
						?>
						<tr>
							<td>
								<input type="text" name="fields[<?php echo $i; ?>][subid]" maxlength="250" size="4" value="<?php echo $this->escape(stripslashes($row->subid)); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][ordering]" value="<?php echo $this->escape(stripslashes($row->ordering)); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][subtable]" value="<?php echo $this->escape(stripslashes($row->subtable)); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][authorid]" value="<?php echo $this->escape($this->authorid); ?>" />
								<input type="hidden" name="fields[<?php echo $i; ?>][id]" value="<?php echo $this->escape(stripslashes($row->id)); ?>" />
							</td>
							<td>
								<input type="text" name="fields[<?php echo $i; ?>][name]" maxlength="250" value="<?php echo $this->escape(stripslashes($row->name)); ?>" />
							</td>
							<td>
								<input type="text" name="fields[<?php echo $i; ?>][organization]" maxlength="250" value="<?php echo $this->escape(stripslashes($row->organization)); ?>" />
							</td>
							<td>
								<select name="fields[<?php echo $i; ?>][role]">
									<option value=""<?php if ($row->role == '') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_RESOURCES_ROLE_AUTHOR'); ?></option>
									<?php
									if ($this->roles)
									{
										foreach ($this->roles as $role)
										{
											?>
											<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($row->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
											<?php
										}
									}
									?>
								</select>
							</td>
						</tr>
						<?php
						$i++;
					}
					?>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span4">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_FIELDSET_AUTHOR'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-authorid"><?php echo Lang::txt('COM_RESOURCES_FIELD_ID'); ?>:</label><br />
					<input type="text" name="authorid" id="field-authorid" value="<?php echo $this->escape($this->authorid); ?>" />
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->authorid; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
