<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Projects' ), 'user.png' );
JToolBarHelper::preferences('com_projects', '550');
JToolBarHelper::editList();

include_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php');

$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<span><?php echo JText::_('Total projects'); ?>: <strong><?php echo $this->total; ?></strong></span> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;
		<label for="filter_search"><?php echo JText::_('Search'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search...'); ?>" />

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>

	<table class="adminlist" id="projects-admin">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th scope="col"> </th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th scope="col" colspan="2"><?php echo JHTML::_('grid.sort', 'Owner', 'owner', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Status', 'status', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Privacy', 'privacy', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
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
			$filterstring  = ($this->filters['sortby'])   ? '&amp;sort='.$this->filters['sortby']     : '';

			$database = JFactory::getDBO();
			$now = JFactory::getDate()->toSql();

			$database = JFactory::getDBO();
			$pt = new ProjectTags($database);

			for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
			{
				$row = $this->rows[$i];

				$thumb = ProjectsHtml::getThumbSrc($row->id, $row->alias, $row->picture, $this->config);

				if ($row->owned_by_group && !$row->groupcn)
				{
					$row->groupname = '<span class="italic pale">'.JText::_('COM_PROJECTS_INFO_DELETED_GROUP').'</span>';
				}
				$owner = ($row->owned_by_group) ? $row->groupname.'<span class="block  prominent">'.$row->groupcn.'</span>' : $row->authorname;
				$ownerclass = ($row->owned_by_group) ? '<span class="i_group">&nbsp;</span>' : '<span class="i_user">&nbsp;</span>';

				// Determine status
				$status = '';
				if ($row->state == 1 && $row->setup_stage >= $setup_complete)
				{
					$status = '<span class="active">'.JText::_('Active').'</span> '.JText::_('since').' '.JHTML::_('date', $row->created, 'd M. Y');
				}
				else if ($row->state == 2)
				{
					$status  = '<span class="deleted">'.JText::_('Deleted').'</span> ';
				}
				else if ($row->setup_stage < $setup_complete)
				{
					$status = '<span class="setup">'.JText::_('Setup').'</span> '.JText::_('in progress');
				}
				else if ($row->state == 0)
				{
					$status = '<span class="faded italic">'.JText::_('Inactive/Suspended').'</span> ';
				}
				else if ($row->state == 5)
				{
					$status = '<span class="inactive">'.JText::_('Pending approval').'</span> ';
				}

				$tags = $pt->get_tag_cloud(3, 1, $row->id);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo JHTML::_('grid.id', $i, $row->id, false, 'id' ); ?></td>
				<td><?php echo $row->id; ?></td>
				<td><?php echo '<img src="'.$thumb.'" width="30" height="30" alt="' . $this->escape($row->alias) . '" />'; ?></td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;  echo $filterstring; ?>"><?php echo stripslashes($row->title); ?></a><br />
					<strong><?php echo stripslashes($row->alias); ?></strong>
					<?php if ($tags) { ?>
						<span class="project-tags block">
							<?php echo $tags; ?>
						</span>
					<?php } ?>
				</td>
				<td><?php echo $ownerclass; ?></td>
				<td><?php echo $owner; ?></td>
				<td><?php echo $status; ?></td>
				<td><?php echo ($row->private == 1) ? '<span class="private">&nbsp;</span>' : ''; ?></td>
			</tr>
			<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
