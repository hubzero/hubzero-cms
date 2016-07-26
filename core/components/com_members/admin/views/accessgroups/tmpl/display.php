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

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_ACCESSGROUPS'), 'user');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
	Toolbar::divider();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
	Toolbar::divider();
}
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_members');
	Toolbar::divider();
}
Toolbar::help('groups');

// Include the component HTML helpers.
//Html::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'accessgroups') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=accessgroups'); ?>"><?php echo Lang::txt('COM_MEMBERS_ACCESSGROUPS'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'accesslevels') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=accesslevels'); ?>"><?php echo Lang::txt('COM_MEMBERS_ACCESSLEVELS'); ?></a>
		</li>
	</ul>
</nav>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'groups.delete')
		{
			var f = document.adminForm;
			var cb='';
<?php foreach ($this->rows as $i => $row): ?>
	<?php if ($row->maps()->select('group_id', 'count', true)->rows(false)->first()->count > 0): ?>
			cb = f['cb'+<?php echo $i;?>];
			if (cb && cb.checked) {
				if (confirm('<?php echo Lang::txt('COM_MEMBERS_GROUPS_CONFIRM_DELETE'); ?>')) {
					Joomla.submitform(task);
				}
				return;
			}
	<?php endif; ?>
<?php endforeach; ?>
		}
		Joomla.submitform(task);
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_GROUPS_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_IN_GROUPS'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="priority-4">
					<?php echo Lang::txt('JGRID_HEADING_ID'); ?>
				</th>
				<th class="left">
					<?php echo Lang::txt('COM_MEMBERS_HEADING_GROUP_TITLE'); ?>
				</th>
				<th class="priority-3">
					<?php echo Lang::txt('COM_MEMBERS_HEADING_USERS_IN_GROUP'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->rows as $i => $row) :
			$canCreate = User::authorise('core.create', $this->option);
			$canEdit   = User::authorise('core.edit', $this->option);
			// If this group is super admin and this user is not super admin, $canEdit is false
			if (!User::authorise('core.admin') && \JAccess::checkGroup($row->get('id'), 'core.admin'))
			{
				$canEdit = false;
			}
			$canChange = User::authorise('core.edit.state', $this->option);

			$level = Hubzero\Access\Group::all()
				->where('lft', '<', $row->get('lft'))
				->where('rgt', '>', $row->get('rgt'))
				->total();
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($canEdit) : ?>
						<?php echo Html::grid('id', $i, $row->get('id')); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-4">
					<?php echo (int) $row->id; ?>
				</td>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $level) ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('title')); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($row->get('title')); ?>
					<?php endif; ?>
					<?php if (Config::get('debug')) : ?>
						<a class="button fltrt" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=debug&id=' . (int) $row->get('id')); ?>">
							<?php echo Lang::txt('COM_MEMBERS_DEBUG_GROUP');?>
						</a>
					<?php endif; ?>
				</td>
				<td class="center priority-3">
					<?php echo $row->maps()->select('group_id', 'count', true)->rows(false)->first()->count; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo Html::input('token'); ?>
</form>
