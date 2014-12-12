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

JToolBarHelper::title(JText::_('COM_COURSES') . ': ' . JText::_('COM_COURSES_COUPON_CODES'), 'courses.png');
if ($canDo->get('core.create'))
{
	$bar =  JToolBar::getInstance('toolbar');
	$bar->appendButton('Popup', 'refresh', 'COM_COURSES_GENERATE', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&section=' . $this->section->get('id') . '&task=options&tmpl=component', 500, 200);

	JToolBarHelper::spacer();
	JToolBarHelper::custom('export', 'export', 'export', 'COM_COURSES_EXPORT_CODES', false);
	JToolBarHelper::spacer();
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList('COM_COURSES_DELETE_CONFIRM', 'delete');
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
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" value="<?php echo JText::_('COM_COURSES_GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');$('#filter-redeemed').val('-1');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filter-redeemed"><?php echo JText::_('COM_COURSES_FIELD_STATE'); ?>:</label>
			<select name="redeemed" id="filter-redeemed" onchange="this.form.submit();">
				<option value="-1"<?php if ($this->filters['redeemed'] == -1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_ALL_STATES'); ?></option>
				<option value="1"<?php if ($this->filters['redeemed'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_FILTER_REDEEMED'); ?></option>
				<option value="0"<?php if ($this->filters['redeemed'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_COURSES_FILTER_UNREDEEMED'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="7">
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
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_ID'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_CODE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_CREATED'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_EXPIRES'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_REDEEMED'); ?></th>
				<th scope="col"><?php echo JText::_('COM_COURSES_COL_REDEEMED_BY'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
foreach ($this->rows as $i => $row)
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
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape(stripslashes($row->get('code'))); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->get('code'))); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<time datetime="<?php echo $row->get('created'); ?>"><?php echo JHTML::_('date', $row->get('created'), JText::_('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td>
					<?php echo ($row->get('expires') && $row->get('expires') != '0000-00-00 00:00:00') ? JHTML::_('date', $row->get('expires'), JText::_('DATE_FORMAT_HZ1')) : JText::_('COM_COURSES_NEVER'); ?>
				</td>
			<?php if ($row->get('redeemed')) { ?>
				<td>
					<span class="state <?php echo (($row->get('redeemed') && $row->get('redeemed') != '0000-00-00 00:00:00') || $row->get('redeemed_by')) ? 'yes' : 'no'; ?>">
						<span><?php echo ($row->get('redeemed') && $row->get('redeemed') != '0000-00-00 00:00:00') ? '<time datetime="' . $row->get('redeemed') . '">' . JHTML::_('date', $row->get('redeemed'), JText::_('DATE_FORMAT_HZ1')) . '</time>' : JText::_('JNO'); ?></span>
					</span>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=students&task=edit&section=' . $row->get('section_id') . '&id=' . $row->get('redeemed_by')); ?>">
						<?php echo $this->escape(stripslashes($row->redeemer()->get('name'))); ?>
					</a>
				</td>
			<?php } else { ?>
				<td colspan="2">
					<?php echo JText::_('COM_COURSES_UNREDEEMED'); ?>
				</td>
			<?php } ?>
			</tr>
<?php
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
