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
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$timeFormat = 'H:i p';
	$tz = true;
}

$canDo = CoursesHelper::getActions('offering');

JToolBarHelper::title(JText::_('COM_COURSES').': <small><small>[ ' . JText::_('Offerings') . ' ]</small></small>', 'courses.png');
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
	JToolBarHelper::deleteList('delete', 'delete');
}
JToolBarHelper::spacer();
JToolBarHelper::help('offerings.html', true);

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
					(<!-- <a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses&amp;task=edit&amp;id[]=<?php echo $this->course->get('id'); ?>"> -->
						<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
					<!-- </a> -->) 
					<!-- <a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses&amp;task=edit&amp;id[]=<?php echo $this->course->get('id'); ?>"> -->
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					<!-- </a> -->
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('ID'); ?></th>
				<th scope="col"><?php echo JText::_('Title'); ?></th>
				<th scope="col"><?php echo JText::_('Starts'); ?></th>
				<th scope="col"><?php echo JText::_('Ends'); ?></th>
				<th scope="col"><?php echo JText::_('Managers'); ?></th>
				<th scope="col"><?php echo JText::_('Sections'); ?></th>
				<th scope="col"><?php echo JText::_('Enrollment'); ?></th>
				<th scope="col"><?php echo JText::_('Units'); ?></th>
				<th scope="col"><?php echo JText::_('Pages'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$i = 0;
$k = 0;
foreach ($this->rows as $row)
{
	$tip = '[coming soon]';
	$managers = $row->members(array('count' => true, 'student' => 0));
	$units    = $row->units(array('count' => true));
	$students = $row->members(array('count' => true, 'student' => 1));
	$pages    = $row->pages(array('count' => true, 'active' => array(0, 1)));
	$sections = $row->sections(array('count' => true));
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
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo ($row->get('publish_up') && $row->get('publish_up') != '0000-00-00 00:00:00') ? JHTML::_('date', $row->get('publish_up'), $dateFormat, $tz) : JText::_('(no date set)'); ?>
				</td>
				<td>
					<?php echo ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00') ? JHTML::_('date', $row->get('publish_down'), $dateFormat, $tz) : JText::_('(never)'); ?>
				</td>
				<td>
<?php if ($canDo->get('core.manage')) { ?>
					<a class="glyph member" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $managers; ?>
					</a>
<?php } else { ?>
					<span class="glyph member">
						<?php echo $managers; ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $sections > 0) { ?>
						<a class="glyph category" href="index.php?option=<?php echo $this->option ?>&amp;controller=sections&amp;offering=<?php echo $row->get('id'); ?>">
							<?php echo $sections; ?>
						</a>
					<?php } else { ?>
						<span class="glyph category">
							<?php echo $sections; ?>
						</span>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=sections&amp;offering=<?php echo $row->get('id'); ?>&amp;task=add">
							<span><?php echo JText::_('[ + ]'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage')) { ?>
						<a class="glyph member" href="index.php?option=<?php echo $this->option ?>&amp;controller=students&amp;offering=<?php echo $row->get('id'); ?>&amp;section=0">
							<?php echo $students; ?>
						</a>
					<?php } else { ?>
						<span class="glyph member">
							<?php echo $students; ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $units > 0) { ?>
						<a class="glyph list" href="index.php?option=<?php echo $this->option; ?>&amp;controller=units&amp;offering=<?php echo $row->get('id'); ?>">
							<?php echo $units; ?>
						</a>
					<?php } else { ?>
						<?php echo $units; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=units&amp;offering=<?php echo $row->get('id'); ?>&amp;task=add">
							<span><?php echo JText::_('[ + ]'); ?></span>
						</a>
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.manage') && $pages > 0) { ?>
						<a class="glyph list" href="index.php?option=<?php echo $this->option; ?>&amp;controller=pages&amp;offering=<?php echo $row->get('id'); ?>">
							<?php echo $pages; ?>
						</a>
					<?php } else { ?>
						<?php echo $pages; ?>
						<?php if ($canDo->get('core.manage')) { ?>
						&nbsp;
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=pages&amp;course=<?php echo $this->course->get('id'); ?>&amp;offering=<?php echo $row->get('id'); ?>&amp;task=add">
							<span><?php echo JText::_('[ + ]'); ?></span>
						</a>
						<?php } ?>
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

	<input type="hidden" name="course" value="<?php echo $this->course->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>