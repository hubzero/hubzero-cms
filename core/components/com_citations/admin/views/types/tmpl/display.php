<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('type');

Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_TYPES'), 'citations');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::spacer();
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('types');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('CITATION_TYPES_ID'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('CITATION_TYPES_ALIAS'); ?></th>
				<th scope="col"><?php echo Lang::txt('CITATION_TYPES_TITLE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->types as $i => $t) : ?>
				<tr>
					<td>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $t['id']; ?>" class="checkbox-toggle" />
					</td>
					<td class="priority-3">
						<?php echo $t['id']; ?>
					</td>
					<td class="priority-2">
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $t['id']); ?>">
							<span><?php echo $this->escape($t['type']); ?></span>
						</a>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $t['id']); ?>">
							<span><?php echo $this->escape($t['type_title']); ?></span>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
