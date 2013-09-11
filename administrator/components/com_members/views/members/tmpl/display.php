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

$juser = & JFactory::getUser();

JToolBarHelper::title(JText::_('MEMBERS'), 'user.png');
if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state')) 
{
	JToolBarHelper::publishList('confirm', JText::_('Confirm'));
	JToolBarHelper::unpublishList('unconfirm', JText::_('Unconfirm'));
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete')) 
{
	//JToolBarHelper::deleteList();
}

$dateFormat = '%Y-%m-%d';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'Y-m-d';
	$tz = true;
}

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
		<div class="col width-40 fltlft">
			<label for="filter_search_field"><?php echo JText::_('SEARCH'); ?></label>
			<select name="search_field" id="filter_search_field">
				<option value="uidNumber"<?php if ($this->filters['search_field'] == 'uidNumber') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
				<option value="email"<?php if ($this->filters['search_field'] == 'email') { echo ' selected="selected"'; } ?>><?php echo JText::_('EMAIL'); ?></option>
				<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo JText::_('USERNAME'); ?></option>
				<option value="surname"<?php if ($this->filters['search_field'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_NAME'); ?></option>
				<option value="givenName"<?php if ($this->filters['search_field'] == 'givenName') { echo ' selected="selected"'; } ?>><?php echo JText::_('FIRST_NAME'); ?></option>
				<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo JText::_('FULL_NAME'); ?></option>
			</select>
			
			<label for="filter_search"><?php echo JText::_('for'); ?></label> 
			<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />
			
			<input type="submit" value="<?php echo JText::_('GO'); ?>" />
		</div>
		<div class="col width-60 fltrt" style="text-align: right;">
			<select name="emailConfirmed" id="filter_emailConfirmed" onchange="document.adminForm.submit( );">
				<option value="0"<?php if ($this->filters['emailConfirmed'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('- Select email confirmed -'); ?></option>
				<option value="1"<?php if ($this->filters['emailConfirmed'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Confirmed'); ?></option>
				<option value="-1"<?php if ($this->filters['emailConfirmed'] == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Unconfirmed'); ?></option>
			</select>
			<select name="public" id="filter_public" onchange="document.adminForm.submit( );">
				<option value="-1"<?php if ($this->filters['public'] == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('- Select profile access -'); ?></option>
				<option value="1"<?php if ($this->filters['public'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('Public'); ?></option>
				<option value="0"<?php if ($this->filters['public'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('Private'); ?></option>
			</select>
		</div>
		<div class="clr"></div>
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'uidNumber', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Name'), 'lname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Username'), 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Organization'), 'org', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('E-Mail'), 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" colspan="2"><?php echo JHTML::_('grid.sort', JText::_('Registered'), 'registerDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col"><?php echo JHTML::_('grid.sort', JText::_('# of contributions'), 'rcount', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('Last Visit'), 'lastvisitDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
$default = DS . trim($this->config->get('defaultpic'), DS);
$default = Hubzero_User_Profile_Helper::thumbit($default);

$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$picture = $default;

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
		case '3':
			$task = 'unconfirm';
			$img = 'publish_g.png';
			$alt = JText::_('Domain Supplied Email');
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
		$lvisit = '<span class="never" style="color:#bbb;">' . JText::_('never') . '</span>';
	} 
	else 
	{
		$lvisit = JHTML::_('date', $row->lastvisitDate, $dateFormat, $tz);
	}
	
	if ($row->picture)
	{
		$thumb  = DS . trim($this->config->get('webpath'), DS);
		$thumb .= DS . Hubzero_User_Profile_Helper::niceidformat($row->uidNumber);
		$thumb .= DS . ltrim($row->picture, DS);
		$thumb = Hubzero_User_Profile_Helper::thumbit($thumb);
		
		if (file_exists(JPATH_ROOT . $thumb))
		{
			$picture = $thumb;
		}
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
					<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<? echo $row->uidNumber; ?>" title="<?php echo $this->escape(stripslashes($row->name)); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $picture; ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><span class=&quot;glyph org&quot;><?php echo ($row->organization) ? $this->escape(stripslashes($row->organization)) : '[organization unknown]'; ?></span><br /><span class=&quot;glyph <?php echo ($row->public) ? 'public' : 'private'; ?>&quot;><?php echo ($row->public) ? 'public profile' : 'private profile'; ?></span>">
						<?php echo $this->escape(stripslashes($row->surname)) . ', ' . $this->escape(stripslashes($row->givenName)) . ' ' . $this->escape(stripslashes($row->middleName)); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->username); ?>
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
				</td>
				<td>
					<time datetime="<?php echo $row->registerDate; ?>"><?php echo JHTML::_('date', $row->registerDate, $dateFormat, $tz); ?></time>
				</td>
				<td>
					<time datetime="<?php echo $row->lastvisitDate; ?>"><?php echo $lvisit; ?></time>
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