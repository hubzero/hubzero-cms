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

$canDo = GroupsHelper::getActions('group');

JToolBarHelper::title(JText::_('COM_GROUPS'), 'groups.png');

$bar =  JToolBar::getInstance('toolbar');
// Add an upload button
$bar->appendButton( 'Popup', 'new', 'COM_GROUPS_NEW', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=new&gid=' . $this->filters['gid'], 570, 170 );

JToolBarHelper::spacer();
switch ($this->filters['status'])
{
	case 'invitee':
		//if ($canDo->get('core.edit'))
		//{
			//JToolBarHelper::custom('accept', 'publish', JText::_('Accept'), JText::_('Accept'), false, false);
		//}
		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::custom('uninvite', 'unpublish','COM_GROUPS_MEMBER_UNINVITE', 'COM_GROUPS_MEMBER_UNINVITE', false, false);
		}
	break;
	case 'applicant':
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::custom('approve', 'publish', 'COM_GROUPS_MEMBER_APPROVE', 'COM_GROUPS_MEMBER_APPROVE', false, false);
		}
		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::custom('deny', 'unpublish', 'COM_GROUPS_MEMBER_DENY', 'COM_GROUPS_MEMBER_DENY', false, false);
		}
	break;
	default:
		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::custom('promote', 'promote', 'COM_GROUPS_MEMBER_PROMOTE', 'COM_GROUPS_MEMBER_PROMOTE', false, false);
			JToolBarHelper::custom('demote', 'demote', 'COM_GROUPS_MEMBER_DEMOTE','COM_GROUPS_MEMBER_DEMOTE', false, false);
		}
		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList();
		}
	break;
}
JToolBarHelper::spacer();
JToolBarHelper::help('membership');

$database = JFactory::getDBO();

$document = JFactory::getDocument();
$document->addStyleSheet('components' . DS . $this->option . DS . 'assets' . DS . 'css' . DS . 'groups.css');

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

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('COM_GROUPS_SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_GROUPS_SEARCH'); ?>" />

		<label for="filter-status"><?php echo JText::_('COM_GROUPS_MEMBER_STATUS'); ?>:</label>
		<select name="status" id="filter-status">
			<option value=""<?php echo ($this->filters['status'] == '') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_GROUPS_MEMBER_STATUS'); ?></option>
			<!-- <option value="member"<?php //echo ($this->filters['status'] == 'member') ? ' selected="selected"' : ''; ?>>Member</option> -->
			<option value="manager"<?php echo ($this->filters['status'] == 'manager') ? ' selected="selected"' : ''; ?>>Manager</option>
			<option value="applicant"<?php echo ($this->filters['status'] == 'applicant') ? ' selected="selected"' : ''; ?>>Applicant</option>
			<option value="invitee"<?php echo ($this->filters['status'] == 'invitee') ? ' selected="selected"' : ''; ?>>Invitee</option>
		</select>

		<input type="submit" value="<?php echo JText::_('COM_GROUPS_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="8">(<?php echo $this->escape(stripslashes($this->group->get('cn'))); ?>) <?php echo $this->escape(stripslashes($this->group->get('description'))); ?></th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_GROUPS_ID', 'uidNumber', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_GROUPS_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_GROUPS_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_GROUPS_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('COM_GROUPS_MEMBER_STATUS'); ?></th>
				<th scope="col" colspan="2"><?php echo JText::_('COM_GROUPS_MEMBER_ACTION'); ?></th>
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
$i = 0;
foreach ($this->rows as $row)
{
	if (isset($row->username))
	{
		$reason = new GroupsReason($database);
		$reason->loadReason($row->username, $this->filters['gidNumber']);
		$reasonforjoin = '';
		if ($reason)
		{
			$reasonforjoin = stripslashes( $reason->reason );
		}
	}

	$status = $row->role;
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo (isset($row->uidNumber)) ? $row->uidNumber : $row->email; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row->uidNumber); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit') && isset($row->username)) : ?>
					<a href="index.php?option=com_members&amp;controller=members&amp;task=edit&amp;id[]=<?php echo $row->uidNumber; ?>">
						<?php echo $this->escape(stripslashes($row->name)); ?>
					</a>
				<?php else : ?>
					<span>
						<?php echo $this->escape(stripslashes($row->name)); ?>
					</span>
				<?php endif; ?>
				</td>
				<td>
					<span>
						<?php echo $this->escape(stripslashes($row->username)); ?>
					</span>
				</td>
				<td>
					<span>
						<?php echo $this->escape(stripslashes($row->email)); ?>
					</span>
				</td>
				<td>
					<span class="status <?php echo $status; ?>">
						<?php echo $status; ?>
					</span>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
	<?php
	switch ($status)
	{
		case 'invitee':
		case 'inviteemail':
	?>
					<a class="state unpublish" onclick="javascript:if(confirm('Cancel invitation?')){return true;}else{return false;}" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=uninvite&amp;gid=<?php echo $this->filters['gid']; ?>&amp;id[]=<?php echo (isset($row->uidNumber)) ? $row->uidNumber : $row->email; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo JText::_('COM_GROUPS_MEMBER_UNINVITE'); ?></span>
					</a>
				</td>
				<td>

	<?php
		break;
		case 'applicant':
	?>
					<a class="state publish" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=approve&amp;gid=<?php echo $this->filters['gid']; ?>&amp;id[]=<?php echo $row->uidNumber; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo JText::_('COM_GROUPS_MEMBER_APPROVE'); ?></span>
					</a>
				</td>
				<td>
					<a class="state unpublish" onclick="javascript:if(confirm('Deny membership?')){return true;}else{return false;}" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=deny&amp;gid=<?php echo $this->filters['gid']; ?>&amp;id[]=<?php echo $row->uidNumber; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo JText::_('COM_GROUPS_MEMBER_DENY'); ?></span>
					</a>
	<?php
		break;
		case 'manager':
	?>
					<a class="state demote" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=demote&amp;gid=<?php echo $this->filters['gid']; ?>&amp;id[]=<?php echo $row->uidNumber; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo JText::_('COM_GROUPS_MEMBER_DEMOTE'); ?></span>
					</a>
				</td>
				<td>
					&nbsp;
	<?php
		break;
		default:
		case 'member':
	?>
					<a class="state promote" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=promote&amp;gid=<?php echo $this->filters['gid']; ?>&amp;id[]=<?php echo $row->uidNumber; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo JText::_('COM_GROUPS_MEMBER_PROMOTE'); ?></span>
					</a>
				</td>
				<td>
					<a class="state trash" onclick="javascript:if(confirm('Cancel membership?')){return true;}else{return false;}" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=delete&amp;gid=<?php echo $this->filters['gid']; ?>&amp;id[]=<?php echo $row->uidNumber; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo JText::_('COM_GROUPS_MEMBER_REMOVE'); ?></span>
					</a>
	<?php
		break;
	}
	?>
<?php } ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
	$i++;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="gid" value="<?php echo $this->filters['gid']; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>