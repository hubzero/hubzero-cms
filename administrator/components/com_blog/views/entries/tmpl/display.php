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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = BlogHelper::getActions('entry');

JToolBarHelper::title(JText::_('Blog Manager'), 'blog.png');
if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state')) 
{
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList('', 'delete');
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
JHTML::_('behavior.tooltip');

$dateFormat = '%d %b %Y';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = true;
}
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('SEARCH'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />

<?php if ($this->filters['scope'] == 'group') { ?>
		<?php
		ximport('Hubzero_Group');
		$filters = array();
		$filters['authorized'] = 'admin';
		$filters['fields'] = array('cn','description','published','gidNumber','type');
		$filters['type'] = array(1,3);
		$filters['sortby'] = 'description';
		$groups = Hubzero_Group::find($filters);
		
		$html  = '<label for="filter_group_id">' . JText::_('Group') . ':</label> '."\n";
		$html .= '<select name="group_id" id="filter_group_id">'."\n";
		$html .= '<option value="0"';
		if ($this->filters['group_id'] == 0) 
		{
			$html .= ' selected="selected"';
		}
		$html .= '>'.JText::_('None').'</option>'."\n";
		if ($groups) 
		{
			foreach ($groups as $group)
			{
				$html .= ' <option value="'.$group->gidNumber.'"';
				if ($this->filters['group_id'] == $group->gidNumber) 
				{
					$html .= ' selected="selected"';
				}
				$html .= '>' . $this->escape(stripslashes($group->description)) . '</option>'."\n";
			}
		}
		$html .= '</select>'."\n";
		echo $html;
		?>
<?php } ?>
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Author', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'State', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" colspan="2"><?php echo JHTML::_('grid.sort', 'Comments', 'comments', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
<?php if ($this->filters['scope'] == 'group') { ?>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Group', 'group_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
<?php } ?>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="<?php echo ($this->filters['scope'] == 'group') ? '9' : '8'; ?>"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$config	=& JFactory::getConfig();
$now	=& JFactory::getDate();
$db		=& JFactory::getDBO();

$nullDate = $db->getNullDate();
$rows = $this->rows;
for ($i=0, $n=count($rows); $i < $n; $i++)
{
	$row =& $rows[$i];

	$publish_up =& JFactory::getDate($row->publish_up);
	$publish_down =& JFactory::getDate($row->publish_down);
	$publish_up->setOffset($config->getValue('config.offset'));
	$publish_down->setOffset($config->getValue('config.offset'));
	if ($now->toUnix() <= $publish_up->toUnix() && $row->state == 1) 
	{
		$img = 'publish_y.png';
		$alt = JText::_('Published');
		$cls = 'publish';
		$task = 'unpublish';
	} 
	else if (($now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate) && $row->state == 1) 
	{
		$img = 'publish_g.png';
		$alt = JText::_('Published');
		$cls = 'publish';
		$task = 'unpublish';
	} 
	else if ($now->toUnix() > $publish_down->toUnix() && $row->state == 1) 
	{
		$img = 'publish_r.png';
		$alt = JText::_('Expired');
		$cls = 'publish';
		$task = 'unpublish';
	} 
	else if ($row->state == 0) 
	{
		$img = 'publish_x.png';
		$alt = JText::_('Unpublished');
		$task = 'publish';
		$cls = 'unpublish';
	} 
	else if ($row->state == -1) 
	{
		$img = 'disabled.png';
		$alt = JText::_('Archived');
		$task = 'publish';
		$cls = 'archive';
	}
	$times = '';
	if (isset($row->publish_up)) {
		if ($row->publish_up == $nullDate) {
			$times .= JText::_('Start: Always');
		} else {
			$times .= JText::_('Start') . ': '. $publish_up->toFormat();
		}
	}
	if (isset($row->publish_down)) 
	{
		if ($row->publish_down == $nullDate) 
		{
			$times .= '<br />' . JText::_('Finish: No Expiry');
		} 
		else 
		{
			$times .= '<br />' . JText::_('Finish') . ': '. $publish_down->toFormat();
		}
	}

	if ($row->allow_comments == 0) 
	{
		$cimg = 'publish_x.png';
		$calt = JText::_('Off');
		$cls2 = 'unpublish';
		$state = 1;
	} 
	else 
	{
		$cimg = 'publish_g.png';
		$calt = JText::_('On');
		$cls2 = 'publish';
		$state = 0;
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
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->name)); ?>
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Publish Information');?>::<?php echo $times; ?>">
<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
							<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
						</a>
<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
						</span>
<?php } ?>
					</span>
				</td>
				<td>
					<time datetime="<?php echo $row->created; ?>">
						<?php echo JHTML::_('date', $row->created, $dateFormat, $tz) ?>
					</time>
				</td>
				<td>
					<a class="state <?php echo $cls2; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=setcomments&amp;state=<?php echo $state; ?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $cimg;?>" width="16" height="16" border="0" alt="<?php echo $calt; ?>" /><?php } else { echo $calt; } ?></span>
					</a>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a class="comment" href="index.php?option=<?php echo $this->option ?>&amp;controller=comments&amp;entry_id=<?php echo $row->id; ?>">
						<?php echo $row->comments . ' ' . JText::_('comment(s)'); ?>
					</a>
<?php } else { ?>
					<span class="comment">
						<?php echo $row->comments . ' ' . JText::_('comment(s)'); ?>
					</span>
<?php } ?>
				</td>
<?php if ($this->filters['scope'] == 'group') { ?>
				<td>
					<span>
						<?php echo $this->escape($row->group_id); ?>
					</span>
				</td>
<?php } ?>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="scope" value="<?php echo $this->filters['scope']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
