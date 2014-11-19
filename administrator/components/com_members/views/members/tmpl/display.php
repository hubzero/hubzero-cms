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

$juser =  JFactory::getUser();

JToolBarHelper::title(JText::_('COM_MEMBERS'), 'user.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state'))
{
	JToolBarHelper::publishList('confirm', 'COM_MEMBERS_CONFIRM');
	JToolBarHelper::unpublishList('unconfirm', 'COM_MEMBERS_UNCONFIRM');
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
			<label for="filter_search_field"><?php echo JText::_('COM_MEMBERS_SEARCH'); ?></label>
			<select name="search_field" id="filter_search_field">
				<option value="uidNumber"<?php if ($this->filters['search_field'] == 'uidNumber') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_ID'); ?></option>
				<option value="email"<?php if ($this->filters['search_field'] == 'email') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_EMAIL'); ?></option>
				<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_USERNAME'); ?></option>
				<option value="surname"<?php if ($this->filters['search_field'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_LAST_NAME'); ?></option>
				<option value="givenName"<?php if ($this->filters['search_field'] == 'givenName') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_FIRST_NAME'); ?></option>
				<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_FULL_NAME'); ?></option>
			</select>

			<label for="filter_search"><?php echo JText::_('COM_MEMBERS_SEARCH_FOR'); ?></label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_MEMBERS_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" value="<?php echo JText::_('COM_MEMBERS_GO'); ?>" />
		</div>
		<div class="col width-60 fltrt">
			<select name="emailConfirmed" id="filter_emailConfirmed" onchange="document.adminForm.submit( );">
				<option value="0"<?php if ($this->filters['emailConfirmed'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FILTER_EMAIL_CONFIRMED'); ?></option>
				<option value="1"<?php if ($this->filters['emailConfirmed'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_CONFIRMED'); ?></option>
				<option value="-1"<?php if ($this->filters['emailConfirmed'] == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_UNCONFIRMED'); ?></option>
			</select>
			<select name="public" id="filter_public" onchange="document.adminForm.submit( );">
				<option value="-1"<?php if ($this->filters['public'] == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FILTER_PROFILE_ACCESS'); ?></option>
				<option value="1"<?php if ($this->filters['public'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_PROFILE_ACCESS_PUBLIC'); ?></option>
				<option value="0"<?php if ($this->filters['public'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_MEMBERS_FIELD_PROFILE_ACCESS_PRIVATE'); ?></option>
			</select>
		</div>
		<div class="clr"></div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_ID', 'uidNumber', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_NAME', 'lname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_ORGANIZATION', 'org', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_EMAIL', 'email', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" colspan="2"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_REGISTERED', 'registerDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_CONTRIBUTIONS', 'rcount', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th> -->
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_COL_LAST_VISIT', 'lastvisitDate', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
$default = \Hubzero\User\Profile\Helper::thumbit($default);

$base = str_replace('/administrator', '', JURI::base(true));

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
	$row->surname   = (trim($row->surname))   ? trim($row->surname)   : JText::_('COM_MEMBERS_UNDEFINED');
	$row->givenName = (trim($row->givenName)) ? trim($row->givenName) : JText::_('COM_MEMBERS_UNDEFINED');

	switch ($row->emailConfirmed)
	{
		case '1':
			$task = 'unconfirm';
			$img = 'publish_g.png';
			$alt = JText::_('JYES');
			$state = 'publish';
			break;
		case '3':
			$task = 'unconfirm';
			$img = 'publish_g.png';
			$alt = JText::_('COM_MEMBERS_DOMAIN_SUPPLIED_EMAIL');
			$state = 'publish';
			break;
		default:
			$task = 'confirm';
			$img = 'publish_x.png';
			$alt = JText::_('JNO');
			$state = 'unpublish';
			break;
	}

	if (!$row->lastvisitDate || $row->lastvisitDate == "0000-00-00 00:00:00")
	{
		$lvisit = '<span class="never" style="color:#bbb;">' . JText::_('COM_MEMBERS_NEVER') . '</span>';
	}
	else
	{
		$lvisit = '<time datetime="' . $row->lastvisitDate . '">' . JHTML::_('date', $row->lastvisitDate, 'Y-m-d') . '</time>';
	}

	if ($row->picture)
	{
		$thumb  = DS . trim($this->config->get('webpath'), DS);
		$thumb .= DS . \Hubzero\User\Profile\Helper::niceidformat($row->uidNumber);
		$thumb .= DS . ltrim($row->picture, DS);
		$thumb = \Hubzero\User\Profile\Helper::thumbit($thumb);

		if (file_exists(JPATH_ROOT . $thumb))
		{
			$picture = $thumb;
		}
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->uidNumber; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $row->uidNumber; ?>
				</td>
				<td>
					<a class="editlinktip hasTip" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->uidNumber); ?>" title="<?php echo $this->escape(stripslashes($row->name)); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $base . $picture; ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><span class=&quot;glyph org&quot;><?php echo ($row->organization) ? $this->escape(stripslashes($row->organization)) : JText::_('COM_MEMBERS_UNKNOWN'); ?></span><br /><span class=&quot;glyph <?php echo ($row->public) ? 'public' : 'private'; ?>&quot;><?php echo ($row->public) ? 'public profile' : 'private profile'; ?></span>">
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
					<a class="state <?php echo $state; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->uidNumber . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::sprintf('COM_MEMBERS_SET_TASK', $task); ?>">
						<span class="text"><?php echo $alt; ?></span>
					</a>
				</td>
				<td>
					<time datetime="<?php echo $row->registerDate; ?>"><?php echo JHTML::_('date', $row->registerDate, 'Y-m-d'); ?></time>
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