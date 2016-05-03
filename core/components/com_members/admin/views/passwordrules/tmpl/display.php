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

$canDo = Components\Members\Helpers\Admin::getActions('component');

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_PASSWORD_RULES'), 'user');
if ($canDo->get('core.edit'))
{
	Toolbar::custom('restore_default_content', 'refresh', 'refresh', 'COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS', false, false);
	Toolbar::spacer();
	Toolbar::addNew();
	Toolbar::editList();
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

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if (pressbutton == 'restore_default_content') {
		var mes = confirm('<?php echo Lang::txt('COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS_CONFIRM'); ?>');
		if (!mes) {
			return false;
		}
		submitform( pressbutton );
	}

	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->count(); ?>);" /></th>
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
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape(stripslashes($row->get('rule'))); ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape($row->description); ?>
					</a>
				</td>
				<td class="order">
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
					<input type="text" name="order[]" size="5" value="<?php echo $row->get('ordering'); ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
				</td>
				<td class="priority-2">
					<a class="state <?php echo ($row->get('enabled') ? 'yes': 'no'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=toggle_enabled&id=' . $row->get('id')); ?>">
						<span><?php echo Lang::txt(($row->get('enabled') ? 'JYES': 'JNO')); ?></span>
					</a>
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
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>