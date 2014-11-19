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

// Menu
JToolBarHelper::title(JText::_('COM_MEMBERS') . ': ' . JText::_('COM_MEMBERS_PASSWORD_RULES'), 'user.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::custom('restore_default_content', 'refresh', 'refresh', 'COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS', false, false);
	JToolBarHelper::spacer();
	JToolBarHelper::addNew();
	JToolBarHelper::editList();
	JToolBarHelper::spacer();
	JToolBarHelper::deleteList();
}
?>

<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'passwordrules') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=passwordrules'); ?>"><?php echo JText::_('COM_MEMBERS_PASSWORD_RULES'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'passwordblacklist') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=passwordblacklist'); ?>"><?php echo JText::_('COM_MEMBERS_PASSWORD_BLACKLIST'); ?></a>
		</li>
	</ul>
</nav>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if (pressbutton == 'restore_default_content') {
		var mes = confirm('<?php echo JText::_('COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS_CONFIRM'); ?>');
		if(!mes) {
			return false;
		}
		submitform( pressbutton );
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_PASSWORD_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_PASSWORD_RULE', 'rule', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_PASSWORD_DESCRIPTION', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'COM_MEMBERS_PASSWORD_ORDERING', 'ordering', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
					<?php echo JHTML::_('grid.order',  $this->rows); ?>
				</th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_MEMBERS_PASSWORD_ENABLED', 'enabled', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
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
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->rule)); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape($row->description); ?>
					</a>
				</td>
				<td class="order">
					<span><?php echo $this->pageNav->orderUpIcon($i, $row->ordering, 'orderup', 'JLIB_HTML_MOVE_UP', $row->ordering); ?></span>
					<span><?php echo $this->pageNav->orderDownIcon($i, $n, $row->ordering, 'orderdown', 'JLIB_HTML_MOVE_DOWN', $row->ordering); ?></span>
					<?php $disabled = $row->ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
				</td>
				<td>
					<a class="state <?php echo ($row->enabled) ? 'yes': 'no'; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=toggle_enabled&id=' . $row->id); ?>">
						<span><?php echo JText::_(($row->enabled ? 'JYES': 'JNO')); ?></span>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>