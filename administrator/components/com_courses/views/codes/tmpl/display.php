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

$dateFormat = '%d %b %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = false;
}

$canDo = CoursesHelper::getActions('unit');

JToolBarHelper::title(JText::_('COM_COURSES'), 'courses.png');
if ($canDo->get('core.create')) 
{
	//JToolBarHelper::custom('generate', 'refresh', JText::_('Generate'), JText::_('Generate'), true, false);
	$bar = & JToolBar::getInstance('toolbar');
	$bar->appendButton('Popup', 'refresh', 'Generate', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&section=' . $this->section->get('id') . '&task=options&tmpl=component', 500, 200);

	JToolBarHelper::spacer();
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList('delete', 'delete');
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

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('COM_COURSES_SEARCH'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->filters['search']; ?>" />

		<input type="submit" value="<?php echo JText::_('COM_COURSES_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>
	
	<table class="adminlist" summary="<?php echo JText::_('COM_COURSES_TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th colspan="10">
					(<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;course=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
					</a>) 
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;course=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					</a>: 
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=sections&amp;offering=<?php echo $this->offering->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
					</a>:
					<?php echo $this->escape(stripslashes($this->section->get('title'))); ?>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('ID'); ?></th>
				<th scope="col"><?php echo JText::_('Code'); ?></th>
				<th scope="col"><?php echo JText::_('Created'); ?></th>
				<th scope="col"><?php echo JText::_('Expires'); ?></th>
				<th scope="col"><?php echo JText::_('Redeemed'); ?></th>
				<th scope="col"><?php echo JText::_('Redeemed By'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$i = 0;
$k = 0;
$n = $this->rows->total();
foreach ($this->rows as $row)
{
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape(stripslashes($row->get('code'))); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('code'))); ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo JHTML::_('date', $row->get('created'), $dateFormat, $tz); ?>
				</td>
				<td>
					<?php echo ($row->get('expires') && $row->get('expires') != '0000-00-00 00:00:00') ? JHTML::_('date', $row->get('expires'), $dateFormat, $tz) : JText::_('(never)'); ?>
				</td>
<?php if ($row->get('redeemed') && $row->get('redeemed') != '0000-00-00 00:00:00') { ?>
				<td>
					<?php echo ($row->get('redeemed') && $row->get('redeemed') != '0000-00-00 00:00:00') ? JHTML::_('date', $row->get('redeemed'), $dateFormat, $tz) : JText::_('(unredeemed)'); ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=students&amp;task=edit&amp;section=<?php echo $row->get('section_id'); ?>&amp;id[]=<?php echo $row->get('redeemed_by'); ?>">
						<?php echo $this->escape(stripslashes($row->redeemer()->get('name'))); ?>
					</a>
				</td>
<?php } else { ?>
				<td colspan="2">
					<?php echo JText::_('(not redeemed)'); ?>
				</td>
<?php } ?>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="section" value="<?php echo $this->section->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
