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

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATIONS') . ': [' . JText::_('COM_PUBLICATIONS_CATEGORIES') . ']', 'addedit.png');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::publishList('changestatus', JText::_('COM_PUBLICATIONS_CHANGE_STATUS'));

?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_NAME'), 'name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'), 'contributable', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_STATUS'), 'state', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="narrow">
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="narrow">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<span><?php echo $this->escape($row->name); ?></span>
					</a>
					<span class="block faded">
						<?php echo JText::_('COM_PUBLICATIONS_FIELD_ALIAS') . ': ' .$this->escape($row->alias); ?> |
						<?php echo JText::_('COM_PUBLICATIONS_FIELD_URL_ALIAS') . ': ' .$this->escape($row->url_alias); ?> |
						<?php echo JText::_('COM_PUBLICATIONS_FIELD_DC_TYPE') . ': ' .$this->escape($row->dc_type); ?>
					</span>
				</td>
				<td class="centeralign narrow">
					<span class="state <?php echo ($row->contributable == 1 ? 'yes' : 'no'); ?>">
						<span><?php echo ($row->contributable == 1 ? JText::_('JYES') : JText::_('JNO')); ?></span>
					</span>
				</td>
				<td class="centeralign narrow">
					<span class="state <?php echo ($row->state == 1 ? 'on' : 'off'); ?>">
						<span><?php echo ($row->state == 1 ? JText::_('COM_PUBLICATIONS_ON') : JText::_('COM_PUBLICATIONS_OFF')); ?></span>
					</span>
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