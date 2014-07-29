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

$canDo = CoursesHelper::getActions();

JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_STUDENTS'), 'courses.png');
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
		<div class="col width-30 fltlft">
			<label for="filter_search"><?php echo JText::_('COM_COURSES_SEARCH'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COURSES_STUDENTS_SEARCH_PLACEHOLDER'); ?>" />
			<input type="submit" value="<?php echo JText::_('COM_COURSES_GO'); ?>" />
		</div>
		<div class="col width-70 fltrt">
			<label for="filter_offering"><?php echo JText::_('COM_COURSES_OFFERING'); ?>:</label>
			<select name="offering" id="filter_offering" onchange="document.adminForm.submit();">
				<option value="0"><?php echo JText::_('COM_COURSES_OFFERING_SELECT'); ?></option>
				<?php
				$offerings = array();
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
				$model = CoursesModelCourses::getInstance();
				if ($model->courses()->total() > 0)
				{
					foreach ($model->courses() as $course)
					{
					?>
					<optgroup label="<?php echo $this->escape(stripslashes($course->get('alias'))); ?>">
					<?php
					foreach ($course->offerings() as $offering)
					{
						$offerings[$offering->get('id')] = $course->get('alias') . ' : ' . $offering->get('alias');
						?>
						<option value="<?php echo $this->escape(stripslashes($offering->get('id'))); ?>"<?php if ($offering->get('id') == $this->offering->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('alias'))); ?></option>
						<?php
					}
					?>
					</optgroup>
					<?php
					}
				}
				?>
			</select>

		<?php if ($this->filters['offering']) { ?>
			<label for="filter_section"><?php echo JText::_('COM_COURSES_SECTION'); ?>:</label>
			<select name="section" id="filter_section" onchange="document.adminForm.submit();">
				<option value="0"><?php echo JText::_('COM_COURSES_SECTION_SELECT'); ?></option>
				<?php
				if ($this->offering->sections()->total() > 0)
				{
					foreach ($this->offering->sections() as $section)
					{
				?>
						<option value="<?php echo $this->escape(stripslashes($section->get('id'))); ?>"<?php if ($section->get('id') == $this->filters['section_id']) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
				<?php
					}
				}
				?>
			</select>
		<?php } else { ?>
			<input type="hidden" name="section" id="filter_section" value="0" />
		<?php } ?>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
		<?php if ($this->filters['offering']) { ?>
			<tr>
				<th colspan="<?php echo (!$this->filters['offering']) ? '8' : '7'; ?>">
					(<a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses">
						<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
					</a>)
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=courses">
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					</a>:
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=offerings&amp;course=<?php echo $this->course->get('id'); ?>">
						<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
					</a>
				</th>
			</tr>
		<?php } ?>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_ID'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_NAME'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_EMAIL'); ?></th>
			<?php if (!$this->filters['offering']) { ?>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_COURSE_OFFERING'); ?></th>
			<?php } ?>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_SECTION'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_CERTIFICATE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_ENROLLED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo (!$this->filters['offering']) ? '8' : '7'; ?>"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$i = 0;
$k = 0;
$n = count($this->rows);
foreach ($this->rows as $row)
{
	$section = CoursesModelSection::getInstance($row->get('section_id'));
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<?php echo $this->escape($row->get('user_id')); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;offering=<?php echo $row->get('offering_id'); ?>&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape(stripslashes($row->get('name'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('name'))); ?>
					</span>
				<?php } ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;offering=<?php echo $row->get('offering_id'); ?>&amp;id[]=<?php echo $row->get('id'); ?>">
						<?php echo $this->escape(stripslashes($row->get('email'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('email'))); ?>
					</span>
				<?php } ?>
				</td>
			<?php if (!$this->filters['offering']) { ?>
				<td>
					<?php echo (isset($offerings[$row->get('offering_id')])) ? $offerings[$row->get('offering_id')] : JText::_('COM_COURSES_UNKNOWN'); ?>
				</td>
			<?php } ?>
				<td>
					<?php echo ($section->exists()) ? $this->escape(stripslashes($section->get('title'))) : JText::_('COM_COURSES_NONE'); ?>
				</td>
				<td>
					<span class="state <?php echo ($row->get('token') ? 'publish' : 'unpublish'); ?>">
						<span> <?php echo ($row->get('token') ? 'redeemed' : ''); ?></span>
					</span>
				</td>
				<td>
				<?php if ($row->get('enrolled') && $row->get('enrolled') != '0000-00-00 00:00:00') { ?>
					<?php echo JHTML::_('date', $row->get('enrolled'), JText::_('DATE_FORMAT_HZ1')); ?>
				<?php } else { ?>
					<?php echo JText::_('(unknown)'); ?>
				<?php } ?>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>