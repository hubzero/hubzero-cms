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

JToolBarHelper::title(JText::_('COM_PUBLICATIONS_PUBLICATION_MANAGER'), 'addedit.png');
JToolBarHelper::preferences($this->option, '550');
JToolBarHelper::spacer();
JToolBarHelper::editList();
JToolBarHelper::spacer();
JToolBarHelper::deleteList();

JHTML::_('behavior.tooltip');
//jimport('joomla.html.html.grid');
include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'html' . DS . 'grid.php');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>
<?php if ($this->config->get('enabled') == 0) { ?>
<p class="warning"><?php echo JText::_('COM_PUBLICATIONS_COMPONENT_DISABLED'); ?></p>
<?php } ?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<label for="status"><?php echo JText::_('COM_PUBLICATIONS_FIELD_STATUS'); ?>:</label>
		<select name="status" id="status">
			<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_ALL_STATUS'); ?></option>
			<option value="3"<?php echo ($this->filters['status'] == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_DRAFT'); ?></option>
			<option value="5"<?php echo ($this->filters['status'] == 5) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_PENDING'); ?></option>
			<option value="0"<?php echo ($this->filters['status'] == 0 && $this->filters['status'] != 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_UNPUBLISHED'); ?></option>
			<option value="10"<?php echo ($this->filters['status'] == 10) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_PRESERVING'); ?></option>
			<option value="7"<?php echo ($this->filters['status'] == 7) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_WIP'); ?></option>
			<option value="1"<?php echo ($this->filters['status'] == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED'); ?></option>
			<option value="4"<?php echo ($this->filters['status'] == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_READY'); ?></option>
			<option value="2"<?php echo ($this->filters['status'] == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_PUBLICATIONS_VERSION_DELETED'); ?></option>
		</select>

		<label for="category"><?php echo JText::_('COM_PUBLICATIONS_FIELD_CATEGORY'); ?>:</label>
		<?php echo PublicationsAdminHtml::selectCategory($this->categories, 'category', $this->filters['category'], JText::_('COM_PUBLICATIONS_ALL_CATEGORIES'), '', '', ''); ?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('COM_PUBLICATIONS_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_TITLE'), 'title', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JText::_('@v.'); ?></th>
				<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_STATUS'); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('COM_PUBLICATIONS_FIELD_PROJECT'), 'project', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_RELEASES'); ?></th>
				<th colspan="2"><?php echo JText::_('COM_PUBLICATIONS_FIELD_TYPE_CAT'); ?></th>
				<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_LAST_MODIFIED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$filterstring  = $this->filters['sortby']   ? '&amp;sort='.$this->filters['sortby']    : '';
$filterstring .= '&amp;status='.$this->filters['status'];
$filterstring .= ($this->filters['category'])   ? '&amp;category='.$this->filters['category']     : '';

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = $this->rows[$i];

	// Build some publishing info
	$info  = JText::_('COM_PUBLICATIONS_FIELD_CREATED') . ': ' . $row->created . '<br />';
	$info .= JText::_('COM_PUBLICATIONS_FIELD_CREATOR') . ': ' . $this->escape($row->created_by) . '<br />';

	// Get the published status
	$now = JFactory::getDate()->toSql();

	// See if it's checked out or not
	$checked = '';
	$checkedInfo = '';
	if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
	{
		$info .= ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00')
				 ? JText::_('COM_PUBLICATIONS_FIELD_CHECKED_OUT').': '
				. JHTML::_('date', $row->checked_out_time, JText::_('DATE_FORMAT_LC2')) . '<br />'
				 : '';
		$info .= ($row->checked_out)
				 ? JText::_('COM_PUBLICATIONS_FIELD_CHECKED_OUT_BY') . ': '.$row->checked_out . '<br />'
				 : '';
		$checkedInfo = ' ['.JText::_('COM_PUBLICATIONS_FIELD_CHECKED_OUT').']';
		$checked = JHtml::_('image', 'admin/checked_out.png', null, null, true) . '</span>';
	}
	else
	{
		$checked = JHTML::_('grid.id', $i, $row->id, false, 'id');
	}

	// What's the publication status?
	$status = PublicationsAdminHtml::getPubStateProperty($row, 'status');
	$class 	= PublicationsAdminHtml::getPubStateProperty($row, 'class');
	$task 	= PublicationsAdminHtml::getPubStateProperty($row, 'task');
	$date 	= $row->modified && $row->modified != '0000-00-00 00:00:00'
			? JHTML::_('date', $row->modified, JText::_('DATE_FORMAT_LC2'))
			: JHTML::_('date', $row->created, JText::_('DATE_FORMAT_LC2'));
?>
			<tr class="<?php echo "row$k"; ?> <?php echo $row->state == 5 ? 'attention' : ''; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id;  echo $filterstring; ?>" title="<?php echo JText::_( 'COM_PUBLICATIONS_PUBLISH_INFO' );?>::<?php echo $info; ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</a><?php if ($checkedInfo) { echo $checkedInfo; } ?>
				</td>
				<td>
					<?php echo $row->version_label; ?>
				</td>
				<td>
					<span class="<?php echo $class; ?> hasTip" title="<?php echo $status; ?>">&nbsp;</span>
				</td>
				<td>
					<a href="index.php?option=com_projects&amp;task=edit&amp;id[]=<?php echo $row->project_id; ?>"><?php echo \Hubzero\Utility\String::truncate($row->project_title, 50);  ?></a>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=versions&amp;id=<?php echo $row->id;  echo $filterstring; ?>"><?php echo $this->escape($row->versions); ?></a>
				</td>
				<td>
					<?php echo $this->escape($row->base); ?>
				</td>
				<td>
					<?php echo $this->escape($row->cat_name); ?>
				</td>
				<td>
					<?php echo $date; ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<?php PublicationsAdminHtml::statusKey(); ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
