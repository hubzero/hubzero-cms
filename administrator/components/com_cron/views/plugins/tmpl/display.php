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

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Cron') . '</a>: <small><small>[' . JText::_('Plugins') . ']</small></small>', 'cron.png');
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::editListX();
?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter">
		<?php echo $this->states; ?>
	
		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>
	
	<table class="adminlist" summary="<?php echo JText::_('A list of resource plugins'); ?>">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'ID', 'p.id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col" class="title">
					<?php echo JHTML::_('grid.sort', 'Plugin Name', 'p.name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'Published', 'p.published', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'Order', 'p.folder', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
					<?php echo JHTML::_('grid.order',  $this->rows); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'Access', 'groupname', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo JText::_('Manage'); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'File', 'p.element', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$link = JRoute::_( 'index.php?option='.$this->option.'&controller='.$this->controller.'&client='. $this->client .'&task=edit&cid[]='. $row->id );

	$access 	= JHTML::_('grid.access', $row, $i);
	$checked 	= JHTML::_('grid.checkedout', $row, $i);
	$published 	= JHTML::_('grid.published', $row, $i);

	$ordering = ($this->filters['sort'] == 'p.folder');
	
	switch ($row->published) 
	{
		case '2':
			$task = 'publish';
			$img = 'disabled.png';
			$alt = JText::_('Trashed');
			$cls = 'trashed';
		break;
		case '1':
			$task = 'unpublish';
			$img = 'publish_g.png';
			$alt = JText::_('Published');
			$cls = 'publish';
		break;
		case '0':
		default:
			$task = 'publish';
			$img = 'publish_x.png';
			$alt = JText::_('Unpublished');
			$cls = 'unpublish';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
<?php
					if (JTable::isCheckedOut($this->user->get('id'), $row->checked_out)) {
						echo $this->escape($row->name);
					} else {
?>
					<a class="editlinktip hasTip" href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit Plugin' );?>::<?php echo $row->name; ?>">
						<span><?php echo $this->escape($row->name); ?></span>
					</a>
<?php } ?>
				</td>
				<td>
<?php if (JTable::isCheckedOut($this->user->get('id'), $row->checked_out)) { ?>
					<span class="state <?php echo $cls; ?>">
<?php 		if (version_compare(JVERSION, '1.6', 'lt')) { ?>
						<span><img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></span>
<?php 		} else { ?>
						<span class="text"><?php echo $alt; ?></span>
<?php 		} ?>
					</span>
<?php } else { ?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('Set this to %s',$task);?>">
<?php 		if (version_compare(JVERSION, '1.6', 'lt')) { ?>
						<span><img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></span>
<?php 		} else { ?>
						<span class="text"><?php echo $alt; ?></span>
<?php 		} ?>
					</a>
<?php } ?>
				</td>
				<td class="order">
					<?php echo $this->pagination->orderUpIcon( $i, ($row->folder == @$this->rows[$i-1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderup', 'Move Up', $ordering ); ?>
					<?php echo $this->pagination->orderDownIcon( $i, $n, ($row->folder == @$this->rows[$i+1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderdown', 'Move Down', $ordering ); ?>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $access; ?>
				</td>
				<td nowrap="nowrap">
<?php if (in_array($row->element, $this->manage)) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=manage&amp;plugin=<?php echo $row->element; ?>">
						<span><?php echo JText::_('Manage'); ?></span>
					</a>
<?php } ?>
				</td>
				<td>
					<?php echo $this->escape($row->element); ?>
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