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

$canDo = \Components\Members\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('Members') . ': ' . Lang::txt('Plugins'), 'user.png');
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
}
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<?php echo $this->states; ?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('Go'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'ID', 'p.extension_id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col" class="title">
					<?php echo Html::grid('sort', 'Plugin Name', 'p.name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'Published', 'p.enabled', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'Order', 'p.folder', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
					<?php echo Html::grid('order',  $this->rows); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'Access', 'groupname', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('Manage'); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'File', 'p.element', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php
					// Initiate paging
					$pagination = $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					echo $pagination->render();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$db = App::get('db');
$tbl = new JTableExtension($db);

$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$link = 'index.php?option=com_plugins&task=edit&extension_id=' . $row->id . '&component=' . $row->folder;

	$access    = $row->groupname;
	$published = $row->published;

	$ordering = ($this->filters['sort'] == 'p.folder');

	switch ($row->published)
	{
		case '2':
			$task = 'publish';
			$img = 'disabled.png';
			$alt = Lang::txt('Trashed');
			$cls = 'trashed';
		break;
		case '1':
			$task = 'unpublish';
			$img = 'publish_g.png';
			$alt = Lang::txt('Published');
			$cls = 'publish';
		break;
		case '0':
		default:
			$task = 'publish';
			$img = 'publish_x.png';
			$alt = Lang::txt('Unpublished');
			$cls = 'unpublish';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
					<?php } ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php
						if ($tbl->isCheckedOut(User::get('id'), $row->checked_out) || !$canDo->get('core.edit')) {
							echo $this->escape($row->name);
						} else {
					?>
						<a class="editlinktip hasTip" href="<?php echo Route::url($link); ?>" title="<?php echo Lang::txt( 'Edit Plugin' );?>::<?php echo $row->name; ?>">
							<span><?php echo $this->escape($row->name); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php if ($tbl->isCheckedOut(User::get('id'), $row->checked_out) || !$canDo->get('core.edit.state')) { ?>
						<span class="state <?php echo $cls; ?>">
							<span class="text"><?php echo $alt; ?></span>
						</span>
					<?php } else { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('Set this to %s',$task);?>">
							<span class="text"><?php echo $alt; ?></span>
						</a>
					<?php } ?>
				</td>
				<td class="order">
					<span><?php echo $pagination->orderUpIcon($i, ($row->folder == @$this->rows[$i-1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderup', 'Move Up', $row->ordering); ?></span>
					<span><?php echo $pagination->orderDownIcon($i, $n, ($row->folder == @$this->rows[$i+1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderdown', 'Move Down', $row->ordering); ?></span>
					<?php $disabled = $row->ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td>
					<?php echo $access; ?>
				</td>
				<td>
					<?php if (in_array($row->element, $this->manage)) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=manage&plugin=' . $row->element); ?>">
							<span><?php echo Lang::txt('Manage'); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php echo $this->escape($row->element); ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="sort" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="sort_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>