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

Toolbar::title( Lang::txt( 'Projects' ), 'user.png' );

// Only display if enabled
if ($this->config->get('edit_settings') == 'custom')
{
	Toolbar::custom('customizeDescription', 'menus', 'menus', 'COM_PROJECTS_CUSTOM_DESCRIPTION', false);
}

Toolbar::spacer();
Toolbar::preferences('com_projects', '550');
Toolbar::editList();


Html::behavior('tooltip');

$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
$now = Date::toSql();

$base = rtrim(Request::base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
}

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

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<label for="filter_search"><?php echo Lang::txt('COM_PROJECTS_SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_PROJECTS_SEARCH'); ?>" />

		<label for="quota"><?php echo Lang::txt('COM_PROJECTS_FILTER_QUOTA'); ?>:</label>
		<select name="quota" id="quota">
			<option value="all"<?php echo ($this->filters['quota'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_QUOTA_ALL'); ?></option>
			<option value="regular"<?php echo ($this->filters['quota'] == 'regular') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_QUOTA_REGULAR'); ?></option>
			<option value="premium"<?php echo ($this->filters['quota'] == 'premium') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_PROJECTS_QUOTA_PREMIUM'); ?></option>
		</select>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_PROJECTS_GO'); ?>" />
	</fieldset>

	<table class="adminlist" id="projects-admin">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th class="priority-5" scope="col"><?php echo $this->grid('sort', 'ID', 'id', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th class="priority-5" scope="col"> </th>
				<th scope="col"><?php echo $this->grid('sort', 'Title', 'title', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th class="priority-3" scope="col" colspan="2"><?php echo $this->grid('sort', 'Owner', 'owner', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'Status', 'status', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th class="priority-4" scope="col"><?php echo $this->grid('sort', 'Privacy', 'privacy', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th class="priority-4"><?php echo Lang::txt('COM_PROJECTS_QUOTA'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php 
				// Initiate paging
				echo $this->pagination(
					$this->total,
					$this->start,
					$this->limit
				);
			?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;
			$filterstring  = ($this->filters['sortby'])   ? '&amp;sort='.$this->filters['sortby']     : '';

			if ($this->rows)
			{
				for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
				{
					$row = $this->rows[$i];

					if ($row->owned_by_group && !$row->groupcn)
					{
						$row->groupname = '<span class="italic pale">' . Lang::txt('COM_PROJECTS_INFO_DELETED_GROUP') . '</span>';
					}
					$owner = ($row->owned_by_group) ? $row->groupname . ' <span class="block  prominent">' . $row->groupcn . '</span>' : $row->authorname;
					$ownerclass = ($row->owned_by_group) ? '<span class="i_group">&nbsp;</span>' : '<span class="i_user">&nbsp;</span>';

					// Determine status
					$status = '';
					if ($row->state == 1 && $row->setup_stage >= $setup_complete)
					{
						$status = '<span class="active">' . Lang::txt('Active') . '</span> ' . Lang::txt('since') . ' ' . Date::of($row->created)->toLocal('M d, Y');
					}
					else if ($row->state == 2)
					{
						$status  = '<span class="deleted">' . Lang::txt('Deleted') . '</span> ';
					}
					else if ($row->setup_stage < $setup_complete)
					{
						$status = '<span class="setup">' . Lang::txt('Setup') . '</span> ' . Lang::txt('in progress');
					}
					else if ($row->state == 0)
					{
						$status = '<span class="faded italic">' . Lang::txt('Inactive/Suspended') . '</span> ';
					}
					else if ($row->state == 5)
					{
						$status = '<span class="inactive">' . Lang::txt('Pending approval') . '</span> ';
					}

					$cloud = new \Components\Projects\Models\Tags($row->id);
					$tags  = $cloud->render('cloud');

					$params = new \Hubzero\Config\Registry( $row->params );
					$quota  = $params->get('quota', $this->defaultQuota);
					$quota  = \Components\Projects\Helpers\Html::convertSize($quota, 'b', 'GB', 2);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td><?php echo Html::grid('id', $i, $row->id, false, 'id' ); ?></td>
					<td class="priority-5"><?php echo $row->id; ?></td>
					<td class="priority-5"><?php echo '<img src="' . rtrim($base, DS) . DS . 'projects' . DS . $row->alias . '/media' . '" width="30" height="30" alt="' . $this->escape($row->alias) . '" />'; ?></td>
					<td>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $row->id . $filterstring); ?>"><?php echo stripslashes($row->title); ?></a><br />
						<strong><?php echo stripslashes($row->alias); ?></strong>
						<?php if ($tags) { ?>
							<span class="project-tags block">
								<?php echo $tags; ?>
							</span>
						<?php } ?>
					</td>
					<td class="priority-3"><?php echo $ownerclass; ?></td>
					<td class="priority-3"><?php echo $owner; ?></td>
					<td><?php echo $status; ?></td>
					<td class="priority-4"><?php echo ($row->private == 1) ? '<span class="private">' . Lang::txt('COM_PROJECTS_FLAG_PRIVATE') . '</span>' : '<span class="public">' . Lang::txt('COM_PROJECTS_FLAG_PUBLIC') . '</span>'; ?></td>
					<td class="priority-4"><?php echo $quota . 'GB'; ?></td>
				</tr>
				<?php
					$k = 1 - $k;
				}
			} else { ?>
				<tr><td colspan="9"><?php echo Lang::txt('COM_PROJECTS_NO_RESULTS'); ?></td></tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
	<?php echo Html::input('token'); ?>
</form>
