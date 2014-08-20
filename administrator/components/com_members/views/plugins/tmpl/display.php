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

$canDo = MembersHelper::getActions('component');

JToolBarHelper::title(JText::_('Members') . ': ' . JText::_('Plugins'), 'user.png');
if ($canDo->get('core.edit.state'))
{
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	//JToolBarHelper::spacer();
}
/*if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editListX();
}*/
?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<?php echo $this->states; ?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'ID', 'p.extension_id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
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
$db = JFactory::getDBO();
$tbl = new JTableExtension($db);

$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	//$link = JRoute::_( 'index.php?option='.$this->option.'&controller='.$this->controller.'&client='. $this->client .'&task=edit&cid[]='. $row->id );
	$link = 'index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id=' . $row->id . '&amp;component=' . $row->folder;

	$access 	= JHTML::_('grid.access', $row, $i);
	//$checked 	= JHTML::_('grid.checkedout', $row, $i);
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
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
					<?php } ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php
						if ($tbl->isCheckedOut($this->user->get('id'), $row->checked_out) || !$canDo->get('core.edit')) {
							echo $this->escape($row->name);
						} else {
					?>
						<a class="editlinktip hasTip" href="<?php echo $link; ?>" title="<?php echo JText::_( 'Edit Plugin' );?>::<?php echo $row->name; ?>">
							<span><?php echo $this->escape($row->name); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php if ($tbl->isCheckedOut($this->user->get('id'), $row->checked_out) || !$canDo->get('core.edit.state')) { ?>
						<span class="state <?php echo $cls; ?>">
							<span class="text"><?php echo $alt; ?></span>
						</span>
					<?php } else { ?>
						<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('Set this to %s',$task);?>">
							<span class="text"><?php echo $alt; ?></span>
						</a>
					<?php } ?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, ($row->folder == @$this->rows[$i-1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderup', 'Move Up', $row->ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $n, ($row->folder == @$this->rows[$i+1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderdown', 'Move Down', $row->ordering); ?></span>
					<?php $disabled = $row->ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td>
					<?php echo $access; ?>
				</td>
				<td>
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
	<input type="hidden" name="sort" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="sort_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>