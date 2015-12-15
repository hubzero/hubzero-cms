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

$this->css();
$this->js();

$canDo = \Components\Publications\Helpers\Permissions::getActions('item');

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER'), 'addedit.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
}
if ($canDo->get('core.edit'))
{
	Toolbar::spacer();
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::spacer();
	Toolbar::deleteList();
}

Html::behavior('tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<?php if ($this->config->get('enabled') == 0) { ?>
	<p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_COMPONENT_DISABLED'); ?></p>
<?php } ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<label for="status"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_STATUS'); ?>:</label>
		<select name="status" id="status">
			<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_ALL_STATUS'); ?></option>
			<option value="3"<?php echo ($this->filters['status'] == 3) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT'); ?></option>
			<option value="5"<?php echo ($this->filters['status'] == 5) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PENDING'); ?></option>
			<option value="0"<?php echo ($this->filters['status'] == 0 && $this->filters['status'] != 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED'); ?></option>
			<option value="10"<?php echo ($this->filters['status'] == 10) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PRESERVING'); ?></option>
			<option value="7"<?php echo ($this->filters['status'] == 7) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_WIP'); ?></option>
			<option value="1"<?php echo ($this->filters['status'] == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED'); ?></option>
			<option value="4"<?php echo ($this->filters['status'] == 4) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_READY'); ?></option>
			<option value="2"<?php echo ($this->filters['status'] == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PUBLICATIONS_VERSION_DELETED'); ?></option>
		</select>

		<label for="category"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CATEGORY'); ?>:</label>
		<?php 
		// Draw category list
		$this->view('_selectcategory')
		     ->set('categories', $this->categories)
		     ->set('value', $this->filters['category'])
		     ->set('name', 'category')
		     ->set('showNone', Lang::txt('COM_PUBLICATIONS_ALL_CATEGORIES'))
		     ->display();
		?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th></th>
				<th class="priority-3"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_TITLE'), 'title', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th class="priority-4"><?php echo Lang::txt('@v.'); ?></th>
				<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_STATUS'); ?></th>
				<th class="priority-2"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_PROJECT'), 'project', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th class="priority-4"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_RELEASES'); ?></th>
				<th class="priority-4" colspan="2"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TYPE_CAT'); ?></th>
				<th class="priority-5"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_LAST_MODIFIED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
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
		$k = 0;
		$filterstring  = $this->filters['sortby'] ? '&sort=' . $this->filters['sortby'] : '';
		$filterstring .= '&status=' . $this->filters['status'];
		$filterstring .= ($this->filters['category']) ? '&category=' . $this->filters['category'] : '';

		for ($i=0, $n=count($this->rows); $i < $n; $i++)
		{
			$row = $this->rows[$i];

			// Build some publishing info
			$info  = Lang::txt('COM_PUBLICATIONS_FIELD_CREATED') . ': ' . $row->created . '<br />';
			$info .= Lang::txt('COM_PUBLICATIONS_FIELD_CREATOR') . ': ' . $this->escape($row->created_by) . '<br />';

			// Get the published status
			$now = Date::toSql();

			// See if it's checked out or not
			$checked = '';
			$checkedInfo = '';
			if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
			{
				$date = Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_LC1'));
				$time = Date::of($row->checked_out_time)->toLocal('H:i');

				$checked  = '<span class="editlinktip hasTip" title="' . Lang::txt('JLIB_HTML_CHECKED_OUT') . '::' . $this->escape($row->checked_out) . '<br />' . $date . '<br />' . $time . '">';
				$checked .= '<span class="checkedout">' . Lang::txt('JLIB_HTML_CHECKED_OUT') . '</span>';
				$checked .= '</span>';

				$info .= ($row->checked_out_time != '0000-00-00 00:00:00')
						? Lang::txt('COM_PUBLICATIONS_FIELD_CHECKED_OUT').': '
						. $date . '<br />'
						: '';
				$info .= ($row->checked_out)
						 ? Lang::txt('COM_PUBLICATIONS_FIELD_CHECKED_OUT_BY') . ': ' . $row->checked_out . '<br />'
						 : '';
				$checkedInfo = ' ['.Lang::txt('COM_PUBLICATIONS_FIELD_CHECKED_OUT').']';

			}
			else
			{
				$checked = Html::grid('id', $i, $row->id, false, 'id');
			}

			// What's the publication status?
			$status = $this->model->getStatusName($row->state);
			$class  = $this->model->getStatusCss($row->state);

			$date   = $row->modified() ? $row->modified('datetime') : $row->created('datetime');
			?>
			<tr class="<?php echo "row$k"; ?> <?php echo $row->isPending() ? 'attention' : ''; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td class="priority-3">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a class="editlinktip hasTip" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id . $filterstring); ?>" title="<?php echo Lang::txt( 'COM_PUBLICATIONS_PUBLISH_INFO' );?>::<?php echo $info; ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</a><?php if ($checkedInfo) { echo $checkedInfo; } ?>
				</td>
				<td class="priority-4">
					<?php echo $row->version_label; ?>
				</td>
				<td>
					<span class="<?php echo $class; ?> hasTip" title="<?php echo $status; ?>">&nbsp;</span>
				</td>
				<td class="priority-2">
					<a href="<?php echo Route::url('index.php?option=com_projects&task=edit&id=' . $row->project_id ); ?>"><?php echo \Hubzero\Utility\String::truncate($row->project_title, 50);  ?></a>
				</td>
				<td class="priority-4">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=versions&id=' . $row->id . $filterstring ); ?>"><?php echo $this->escape($row->versions); ?></a>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->base); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->cat_name); ?>
				</td>
				<td class="priority-5">
					<?php echo $date; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<?php 
	// Draw legend
	$this->view('_statuskey')
	     ->display(); ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
