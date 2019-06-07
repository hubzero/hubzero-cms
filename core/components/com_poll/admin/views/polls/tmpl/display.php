<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('tooltip');

$canDo = \Components\Poll\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_POLL'), 'poll.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_poll', '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_POLL_CONFIRM_DELETE');
	Toolbar::spacer();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('polls');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_POLL_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_POLL_GO'); ?>" />
				<button class="filter filter-submit"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<?php echo $this->filters['states']; ?>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" />
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_POLL_COL_NUM'); ?>
				</th>
				<th scope="col" class="title">
					<?php echo Html::grid('sort', 'COM_POLL_COL_TITLE', 'title', @$this->filters['order_Dir'], @$this->filters['order']); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'COM_POLL_COL_PUBLISHED', 'state', @$this->filters['order_Dir'], @$this->filters['order']); ?>
				</th>
				<th scope="col" class="priority-2">
					<?php echo Html::grid('sort', 'COM_POLL_COL_OPEN', 'open', @$this->filters['order_Dir'], @$this->filters['order']); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo Html::grid('sort', 'COM_POLL_COL_VOTES', 'voters', @$this->filters['order_Dir'], @$this->filters['order']); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Lang::txt('COM_POLL_COL_OPTIONS'); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Html::grid('sort', 'COM_POLL_COL_LAG', 'lag', @$this->filters['order_Dir'], @$this->filters['order']); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Html::grid('sort', 'COM_POLL_COL_ID', 'id', @$this->filters['order_Dir'], @$this->filters['order']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php
					echo $this->rows->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			$task  = $row->get('state') ? 'unpublish' : 'publish';
			$class = $row->get('state') ? 'published' : 'unpublished';
			$alt   = $row->get('state') ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');

			$task2  = ($row->get('open') == 1) ? 'close' : 'open';
			$class2 = ($row->get('open') == 1) ? 'published' : 'unpublished';
			$alt2   = ($row->get('open') == 1) ? Lang::txt('COM_POLL_OPEN') : Lang::txt('COM_POLL_CLOSED');
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if (($row->get('checked_out') && $row->get('checked_out') != User::get('id')) || !$canDo->get('core.edit')) { ?>
						<span> </span>
					<?php } else { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
					<?php } ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php if (($row->get('checked_out') && $row->get('checked_out') != User::get('id')) || !$canDo->get('core.edit')) {
						echo $row->get('title');
					} else { ?>
						<span class="editlinktip hasTip" title="<?php echo $this->escape($row->get('title')); ?>">
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&view=poll&task=edit&id='. $row->get('id')); ?>">
								<?php echo $this->escape($row->get('title')); ?>
							</a>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class;?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_POLL_SET_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class2; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $task2 . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_POLL_SET_TO', $task2); ?>">
							<span><?php echo $alt2; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class2; ?>">
							<span><?php echo $alt2; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php echo $row->dates->count(); ?>
				</td>
				<td class="priority-4">
					<?php echo $row->options->count(); ?>
				</td>
				<td class="priority-4">
					<?php echo $row->get('lag'); ?>
				</td>
				<td class="priority-5">
					<?php echo $row->get('id'); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['order']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['order_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
