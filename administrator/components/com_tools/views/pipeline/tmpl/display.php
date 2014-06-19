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
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_TOOLS' ), 'tools.png');
JToolBarHelper::preferences('com_tools', '550');
JToolBarHelper::spacer();
JToolBarHelper::help('tools');

$this->css();

JHTML::_('behavior.tooltip');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_TOOLS_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo JText::_('COM_TOOLS_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_NAME', 'toolname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_REGISTERED', 'registered', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_STATECHANGED', 'state_changed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_VERSIONS', 'versions', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
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

	switch ($row['state'])
	{
		case 0: $state = 'unpublished'; break;
		case 1: $state = 'registered';  break;
		case 2: $state = 'created';     break;
		case 3: $state = 'uploaded';    break;
		case 4: $state = 'installed';   break;
		case 5: $state = 'updated';     break;
		case 6: $state = 'approved';    break;
		case 7: $state = 'published';   break;
		case 8: $state = 'retired';     break;
		case 9: $state = 'abandoned';   break;
		default: $state = 'unknown';    break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="radio" name="id" id="cb<?php echo $i; ?>" value="<?php echo $row['id'] ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row['id']); ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row['id']; ?>">
						<?php echo $this->escape(stripslashes($row['toolname'])); ?>
					</a>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row['id']; ?>">
						<?php echo $this->escape(stripslashes($row['title'])); ?>
					</a>
				</td>
				<td>
					<span class="state <?php echo $state; ?> hasTip" title="<?php echo $this->escape(JText::_(strtoupper($this->option) . '_' . strtoupper($state))); ?>">
						<span><?php echo $this->escape(JText::_(strtoupper($this->option) . '_' . strtoupper($state))); ?></span>
					</span>
				</td>
				<td>
					<time><?php echo $this->escape($row['registered']); ?></time>
				</td>
				<td>
					<time><?php echo $this->escape($row['state_changed']); ?></time>
				</td>
				<td>
					<a class="glyph menulist" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;controller=versions&amp;id=<?php echo $row['id'];?>">
						<span><?php echo $this->escape($row['versions']); ?></span>
					</a>
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
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>