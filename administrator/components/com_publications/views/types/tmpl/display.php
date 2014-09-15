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

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATIONS') . ': [' . JText::_('COM_PUBLICATIONS_MASTER_TYPES') . ']', 'addedit.png');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::spacer();
JToolBarHelper::deleteList();

// Use new curation flow?
$useBlocks  = $this->config->get('curation', 0);

?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_NAME'), 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_ALIAS'); ?></th>
				<?php if (!$useBlocks) { ?>
				<th><?php echo JText::_('COM_PUBLICATIONS_STATUS_ACTIVE'); ?></th>
				<?php } ?>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'), 'contributable', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<?php if (!$useBlocks) { ?>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_SUPPORTING'), 'supporting', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<?php } ?>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_ORDER'), 'ordering', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];
	$sClass  = $row->supporting == 1 ? 'on' : 'off';
	$cClass  = $row->contributable == 1 ? 'on' : 'off';

	// Determine whether master type is supported in current version of hub code
	$aClass  = 'off';
	$active  = 'off';

	// If we got a plugin - type is supported
	if (JPluginHelper::isEnabled('projects', $row->alias))
	{
		$aClass  = 'on';
		$active  = 'on';
	}
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
						<span><?php echo $this->escape($row->type); ?></span>
					</a>
				</td>
				<td><span class="block faded"><?php echo $this->escape($row->alias); ?></span></td>
				<?php if (!$useBlocks) { ?>
				<td class="centeralign narrow">
					<span class="state <?php echo $aClass; ?>">
						<span><?php echo $active; ?></span>
					</span>
				</td>
				<?php } ?>
				<td class="centeralign narrow">
					<span class="state <?php echo $cClass; ?>">
						<span><?php echo JText::_($cClass); ?></span>
					</span>
				</td>
				<?php if (!$useBlocks) { ?>
				<td class="centeralign narrow">
					<span class="state <?php echo $sClass; ?>">
						<span><?php echo JText::_($sClass); ?></span>
					</span>
				</td>
				<?php } ?>
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