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
JToolBarHelper::title(JText::_('COM_MEMBERS_QUOTAS'), 'user.png');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::custom('restoreDefault', 'restore', 'restore', 'Default');

$this->css('quotas.css');
?>

<script type="text/javascript">
	jQuery(document).ready(function ( $ ) {
		setTimeout(doWork, 10);

		function doWork() {
			var rows = $('.quota-row');

			rows.each(function ( i, el ) {
				var id = $(el).find('.row-id').val();
				var usage = $(el).find('.usage-outer');

				$.ajax({
					url      : 'index.php?option=com_members&controller=quotas&task=getQuotaUsage',
					dataType : 'JSON',
					type     : 'GET',
					data     : {"id":id},
					success  : function ( data, textStatus, jqXHR ) {
						if (data.percent > 100) {
							data.percent = 100;
							usage.find('.usage-inner').addClass('max');
						}
						usage.prev('.usage-calculating').hide();
						usage.fadeIn();
						usage.find('.usage-inner').css('width', data.percent+"%");
					},
					error : function ( ) {
						usage.prev('.usage-calculating').hide();
						usage.next('.usage-unavailable').show();
					}
				});
			});
		};
	});
</script>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-40 fltlft">
			<label for="filter_search_field"><?php echo JText::_('SEARCH'); ?></label>
			<select name="search_field" id="filter_search_field">
				<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_QUOTA_USERNAME'); ?></option>
				<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_QUOTA_NAME'); ?></option>
			</select>

			<label for="filter_search"><?php echo JText::_('for'); ?></label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" placeholder="<?php echo JText::_('Search...'); ?>" />

			<input type="submit" value="<?php echo JText::_('GO'); ?>" />
		</div>
		<div class="col width-60 fltrt">
			<select name="class_alias" id="filter_class_alias" onchange="document.adminForm.submit( );">
				<option value=""<?php if ($this->filters['class_alias'] == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('- Quota Class -'); ?></option>
				<?php foreach ($this->classes as $class) : ?>
					<option value="<?php echo $class->alias; ?>"<?php if ($this->filters['class_alias'] == $class->alias) { echo ' selected="selected"'; } ?>><?php echo JText::_($class->alias); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="clr"></div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_MEMBERS_QUOTA_USER_ID'), 'user_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_MEMBERS_QUOTA_USERNAME'), 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_MEMBERS_QUOTA_NAME'), 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_MEMBERS_QUOTA_CLASS'), 'class_alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_DISK_USAGE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pageNav->getListFooter(); ?>
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
					<input class="row-id" type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
						<?php echo $this->escape($row->user_id); ?>
					</a>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
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
					<div class="usage-calculating">[calculating...]</div>
					<div class="usage-outer">
						<div class="usage-inner"></div>
					</div>
					<div class="usage-unavailable">unavailable</div>
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