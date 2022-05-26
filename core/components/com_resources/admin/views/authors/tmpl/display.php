<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('contributor');

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_AUTHORS'), 'resources');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_RESOURCES_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_ID', 'authorid', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_RESOURCES_COL_MEMBER'); ?></th>
				<?php /* Temporarily removed until query can be rewritten
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_RESOURCES', 'resources', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> */ ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4"><?php
				// Initiate paging
				echo $this->rows->pagination;
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			if ($row->authorid > 0)
			{
				$stickyTask = '1';
				$stickyAlt = Lang::txt('JYES');
				$scls = 'member';
			}
			else
			{
				$stickyTask = '0';
				$stickyAlt = Lang::txt('JNO');
				$scls = 'notmember';
			}

			if ($row->authorid > 0 && !$row->name)
			{
				$u = User::getInstance($row->authorid);
				$row->name = $u->get('name');
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->authorid; ?>" class="checkbox-toggle" />
						<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->authorid; ?></label>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $row->authorid; ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->authorid); ?>">
							<span><?php echo ($row->name) ? $this->escape(stripslashes($row->name)) : Lang::txt('COM_RESOURCES_UNKNOWN'); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo ($row->name) ? $this->escape(stripslashes($row->name)) : Lang::txt('COM_RESOURCES_UNKNOWN'); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($row->authorid > 0) { ?>
						<a class="state <?php echo $scls; ?>" href="<?php echo Route::url('index.php?option=com_members&task=edit&id=' . $row->authorid); ?>">
							<span><?php echo $stickyAlt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $scls; ?>">
							<span><?php echo $stickyAlt; ?></span>
						</span>
					<?php } ?>
				</td>
				<?php /* Temporarily removed until query can be rewritten
				<td class="priority-3">
					<?php echo Lang::txt('COM_RESOURCES_AUTHORS_NUM_RESOURCES', $this->escape(stripslashes($row->resources))); ?>
				</td> */ ?>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
