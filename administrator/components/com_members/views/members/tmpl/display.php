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

$juser = & JFactory::getUser();

JToolBarHelper::title(JText::_('MEMBERS'), 'user.png');
JToolBarHelper::preferences('com_members', '550');
JToolBarHelper::spacer();
JToolBarHelper::publishList('confirm', JText::_('Confirm'));
JToolBarHelper::unpublishList('unconfirm', JText::_('Unconfirm'));
JToolBarHelper::spacer();
if ($juser->authorize('com_members', 'admin')) 
{
	JToolBarHelper::addNew();
}
JToolBarHelper::editList();
if ($juser->authorize('com_members', 'admin')) 
{
	JToolBarHelper::deleteList();
}

include_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php');
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
		<label for="filter_search_field"><?php echo JText::_('SEARCH'); ?></label>
		<select name="search_field" id="filter_search_field">
			<option value="uidNumber"<?php if ($this->filters['search_field'] == 'uidNumber') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
			<option value="email"<?php if ($this->filters['search_field'] == 'email') { echo ' selected="selected"'; } ?>><?php echo JText::_('EMAIL'); ?></option>
			<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo JText::_('USERNAME'); ?></option>
			<option value="surname"<?php if ($this->filters['search_field'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_NAME'); ?></option>
			<option value="givenName"<?php if ($this->filters['search_field'] == 'giveName') { echo ' selected="selected"'; } ?>><?php echo JText::_('FIRST_NAME'); ?></option>
			<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo JText::_('FULL_NAME'); ?></option>
		</select>
		
		<label for="filter_search"><?php echo JText::_('for'); ?></label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', 'ID', 'uidNumber', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Name', 'lname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Username', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Organization', 'org', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'E-Mail', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Confirmed', 'emailConfirmed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th><?php echo JHTML::_('grid.sort', '# of contributions', 'rcount', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th><?php echo JHTML::_('grid.sort', 'Last Visit', 'lastvisitDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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

	if (!$row->surname && !$row->givenName) 
	{
		$bits = explode(' ', $row->name);
		$row->surname = array_pop($bits);
		if (count($bits) >= 1) 
		{
			$row->givenName = array_shift($bits);
		}
		if (count($bits) >= 1) 
		{
			$row->middleName = implode(' ', $bits);
		}
	}
	$row->surname = (trim($row->surname)) ? trim($row->surname) : JText::_('[undefined]');
	$row->givenName = (trim($row->givenName)) ? trim($row->givenName) : JText::_('[undefined]');

	switch ($row->emailConfirmed) 
	{
		case '1':
			$task = 'unconfirm';
			$img = 'publish_g.png';
			$alt = JText::_('Yes');
			$state = 'publish';
			break;
		default:
			$task = 'confirm';
			$img = 'publish_x.png';
			$alt = JText::_('No');
			$state = 'unpublish';
			break;
	}

	if (!$row->lastvisitDate || $row->lastvisitDate == "0000-00-00 00:00:00") 
	{
		$lvisit = JText::_('Never');
	} 
	else 
	{
		$lvisit	= $row->lastvisitDate; //= JHTML::_('date',  $row->lastvisitDate, JText::_('DATE_FORMAT_LC4'));
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->uidNumber; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $row->uidNumber; ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<? echo $row->uidNumber; ?>">
						<?php echo $this->escape(stripslashes($row->surname)) . ', ' . $this->escape(stripslashes($row->givenName)) . ' ' . $this->escape(stripslashes($row->middleName)); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->username); ?>
				</td>
				<td>
					<?php echo ($row->organization) ? $this->escape(stripslashes($row->organization)) : '&nbsp;';?>
				</td>
				<td>
<?php if (trim($row->email)) { ?>
					<a href="mailto:<?php echo $row->email; ?>"><?php echo $this->escape($row->email); ?></a>
<?php } ?>
				</td>
				<td>
					<a class="state <?php echo $state; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->uidNumber; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>">
<?php if (version_compare(JVERSION, '1.6', 'lt')) { ?>
						<img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
<?php } else { ?>
						<span class="text"><?php echo $alt; ?></span>
<?php } ?>
					</a>
					<?php //echo $row->rcount; ?>
				</td>
				<td>
					<?php echo $lvisit; ?>
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

