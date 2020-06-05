<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Wiki\Helpers\Permissions::getActions('page');

Toolbar::title(Lang::txt('COM_WIKI'), 'wiki');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
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
	Toolbar::deleteList('COM_WIKI_CONFIRM_DELETE');
}
Toolbar::spacer();
Toolbar::help('pages');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_WIKI_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span7">
				<label for="filter_scope"><?php echo Lang::txt('COM_WIKI_FILTER_SCOPE'); ?>:</label>
				<select name="scope" id="filter_scope" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('COM_WIKI_FILTER_SCOPE_SELECT'); ?></option>
					<?php foreach ($this->scopes as $scope) {
						$val = $scope->get('scope') . ':' . $scope->get('scope_id');
					?>
						<option value="<?php echo $this->escape($val); ?>"<?php if ($val == $this->filters['scope']) { echo ' selected="selected"'; } ?>><?php echo $this->escape($val); ?></option>
					<?php } ?>
				</select>

				<label for="filter_namespace"><?php echo Lang::txt('COM_WIKI_FILTER_NAMESPACE'); ?>:</label>
				<select name="namespace" id="filter_namespace" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('COM_WIKI_FILTER_NAMESPACE_SELECT'); ?></option>
					<?php foreach ($this->namespaces as $nspace) {
						if (!trim($nspace->get('namespace')))
						{
							continue;
						}
						?>
						<option value="<?php echo $nspace->get('namespace'); ?>"<?php if ($this->filters['namespace'] == $nspace->get('namespace')) { echo ' selected="selected"'; } ?>><?php echo $nspace->get('namespace'); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_WIKI_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_WIKI_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_WIKI_COL_MODE'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_WIKI_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_WIKI_COL_LOCKED', 'protected', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_WIKI_COL_SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_WIKI_COL_REVISIONS'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_WIKI_COL_COMMENTS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php
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
				case 2:
					$cls  = 'trash';
					$task = 0;
					$alt  = Lang::txt('COM_WIKI_STATE_TRASHED');
				break;

				case 1:
					$alt  = Lang::txt('JPUBLISHED');
					$task = 0;
					$cls  = 'publish';
				break;

				case 0:
				default:
					$alt  = Lang::txt('JUNPUBLISHED');
					$task = 1;
					$cls  = 'unpublish';
				break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-5">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('title', Lang::txt('COM_WIKI_NONE')))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('title', Lang::txt('COM_WIKI_NONE')))); ?>
						</span>
					<?php } ?>
					<br />
					<span class="smallsub">/wiki/</span> &nbsp;
					<span class="smallsub"><?php echo ($row->get('path') ? $row->get('path') . '/' : '') . $this->escape(stripslashes($row->get('pagename'))); ?></span>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->param('mode')); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=state&id=' . $row->get('id') . '&state=' . $task . '&' . Session::getFormToken() . '=1'); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php if ($row->get('protected')) { ?>
						<span class="access private">
							<span><?php echo Lang::txt('COM_WIKI_STATE_LOCKED'); ?></span>
						</span>
					<?php } else { ?>
						<span class="access public">
							<span><?php echo Lang::txt('COM_WIKI_STATE_OPEN'); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="group">
						<span><?php echo $this->escape($row->get('scope') . ':' . $row->get('scope_id')); ?></span>
					</span>
				</td>
				<td class="priority-2">
					<a class="revisions" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=versions&pageid=' . $row->get('id')); ?>">
						<span><?php echo Lang::txt('COM_WIKI_NUM_REVISIONS', $row->versions->count()); ?></span>
					</a>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit')) { ?>
						<a class="comment" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=comments&page_id=' . $row->get('id')); ?>">
							<?php echo Lang::txt('COM_WIKI_NUM_COMMENTS', $row->comments->count()); ?>
						</a>
					<?php } else { ?>
						<span class="comment">
							<?php echo Lang::txt('COM_WIKI_NUM_COMMENTS', $row->comments->count()); ?>
						</span>
					<?php } ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
