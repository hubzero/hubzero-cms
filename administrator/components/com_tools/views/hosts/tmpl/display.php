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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('COM_TOOLS') . ': ' . JText::_('COM_TOOLS_HOSTS'), 'tools.png');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('hosts');

$this->css();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_NAME', 'hostname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_PROVISIONS', 'provisions', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_STATUS', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_USES', 'uses', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_ZONE', 'zone_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('COM_TOOLS_COL_BROKEN_CONTAINERS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->rows)
{
	$i = 0;
	foreach ($this->rows as $row)
	{
		$list = array();
		for ($k=0; $k<count($this->hosttypes); $k++)
		{
			$r = $this->hosttypes[$k];
			$list[$r->name] = (int)$r->value & (int)$row->provisions;
		}
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->hostname; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;hostname=<?php echo $row->hostname; ?>">
						<span><?php echo $this->escape($row->hostname); ?></span>
					</a>
				</td>
				<td>
				<?php
					foreach ($list as $key => $value)
					{
						if ($value != '0')
						{
							echo '<strong>';
						}
						?>
					<a class="<?php echo ($value != '0') ? 'active' : 'inactive'; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=toggle&amp;hostname=<?php echo $row->hostname; ?>&amp;item=<?php echo $key; ?>">
						<span><?php echo $this->escape($key); ?></span>
					</a>
						<?php
						if ($value != '0')
						{
							echo '</strong>';
						}
						echo '<br />';
					}
				?>
				</td>
				<td>
					<a class="state <?php echo ($row->status == 'up') ? 'publish' : 'unpublish'; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=status&amp;hostname=<?php echo $row->hostname; ?>">
						<span><?php echo $this->escape($row->status); ?></span>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->uses); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->zone)); ?>
				</td>
				<td>
					<?php
						$db = MwUtils::getMWDBO();
						$db->setQuery("SELECT count(*) FROM `display` WHERE `status`='broken' AND `hostname`=" . $db->quote($row->hostname));
						echo $db->loadResult();
					?>
				</td>
			</tr>
<?php
	}
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
