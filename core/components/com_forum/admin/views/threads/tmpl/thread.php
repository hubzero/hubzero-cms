<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Forum\Helpers\Permissions::getActions('thread');

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_POSTS'), 'forum');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('thread');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label for="field-category_id"><?php echo Lang::txt('COM_FORUM_FILTER_CATEGORY'); ?>:</label>
				<select name="category_id" id="field-category_id" class="filter filter-submit">
					<option value="-1"><?php echo Lang::txt('COM_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
					<?php foreach ($this->sections as $group => $sections) { ?>
						<optgroup label="<?php echo $this->escape(stripslashes($group)); ?>">
							<?php foreach ($sections as $section) { ?>
								<optgroup label="&nbsp; &nbsp; <?php echo $this->escape(stripslashes($section->title)); ?>">
									<?php foreach ($section->categories as $category) { ?>
										<option value="<?php echo $category->id; ?>"<?php if ($this->filters['category_id'] == $category->id) { echo ' selected="selected"'; } ?>>&nbsp; &nbsp; <?php echo $this->escape(stripslashes($category->title)); ?></option>
									<?php } ?>
								</optgroup>
							<?php } ?>
						</optgroup>
					<?php } ?>
				</select>
			</div>
			<div class="col span6 align-right">
				<label for="filter-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="-1"<?php if ($this->filters['state'] == '-1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FORUM_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['state'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['state'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->filters['state'] === 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>

				<label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
				<select name="access" id="filter-access" class="filter filter-submit">
					<option value="-1"><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<?php if (is_object($this->thread)) { ?>
			<caption><?php echo Lang::txt('COM_FORUM_THREAD') . ': ' . $this->escape(stripslashes($this->thread->title)); ?></caption>
		<?php } ?>
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_FORUM_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_FORUM_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_FORUM_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_FORUM_COL_SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_FORUM_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_FORUM_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
				switch ($row->get('state'))
				{
					case '2':
						$task = 'publish';
						$alt = Lang::txt('JTRASHED');
						$cls = 'trash';
					break;
					case '1':
						$task = 'unpublish';
						$alt = Lang::txt('JPUBLISHED');
						$cls = 'publish';
					break;
					case '0':
					default:
						$task = 'publish';
						$alt = Lang::txt('JUNPUBLISHED');
						$cls = 'unpublish';
					break;
				}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
						<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo $row->get('id'); ?></label>
					</td>
					<td class="priority-5">
						<?php echo $row->get('id'); ?>
					</td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id . '&thread=' . $this->filters['thread']); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
					</td>
					<td>
						<?php if ($canDo->get('core.edit.state')) { ?>
							<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&category_id=' . $this->filters['category_id'] . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_FORUM_SET_TO', $task); ?>">
								<span><?php echo $alt; ?></span>
							</a>
						<?php } else { ?>
							<span class="state <?php echo $cls; ?>">
								<span><?php echo $alt; ?></span>
							</span>
						<?php } ?>
					</td>
					<td class="priority-3">
						<span class="scope">
							<span><?php echo $this->escape($row->get('scope')); ?> <?php echo ($row->get('scope_id')) ? '(' . $this->escape($row->get('scope_id')) . ')' : ''; ?></span>
						</span>
					</td>
					<td class="priority-5">
						<span class="creator">
							<span><?php echo $this->escape($row->get('created_by')); ?></span>
						</span>
					</td>
					<td class="priority-4">
						<span class="created">
							<time datetime="<?php echo $row->get('created'); ?>"><?php echo $row->get('created'); ?></time>
						</span>
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
	<input type="hidden" name="thread" value="<?php echo $this->filters['thread']; ?>" />
	<input type="hidden" name="task" value="thread" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>