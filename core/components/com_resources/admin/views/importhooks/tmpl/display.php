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

Toolbar::title(Lang::txt('COM_RESOURCES_IMPORTHOOK_TITLE_HOOKS'), 'import.png');

Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->hooks->count(); ?>);" /></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_NAME'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_TYPE'); ?></th>
					<th scope="col" class="priority-2"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FIELD_FILE'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($this->hooks->count() > 0) : ?>
					<?php foreach ($this->hooks as $i => $hook) : ?>
						<tr>
							<td>
								<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $hook->get('id'); ?>" onclick="isChecked(this.checked);" />
							</td>
							<td>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $hook->get('id')); ?>">
									<?php echo $this->escape($hook->get('name')); ?>
								</a><br />
								<span class="hint">
									<?php echo nl2br($this->escape($hook->get('notes'))); ?>
								</span>
							</td>
							<td>
								<?php
									switch ($hook->get('type'))
									{
										case 'postconvert':    echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT');    break;
										case 'postmap':        echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTMAP');        break;
										case 'postparse':
										default:               echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE');      break;
									}
								?>
							</td>
							<td class="priority-2">
								<?php echo $hook->get('file'); ?> &mdash;
								<a target="_blank" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=raw&id=' . $hook->get('id')); ?>">
									<?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_DISPLAY_FILE_VIEWRAW'); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="4"><?php echo Lang::txt('COM_RESOURCES_IMPORTHOOK_NONE_FOUND'); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>