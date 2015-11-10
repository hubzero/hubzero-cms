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

JToolBarHelper::title(JText::_('COM_TOOLS') . ': ' . JText::_('COM_TOOLS_SESSIONS'), 'tools.png');
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('sessions');

JHTML::_('behavior.tooltip');
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

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<a class="refresh button" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&username=&appname=&exechost=&start=0'); ?>">
			<span><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></span>
		</a>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_SESSION', 'sessnum', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_OWNER', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_VIEWER', 'viewuser', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_STARTED', 'start', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_LAST_ACCESSED', 'accesstime', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_TOOL', 'appname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_EXEC_HOST', 'exechost', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('COM_TOOLS_COL_STOP'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->rows)
{
	$i = 0;
	foreach ($this->rows as $row)
	{
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->sessnum; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo $this->escape(stripslashes($row->sessname)); ?>::Host: <?php echo $row->exechost; ?>&lt;br /&gt;IP: <?php echo $row->remoteip; ?>">
						<span><?php echo $this->escape($row->sessnum); ?></span>
					</span>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;username=<?php echo $row->username; ?>">
						<span><?php echo $this->escape($row->username); ?></span>
					</a>
				</td>
				<td>
					<span><?php echo $this->escape($row->viewuser); ?></span>
				</td>
				<td>
					<time datetime="<?php echo $this->escape($row->start); ?>">
						<?php echo $this->escape($row->start); ?>
					</time>
				</td>
				<td>
					<time datetime="<?php echo $this->escape($row->accesstime); ?>">
						<?php echo $this->escape($row->accesstime); ?>
					</time>
				</td>
				<td>
					<a class="tool" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;appname=<?php echo $row->appname; ?>">
						<span><?php echo $this->escape($row->appname); ?></span>
					</a>
				</td>
				<td>
					<a class="tool" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;exechost=<?php echo $row->exechost; ?>">
						<span><?php echo $this->escape($row->exechost); ?></span>
					</a>
				</td>
				<td>
					<a class="state trash" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=remove&amp;id[]=<?php echo $row->sessnum; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('COM_TOOLS_TERMINATE'); ?>">
						<span><?php echo JText::_('COM_TOOLS_TERMINATE'); ?></span>
					</a>
				</td>
			</tr>
<?php
		$i++;
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