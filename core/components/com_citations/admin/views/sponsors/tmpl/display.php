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

$canDo = \Components\Citations\Helpers\Permissions::getActions('sponsor');

Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_SPONSORS'), 'citation.png');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('sponsors');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th class="priority-3"><?php echo Lang::txt('CITATION_ID'); ?></th>
				<th><?php echo Lang::txt('CITATION_SPONSORS'); ?></th>
				<th class="priority-2"><?php echo Lang::txt('CITATION_SPONSORS_LINK'); ?></th>
				<th class="priority-4"><?php echo Lang::txt('CITATION_SPONSORS_IMAGE'); ?></th>
				<th><?php echo Lang::txt('CITATION_SPONSORS_ACTIONS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->sponsors) > 0) : ?>
				<?php foreach ($this->sponsors as $sponsor) : ?>
					<tr>
						<td class="priority-3"><?php echo $sponsor['id']; ?></td>
						<td><?php echo $this->escape($sponsor['sponsor']); ?></td>
						<td class="priority-2"><?php echo $this->escape($sponsor['link']); ?></td>
						<td class="priority-4"><?php echo $this->escape($sponsor['image']); ?></td>
						<td>
							<?php if ($canDo->get('core.edit')) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $sponsor['id']); ?>"><?php echo Lang::txt('JACTION_EDIT'); ?></a> |
							<?php } ?>
							<?php if ($canDo->get('core.delete')) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=remove&id=' . $sponsor['id']); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
							<?php } ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="5"><?php echo Lang::txt('COM_CITATIONS_SPONSORS_NO_RESULTS'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />

	<?php echo Html::input('token'); ?>
</form>