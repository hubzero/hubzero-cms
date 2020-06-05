<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Blog\Admin\Helpers\Permissions::getActions('entry');

Toolbar::title(Lang::txt('COM_BLOG_TITLE'), 'blog');
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
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_BLOG_CONFIRM_DELETE', 'delete');
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
Toolbar::help('entries');

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span4">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_BLOG_FILTER_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_BLOG_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span8 align-right">
				<label for="filter-scope"><?php echo Lang::txt('COM_BLOG_FIELD_SCOPE'); ?>:</label>
				<?php echo Components\Blog\Admin\Helpers\Html::scopes($this->filters['scope'], 'scope', 'filter-scope', 'class="filter filter-submit"'); ?>

				<label for="filter-state"><?php echo Lang::txt('COM_BLOG_FIELD_STATE'); ?>:</label>
				<select name="state" id="filter-state" class="filter filter-submit">
					<option value="-1"<?php if ($this->filters['state'] == '-1') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_BLOG_ALL_STATES'); ?></option>
					<option value="0"<?php if ($this->filters['state'] === 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->filters['state'] === 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->filters['state'] === 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>

				<label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
				<select name="access" id="filter-access" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_BLOG_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_BLOG_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_BLOG_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-1"><?php echo Html::grid('sort', 'COM_BLOG_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_BLOG_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2" colspan="2"><?php echo Lang::txt('COM_BLOG_COL_COMMENTS'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_BLOG_COL_SCOPE', 'scope_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php
				// Initiate paging
				echo $this->rows->pagination
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;

		$now    = Date::of('now');
		$db     = App::get('db');

		$nullDate = $db->getNullDate();

		foreach ($this->rows as $row)
		{
			$publish_up   = Date::of($row->get('publish_up'));
			$publish_down = Date::of($row->get('publish_down'));

			$alt  = Lang::txt('JUNPUBLISHED');
			$cls  = 'unpublish';
			$task = 'publish';

			if ($now->toUnix() <= $publish_up->toUnix() && $row->get('state') == 1)
			{
				$alt  = Lang::txt('JPUBLISHED');
				$cls  = 'publish';
				$task = 'unpublish';
			}
			else if (($now->toUnix() <= $publish_down->toUnix() || !$row->get('publish_down') || $row->get('publish_down') == $nullDate) && $row->get('state') == 1)
			{
				$alt  = Lang::txt('JPUBLISHED');
				$cls  = 'publish';
				$task = 'unpublish';
			}
			else if ($now->toUnix() > $publish_down->toUnix() && $row->get('state') == 1)
			{
				$alt  = Lang::txt('JLIB_HTML_PUBLISHED_EXPIRED_ITEM');
				$cls  = 'publish';
				$task = 'unpublish';
			}
			else if ($row->get('state') == 0)
			{
				$alt  = Lang::txt('JUNPUBLISHED');
				$task = 'publish';
				$cls  = 'unpublish';
			}
			else if ($row->get('state') == -1)
			{
				$alt  = Lang::txt('JTRASHED');
				$task = 'publish';
				$cls  = 'trash';
			}
			else if ($row->get('state') == 2)
			{
				$alt  = Lang::txt('JTRASHED');
				$task = 'publish';
				$cls  = 'trash';
			}

			$times = '';
			if ($row->get('publish_up'))
			{
				if (!$row->get('publish_up') || $row->get('publish_up') == $nullDate)
				{
					$times .= Lang::txt('COM_BLOG_START') . ': ' . Lang::txt('COM_BLOG_ALWAYS');
				}
				else
				{
					$times .= Lang::txt('COM_BLOG_START') . ': ' . $publish_up->toSql();
				}
			}
			if ($row->get('publish_down'))
			{
				if (!$row->get('publish_down') || $row->get('publish_down') == $nullDate)
				{
					$times .= '<br />' . Lang::txt('COM_BLOG_FINISH') . ': ' . Lang::txt('COM_BLOG_NO_EXPIRY');
				}
				else
				{
					$times .= '<br />' . Lang::txt('COM_BLOG_FINISH') . ': ' . $publish_down->toSql();
				}
			}

			if ($row->get('allow_comments') == 0)
			{
				$calt = Lang::txt('JOFF');
				$cls2 = 'off';
				$state = 1;
			}
			else
			{
				$calt = Lang::txt('JON');
				$cls2 = 'on';
				$state = 0;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id') ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php
					$name = stripslashes($row->creator->get('name'));
					$name = $name ? $name : Lang::txt('COM_BLOG_UNKNOWN') . ' (' . $row->get('created_by') . ')';
					echo $this->escape($name); ?>
				</td>
				<td class="priority-1">
					<span class="editlinktip hasTip" title="<?php echo Lang::txt('COM_BLOG_PUBLISH_INFO');?>::<?php echo $times; ?>">
						<?php if ($canDo->get('core.edit.state')) { ?>
							<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
								<span><?php echo $alt; ?></span>
							</a>
						<?php } else { ?>
							<span class="state <?php echo $cls; ?>">
								<span><?php echo $alt; ?></span>
							</span>
						<?php } ?>
					</span>
				</td>
				<td class="priority-5">
					<time datetime="<?php echo $row->get('created'); ?>">
						<?php echo $row->published('date'); ?>
					</time>
				</td>
				<td class="priority-2">
					<a class="state <?php echo $cls2; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=setcomments&state=' . $state . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $calt; ?></span>
					</a>
				</td>
				<td class="priority-2">
					<?php if ($canDo->get('core.edit')) { ?>
						<a class="comment" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=comments&entry_id=' . $row->get('id')); ?>">
							<?php echo Lang::txt('COM_BLOG_COMMENTS', $row->comments()->total()); ?>
						</a>
					<?php } else { ?>
						<span class="comment">
							<?php echo Lang::txt('COM_BLOG_COMMENTS', $row->comments()->total()); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<span>
						<?php echo $this->escape($row->get('scope') . ' (' . $row->get('scope_id') . ')'); ?>
					</span>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
