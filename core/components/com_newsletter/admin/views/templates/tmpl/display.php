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

$canDo = Components\Newsletter\Helpers\Permissions::getActions('template');

//set the title
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES'), 'template.png');

//add toolbar buttons
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
	Toolbar::custom('duplicate', 'copy', '', 'COM_NEWSLETTER_TOOLBAR_COPY');
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::spacer();
	Toolbar::deleteList('COM_NEWSLETTER_TEMPLATE_DELETE_CHECK', 'delete');
}
if ($canDo->get('core.admin'))
{
	Toolbar::spacer();
	Toolbar::preferences($this->option, '550');
}
?>
<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->templates); ?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->templates) > 0) : ?>
				<?php foreach ($this->templates as $k => $template) : ?>
					<tr>
						<td>
								<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $template->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<?php if (!$template->editable) : ?>
								<?php echo $template->name; ?>
								<br />
								<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_NOT_EDITABLE_OR_DELETABLE'); ?></span>
							<?php else: ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $template->id); ?>">
									<?php echo $template->name; ?>
								</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2">
						<?php echo Lang::txt('COM_NEWSLETTER_NO_TEMPLATES'); ?>
						<a onclick="javascript:submitbutton('add')" href="#"><?php echo Lang::txt('COM_NEWSLETTER_NO_TEMPLATES_CREATE'); ?></a>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="add" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
