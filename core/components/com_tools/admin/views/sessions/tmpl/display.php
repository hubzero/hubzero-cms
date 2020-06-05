<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_SESSIONS'), 'tools');
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('sessions');

Html::behavior('tooltip');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_appname"><?php echo Lang::txt('COM_TOOLS_APPNAME'); ?>:</label>
		<select name="appname" id="filter_appname" class="filter filter-submit">
			<option value=""><?php echo Lang::txt('COM_TOOLS_APPNAME_SELECT'); ?></option>
			<?php
				foreach ($this->appnames as $record)
				{
					$html  = ' <option value="' . $record->appname . '"';
					if ($this->filters['appname'] == $record->appname)
					{
						$html .= ' selected="selected"';
					}
					$html .= '>' . $this->escape(stripslashes($record->appname)) . '</option>' . "\n";

					echo $html;
				}
			?>
		</select>

		<label for="filter_exechost"><?php echo Lang::txt('COM_TOOLS_EXECHOST'); ?>:</label>
		<select name="exechost" id="filter_exechost" class="filter filter-submit">
			<option value=""><?php echo Lang::txt('COM_TOOLS_EXECHOST_SELECT'); ?></option>
			<?php
				foreach ($this->exechosts as $record)
				{
					$html  = ' <option value="' . $record->exechost . '"';
					if ($this->filters['exechost'] == $record->exechost)
					{
						$html .= ' selected="selected"';
					}
					$html .= '>' . $this->escape(stripslashes($record->exechost)) . '</option>' . "\n";

					echo $html;
				}
			?>
		</select>

		<label for="filter_username"><?php echo Lang::txt('COM_TOOLS_USERNAME'); ?>:</label>
		<select name="username" id="filter_username" class="filter filter-submit">
			<option value=""><?php echo Lang::txt('COM_TOOLS_USERNAME_SELECT'); ?></option>
			<?php
				foreach ($this->usernames as $record)
				{
					$html  = ' <option value="' . $record->viewuser . '"';
					if ($this->filters['username'] == $record->viewuser)
					{
						$html .= ' selected="selected"';
					}
					$html .= '>' . $this->escape(stripslashes($record->viewuser)) . '</option>' . "\n";

					echo $html;
				}
			?>
		</select>

		<a class="refresh button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&username=&appname=&exechost=&start=0'); ?>">
			<span><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></span>
		</a>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_SESSION', 'sessnum', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TOOLS_COL_OWNER', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TOOLS_COL_VIEWER', 'viewuser', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TOOLS_COL_STARTED', 'start', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_LAST_ACCESSED', 'accesstime', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_TOOL', 'appname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TOOLS_COL_EXEC_HOST', 'exechost', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_STOP'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php
					// Initiate paging
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		if ($this->rows)
		{
			$i = 0;
			foreach ($this->rows as $row)
			{
				?>
				<tr>
					<td>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->sessnum; ?>" class="checkbox-toggle" />
					</td>
					<td>
						<span class="editlinktip hasTip" title="<?php echo $this->escape(stripslashes($row->sessname)); ?>::Host: <?php echo $row->exechost; ?>&lt;br /&gt;IP: <?php echo $row->remoteip; ?>">
							<span><?php echo $this->escape($row->sessnum); ?></span>
						</span>
					</td>
					<td class="priority-2">
						<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&username=' . $row->username); ?>">
							<span><?php echo $this->escape($row->username); ?></span>
						</a>
					</td>
					<td class="priority-2">
						<span><?php echo $this->escape($row->viewuser); ?></span>
					</td>
					<td class="priority-4">
						<time datetime="<?php echo $this->escape($row->start); ?>">
							<?php echo $this->escape($row->start); ?>
						</time>
					</td>
					<td class="priority-3">
						<time datetime="<?php echo $this->escape($row->accesstime); ?>">
							<?php echo $this->escape($row->accesstime); ?>
						</time>
					</td>
					<td class="priority-3">
						<a class="tool" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&appname=' . $row->appname); ?>">
							<span><?php echo $this->escape($row->appname); ?></span>
						</a>
					</td>
					<td class="priority-4">
						<a class="tool" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&exechost=' . $row->exechost); ?>">
							<span><?php echo $this->escape($row->exechost); ?></span>
						</a>
					</td>
					<td>
						<a class="state trash" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=remove&id=' . $row->sessnum . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_TOOLS_TERMINATE'); ?>">
							<span><?php echo Lang::txt('COM_TOOLS_TERMINATE'); ?></span>
						</a>
					</td>
				</tr>
				<?php
				$i++;
			}
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