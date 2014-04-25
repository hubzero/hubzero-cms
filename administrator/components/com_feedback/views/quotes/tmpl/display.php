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

$canDo = FeedbackHelper::getActions('quote');

JToolBarHelper::title(JText::_('Success Story Manager'), 'feedback.png');
if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences($this->option, '550');
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
	JToolBarHelper::deleteList();
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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('FEEDBACK_SEARCH'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search...'); ?>" />
	
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('FEEDBACK_COL_SUBMITTED'), 'date', @$this->filters['sort_Dir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('FEEDBACK_COL_AUTHOR'), 'fullname', @$this->filters['sort_Dir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('FEEDBACK_COL_ORGANIZATION'), 'org', @$this->filters['sort_Dir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JText::_('FEEDBACK_COL_QUOTE'); ?></th>
				<!--<th scope="col"><?php //echo JText::_('FEEDBACK_COL_PICTURE'); ?></th>-->
				<?php 
				if ($this->type == 'regular') {
					echo ('<th>' . JText::_('FEEDBACK_COL_PUBLISH_CONSENT') . '</th><th>' . JText::_('FEEDBACK_COL_UID') . '</th>');
				} else {
					echo ('<th>' . JText::_('FEEDBACK_COL_QUOTES') . '</th><th>' . JText::_('FEEDBACK_COL_ROTATION') . '</th>');
				}
				?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	//cut quote at 100 characters
	$quotepreview = stripslashes($row->quote);
	$quotepreview = substr($quotepreview, 0, 100);
	if (strlen ($quotepreview)>=99) {
		$quotepreview = $quotepreview.'...';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i; ?>
				</td>
				<td>
					<input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onClick="isChecked(this.checked);" />
				</td>
				<td>
					<time datetime="<?php echo $row->date; ?>"><?php echo JHTML::_('date', $row->date, JText::_('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;type=<?php echo $this->type ?>&amp;id=<?php echo $row->id; ?>">
						<?php echo $this->escape(stripslashes($row->fullname)); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->fullname)); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo ($row->org) ? $this->escape(stripslashes($row->org)) : '&nbsp;';?></td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;type=<?php echo $this->type ?>&amp;id=<?php echo $row->id; ?>">
						<?php echo $this->escape($quotepreview); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape($quotepreview); ?>
					</span>
				<?php } ?>
				</td>
				<!--
				<td>
					<?php //echo ($row->picture != NULL) ? '<span class="state yes"><span>' . JText::_('FEEDBACK_YES') . '<span></span>' : ''; ?>
				</td>
				-->
				<td>
					<?php if ($this->type == 'regular') {
						echo ($row->publish_ok == 1) ? '<span class="state yes"><span>' . JText::_('FEEDBACK_YES') . '</span></span>' : '';
					} else {
						echo ($row->notable_quotes == 1) ? '<span class="state yes"><span>' . JText::_('FEEDBACK_YES') . '</span></span>' : '';
					} ?>
				</td>
				<td>
					<?php if ($this->type == 'regular') {
						echo $row->userid;
					} else {
						echo ($row->flash_rotation == 1) ? '<span class="state yes"><span>' . JText::_('FEEDBACK_YES') . '</span></span>' : '';
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
	<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>