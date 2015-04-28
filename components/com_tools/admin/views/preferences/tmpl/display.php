<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Menu
Toolbar::title(Lang::txt('COM_TOOLS_USER_PREFS'), 'user.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::custom('restoreDefault', 'restore', 'restore', 'COM_TOOLS_USER_PREFS_DEFAULT');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-40 fltlft">
			<label for="filter_search_field"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_SEARCH'); ?></label>
			<select name="search_field" id="filter_search_field">
				<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USERNAME'); ?></option>
				<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_USER_PREFS_NAME'); ?></option>
			</select>

			<label for="filter_search"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_SEARCH_FOR'); ?></label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_GO'); ?>" />
		</div>
		<div class="col width-60 fltrt">
			<select name="class_alias" id="filter_class_alias" onchange="document.adminForm.submit( );">
				<option value=""<?php if ($this->filters['class_alias'] == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_FILTER_SESSION_CLASS'); ?></option>
				<?php foreach ($this->classes as $class) : ?>
					<option value="<?php echo $class->alias; ?>"<?php if ($this->filters['class_alias'] == $class->alias) { echo ' selected="selected"'; } ?>><?php echo $this->escape($class->alias); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="clr"></div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo $this->grid('sort', 'COM_TOOLS_USER_PREFS_USER_ID', 'user_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo $this->grid('sort', 'COM_TOOLS_USER_PREFS_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo $this->grid('sort', 'COM_TOOLS_USER_PREFS_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo $this->grid('sort', 'COM_TOOLS_USER_PREFS_CLASS', 'class_alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo $this->grid('sort', 'COM_TOOLS_USER_PREFS_JOBS', 'jobs', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php
					// Initiate paging
					jimport('joomla.html.pagination');
					$pageNav = new JPagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					echo $pageNav->getListFooter();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k quota-row"; ?>">
				<td>
					<input class="row-id" type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape($row->user_id); ?>
					</a>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape($row->username); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->name); ?>
				</td>
				<td>
					<?php echo ($row->class_alias) ? $this->escape($row->class_alias) : 'custom'; ?>
				</td>
				<td>
					<?php echo $this->escape($row->jobs); ?>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>