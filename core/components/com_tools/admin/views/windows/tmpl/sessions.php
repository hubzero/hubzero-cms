<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS'), 'tools.png');
Toolbar::deleteList('terminate');
Toolbar::spacer();
Toolbar::help('sessions');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=sessions'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_appname"><?php echo Lang::txt('COM_TOOLS_APPNAME'); ?>:</label>
		<select name="appname" id="filter_appname" onchange="document.adminForm.submit();">
			<option value=""><?php echo Lang::txt('COM_TOOLS_APPNAME_SELECT'); ?></option>
			<?php

			foreach ($this->apps as $record)
			{
				$html  = ' <option value="' . $record->path . '"';
				if (Request::getVar('appname','') == $record->path)
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
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
