<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS'), 'tools');
Toolbar::deleteList('terminate');
Toolbar::spacer();
Toolbar::help('sessions');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=sessions'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_appname"><?php echo Lang::txt('COM_TOOLS_APPNAME'); ?>:</label>
		<select name="appname" id="filter_appname" class="filter filter-submit">
			<option value=""><?php echo Lang::txt('COM_TOOLS_APPNAME_SELECT'); ?></option>
			<?php

			foreach ($this->apps as $record)
			{
				$html  = ' <option value="' . $record->path . '"';
				if (Request::getString('appname', '') == $record->path)
				{
					$html .= ' selected="selected"';
				}
				$html .= '>' . $this->escape(stripslashes($record->title)) .'</option>' . "\n";

				echo $html;
			}
			?>
		</select>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_SESSION', 'sessionid', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TOOLS_COL_OPAQUE_DATA', 'url', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_STATUS', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_AVAILABILITY', 'availability', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($this->sessions)
		{
			$i = 0;
			foreach ($this->sessions as $s)
			{
				?>
				<tr>

					<td>
						<span><?php echo $this->escape($s['sessionid']); ?></span>
					</td>
					<td class="priority-2">
						<span><?php echo $this->escape($s['opaquedata']); ?></span>
					</td>
					<td class="priority-3">
						<span><?php echo $this->escape($s['status']); ?></span>
					</td>
					<td class="priority-3">
						<span><?php //echo $this->escape($s->get('availability')); ?></span>
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
	<input type="hidden" name="task" value="sessions" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
