<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title($this->group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_CATEGORIES'), 'groups');

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
	Toolbar::deleteList('COM_GROUPS_PAGES_CATEGORIES_CONFIRM_DELETE', 'delete');
}
Toolbar::spacer();
Toolbar::custom('manage', 'config', 'config', 'COM_GROUPS_MANAGE', false);
?>

<?php require_once dirname(dirname(__DIR__)) . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn); ?>" name="adminForm" id="adminForm" method="post">
	<table class="adminlist">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE'); ?></th>
				<th class="priority-3"><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ($this->rows->count() > 0) : ?>
				<?php foreach ($this->rows as $k => $category) : ?>
					<tr>
						<td>
							<?php if ($canDo->get('core.edit')) : ?>
								<input type="checkbox" name="id[]" id="cb<?php echo $k;?>" value="<?php echo $category->get('id'); ?>" class="checkbox-toggle" />
							<?php endif; ?>
						</td>
						<td>
							<?php if ($canDo->get('core.edit')) : ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn . '&task=edit&id=' . $category->get('id')); ?>">
									<?php echo $this->escape($category->get('title')); ?>
								</a>
							<?php else : ?>
								<?php echo $this->escape($category->get('title')); ?>
							<?php endif; ?>
						</td>
						<td class="priority-3"><?php echo '#' . $this->escape($category->get('color')); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="3"><?php echo Lang::txt('COM_GROUPS_PAGES_NO_CATEGORIES'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>">
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>