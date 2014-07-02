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

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATIONS') . ': [' . JText::_('COM_PUBLICATIONS_LICENSES') . ']', 'addedit.png');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::publishList('makedefault', JText::_('COM_PUBLICATIONS_MAKE_DEFAULT'));
JToolBarHelper::publishList('changestatus', JText::_('COM_PUBLICATIONS_PUBLISH_UNPUBLISH'));

?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('Search'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_PUBLICATIONS_SEARCH'); ?>" />

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('COM_PUBLICATIONS_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_NAME'), 'name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_TITLE'), 'title', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_STATUS'), 'active', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_DEFAULT'); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_ORDER'), 'ordering', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];
	$class = $row->active == 1 ? 'on' : 'off';
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<span><?php echo $this->escape($row->name); ?></span>
					</a>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<span><?php echo $this->escape($row->title); ?></span>
					</a>
				</td>
				<td class="centeralign">
					<span class="state <?php echo $class; ?>">
						<span><?php echo JText::_($class); ?></span>
					</span>
				</td>
				<td class="centeralign">
					<?php if ($row->main == 1) { ?>
					<span class="state yes">
						<span><?php echo JText::_('JYES'); ?></span>
					</span>
					<?php } ?>
				</td>
				<td class="order">
					<span>
						<?php echo $this->pageNav->orderUpIcon( $i, (isset($this->rows[$i-1]->ordering)) ); ?>
					</span>
					<span>
						<?php echo $this->pageNav->orderDownIcon( $i, $n, (isset($this->rows[$i+1]->ordering))); ?>
					</span>
					<input type="hidden" name="order[]" value="<?php echo $row->ordering; ?>" />
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