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

Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS'), 'list.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList('COM_NEWSLETTER_MAILINGLIST_DELETE_CHECK', 'delete');
Toolbar::spacer();
Toolbar::custom('manage', 'user', '', 'COM_NEWSLETTER_TOOLBAR_MANAGE');
Toolbar::custom('export', 'export', '', 'COM_NEWSLETTER_TOOLBAR_EXPORT');
Toolbar::spacer();
Toolbar::preferences($this->option, '550');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->lists); ?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NAME'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_ACTIVE_SUBSCRIBERS'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_TOTAL_SUBSCRIBERS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->lists) > 0) : ?>
				<?php foreach ($this->lists as $k => $list) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $list->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $list->id); ?>">
								<?php echo $this->escape($list->name); ?>
							</a>
						</td>
						<td class="priority-3">
							<span class="access <?php echo ($list->private) ? 'private' : 'public'; ?>">
								<?php echo ($list->private) ? Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PRIVATE') : Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PUBLIC'); ?>
							</span>
						</td>
						<td class="priority-2">
							<?php echo $list->active_count; ?>
						</td>
						<td class="priority-2">
							<?php echo $list->total_count; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="5">
						<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_NO_LISTS', "javascript:submitbutton('add');"); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_newsletter" />
	<input type="hidden" name="controller" value="mailinglist" />
	<input type="hidden" name="task" value="add" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>