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

JToolBarHelper::title(JText::_('COM_SYSTEM_ROUTES_MANAGER'), 'config.png');
JToolBarHelper::preferences($this->option, '550');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label><?php echo JText::_('COM_SYSTEM_ROUTES_VIEW_MODE'); ?>:</label>
		<?php echo $this->lists['viewmode']; ?>

		<label><?php echo JText::_('COM_SYSTEM_ROUTES_SORT_BY'); ?>:</label>
		<?php echo $this->lists['sortby']; ?>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('COM_SYSTEM_ROUTES_COL_NUM'); ?></th>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_SYSTEM_ROUTES_COL_HITS'); ?></th>
				<th scope="col"><?php echo JText::_(($this->is404mode == true ? 'COM_SYSTEM_ROUTES_COL_DATE_ADDED' : 'COM_SYSTEM_ROUTES_COL_SEF_URL')); ?></th>
				<th scope="col"><?php echo JText::_(($this->is404mode == true ? 'COM_SYSTEM_ROUTES_COL_URL' : 'COM_SYSTEM_ROUTES_COL_REAL_URL')); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];
?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td><?php echo $i; ?></td>
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $this->escape($row->cpt); ?></td>
				<td>
					<?php if ($this->is404mode == true) {
						echo $this->escape($row->dateadd);
					} else { ?>
						<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id;?>">
							<?php echo $this->escape($row->oldurl); ?>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php if ($this->is404mode == true) { ?>
						<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id;?>">
							<?php echo $this->escape($row->oldurl); ?>
						</a>
					<?php } else {
						echo $this->escape($row->newurl);
					} ?>
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

	<?php echo JHTML::_('form.token'); ?>
</form>
