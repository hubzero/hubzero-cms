<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_PASSWORD_RULES'), 'user');
if ($canDo->get('core.manage'))
{
	//Toolbar::confirm('COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS_CONFIRM', 'refresh', 'COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS', 'restore_default_content');
	Toolbar::custom('restore_default_content', 'refresh', 'refresh', 'COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS', false, false);
	Toolbar::spacer();
}
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
?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'passwordrules') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=passwordrules'); ?>"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'passwordblacklist') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=passwordblacklist'); ?>"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_MEMBERS_PASSWORD_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_MEMBERS_PASSWORD_RULE', 'rule', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_MEMBERS_PASSWORD_DESCRIPTION', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<?php echo Html::grid('sort', 'COM_MEMBERS_PASSWORD_ORDERING', 'ordering', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
					<?php echo Html::grid('order', $this->rows); ?>
				</th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_MEMBERS_PASSWORD_ENABLED', 'enabled', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					// Initiate paging
					echo $this->rows->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		$n = $this->rows->count();
		foreach ($this->rows as $row)
		{
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) : ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
					<?php endif; ?>
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape(stripslashes($row->get('rule'))); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->description); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($row->description); ?>
					<?php endif; ?>
				</td>
				<td class="order">
					<?php if ($canDo->get('core.edit')) : ?>
						<span><?php
						if ($i > 0)
						{
							echo Html::grid('orderUp', $i, 'orderup', '', 'JLIB_HTML_MOVE_UP', true, 'cb');
						}
						else
						{
							echo '&#160;';
						}
						//echo $pageNav->orderUpIcon($i, $row->ordering, 'orderup', 'JLIB_HTML_MOVE_UP', $row->ordering);
						?></span>
						<span><?php
						if ($i < ($n - 1))
						{
							echo Html::grid('orderDown', $i, 'orderdown', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb');
						}
						else
						{
							echo '&#160;';
						}
						//echo $pageNav->orderDownIcon($i, $n, $row->ordering, 'orderdown', 'JLIB_HTML_MOVE_DOWN', $row->ordering);
						?></span>
						<?php $disabled = $row->get('ordering') ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->get('ordering'); ?>" <?php echo $disabled; ?> class="text_area align-center" />
					<?php else : ?>
						<?php echo $row->get('ordering'); ?>
					<?php endif; ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit')) : ?>
						<a class="state <?php echo $row->get('enabled') ? 'yes': 'no'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=toggle_enabled&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
							<span><?php echo Lang::txt(($row->get('enabled') ? 'JYES': 'JNO')); ?></span>
						</a>
					<?php else : ?>
						<span class="state <?php echo $row->get('enabled') ? 'yes': 'no'; ?>">
							<span><?php echo Lang::txt(($row->get('enabled') ? 'JYES': 'JNO')); ?></span>
						</span>
					<?php endif; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>