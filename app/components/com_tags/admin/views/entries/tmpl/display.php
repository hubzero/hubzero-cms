<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS'), 'tags');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
Toolbar::custom('calculate', 'refresh', '', 'COM_TAGS_RECALCULATE', false);
if ($canDo->get('core.edit'))
{
	Toolbar::custom('pierce', 'copy', '', 'COM_TAGS_PIERCE', false);
	Toolbar::custom('merge', 'forward', '', 'COM_TAGS_MERGE', false);
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_TAGS_CONFIRM_DELETE');
}
Toolbar::spacer();
Toolbar::help('entries');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="filter_search"><?php echo Lang::txt('COM_TAGS_SEARCH'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TAGS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" name="filter_submit" value="<?php echo Lang::txt('COM_TAGS_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span6">
				<label for="filter-filterby"><?php echo Lang::txt('COM_TAGS_FILTER'); ?>:</label>
				<select name="filterby" id="filter-filterby" class="filter filter-submit">
					<option value="all"<?php if ($this->filters['by'] == 'all') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TAGS_FILTER_ALL_TAGS'); ?></option>
					<option value="user"<?php if ($this->filters['by'] == 'user') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TAGS_FILTER_USER_TAGS'); ?></option>
					<option value="admin"<?php if ($this->filters['by'] == 'admin') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TAGS_FILTER_ADMIN_TAGS'); ?></option>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TAGS_COL_RAW_TAG', 'raw_tag', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TAGS_COL_TAG', 'tag', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TAGS_COL_ADMIN', 'admin', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TAGS_COL_NUMBER_TAGGED', 'objects', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TAGS_COL_ALIAS', 'substitutes', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TAGS_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php
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
			if ($row->get('admin'))
			{
				$calt = Lang::txt('JYES');
				$cls2 = 'yes';
				$state = 0;
			}
			else
			{
				$calt = Lang::txt('JNO');
				$cls2 = 'no';
				$state = 1;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('raw_tag'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('tag'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('tag'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<span class="state <?php echo $cls2; ?>">
						<span><?php echo $calt; ?></span>
					</span>
				</td>
				<td class="priority-3">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tagged&tag=' . $row->get('id')); ?>">
						<?php echo $row->get('objects', 0); ?>
					</a>
				</td>
				<td class="priority-3">
					<?php echo $row->get('substitutes', 0); ?>
				</td>
				<td class="priority-4">
					<time datetime="<?php echo $row->created(); ?>"><?php echo ($row->created() && $row->created() != '0000-00-00 00:00:00') ? $row->created() : Lang::txt('COM_TAGS_UNKNOWN'); ?></time>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>