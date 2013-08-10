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

$dateFormat = '%d %b, %Y';
$dateFormat = '%d %b %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat2 = 'd M, Y';
	$dateFormat = 'd M Y';
	$tz = false;
}

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '">' . JText::_('Publication Manager') . '</a>', 'addedit.png');
JToolBarHelper::preferences($this->option, '550');
JToolBarHelper::spacer();

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
<?php if($this->config->get('enabled') == 0) { ?>
<p class="warning">This component is currently disabled and is inaccessible to end users.</p>
<?php } ?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm">
	<fieldset id="filter">
		<label for="status">
			<?php echo JText::_('Status'); ?>:
			<select name="status" id="status">
				<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('[ all ]'); ?></option>
				<option value="3"<?php echo ($this->filters['status'] == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Draft'); ?></option>
				<option value="5"<?php echo ($this->filters['status'] == 5) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Pending'); ?></option>
				<option value="0"<?php echo ($this->filters['status'] == 0 && $this->filters['status'] != 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('Unpublished'); ?></option>
				<option value="1"<?php echo ($this->filters['status'] == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Published'); ?></option>
				<option value="4"<?php echo ($this->filters['status'] == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Ready'); ?></option>
				<option value="6"<?php echo ($this->filters['status'] == 6) ? ' selected="selected"' : ''; ?>><?php echo JText::_('Dark archive'); ?></option>
			</select>
		</label>
	
		<label for="category">
			<?php echo JText::_('Category'); ?>:
			<?php echo PublicationsHtml::selectCategory($this->categories, 'category', $this->filters['category'], '[ all]', '', '', ''); ?>
		</label>
	
		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JText::_('@v.'); ?></th>
				<th><?php echo JText::_('Status'); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Project', 'project', @$this->filters['sortdir'], @$this->filters['sortby'] ); ?></th>
				<th><?php echo JText::_('Releases'); ?></th>
				<th colspan="2"><?php echo JText::_('Master Type/ Category'); ?></th>
				<th><?php echo JText::_('Last modified'); ?></th>
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
$filterstring  = $this->filters['sortby']   ? '&amp;sort='.$this->filters['sortby']    : '';
$filterstring .= '&amp;status='.$this->filters['status'];
$filterstring .= ($this->filters['category'])   ? '&amp;category='.$this->filters['category']     : '';

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	// Build some publishing info
	$info  = JText::_('Created') . ': ' . $row->created . '<br />';
	$info .= JText::_('Created by') . ': ' . $this->escape($row->created_by) . '<br />';

	// Get the published status
	$now = date( "Y-m-d H:i:s" );

	// See if it's checked out or not
	$checked = '';
	if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
	{
		$info .= ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00')
				 ? JText::_('Checked out').': '.JHTML::_('date', $row->checked_out_time, $dateFormat2, $tz).'<br />'
				 : '';
		$info .= ($row->checked_out)
				 ? JText::_('Checked out by').': '.$row->checked_out.'<br />'
				 : '';
		$checked = ' ['.JText::_('checked out').']';
	}
	// What's the publication status?
	$status = PublicationsHtml::getPubStateProperty($row, 'status');
	$class 	= PublicationsHtml::getPubStateProperty($row, 'class');
	$task 	= PublicationsHtml::getPubStateProperty($row, 'task');
	$date 	= $row->modified && $row->modified != '0000-00-00 00:00:00' 
			? JHTML::_('date', $row->modified, $dateFormat, $tz) 
			: JHTML::_('date', $row->created, $dateFormat, $tz);
?>
			<tr class="<?php echo "row$k"; ?> <?php echo $row->state == 5 ? 'attention' : ''; ?>">
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id;  echo $filterstring; ?>" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $info; ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</a><?php if($checked) { echo $checked; } ?>
				</td>
				<td>
					<?php echo $row->version_label; ?>
				</td>
				<td class="status">
					<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
				</td>
				<td>
					<a href="index.php?option=com_projects&amp;task=edit&amp;id[]=<?php echo $row->project_id; ?>"><?php echo Hubzero_View_Helper_Html::shortenText($row->project_title, 50, 0);  ?></a>
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

	<?php PublicationsHtml::statusKey(); ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
