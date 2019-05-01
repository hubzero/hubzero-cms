<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('sponsor');

Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_SPONSORS'), 'citations');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('sponsors');
?>

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