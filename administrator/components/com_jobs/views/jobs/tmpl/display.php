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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = JobsHelper::getActions('job');

JToolBarHelper::title(JText::_('COM_JOBS'), 'addedit.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences('com_jobs', '550');
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
JToolBarHelper::spacer();
JToolBarHelper::help('jobs');

$this->css();

JHTML::_('behavior.tooltip');
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
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_JOBS_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('COM_JOBS_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_JOBS_COL_CODE'); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_JOBS_COL_TITLE', 'title', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_JOBS_COL_COMPANY', 'location', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_JOBS_COL_STATUS', 'status', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_JOBS_COL_OWNER', 'adminposting', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_JOBS_COL_ADDED', 'added', @$this->filters['sortdir'], @$this->filters['sortby']); ?></th>
				<th scope="col"><?php echo JText::_('COM_JOBS_COL_APPLICATIONS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;

$now = JFactory::getDate()->toSql();

$database = JFactory::getDBO();

$jt = new JobType($database);
$jc = new JobCategory($database);

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	$admin = $row->employerid == 1 ? 1 : 0;
	$adminclass = $admin ? 'class="adminpost"' : '';

	$curtype = $row->type > 0 ? $jt->getType($row->type) : '';
	$curcat  = $row->cid > 0  ? $jc->getCat($row->cid)   : '';

	// Build some publishing info
	$info  = JText::_('COM_JOBS_FIELD_CREATED') . ': ' . JHTML::_('date', $row->added, JText::_('DATE_FORMAT_HZ1')) . '<br />';
	$info .= JText::_('COM_JOBS_FIELD_CREATOR') . ': ' . $row->addedBy;
	$info .= $admin ? ' ' . JText::_('COM_JOBS_ADMIN') : '';
	$info .= '<br />';
	$info .= JText::_('COM_JOBS_FIELD_CATEGORY') . ': ' . $curcat . '<br />';
	$info .= JText::_('COM_JOBS_FIELD_TYPE') . ': ' . $curtype . '<br />';

	// Get the published status
	switch ($row->status)
	{
		case 0:
			$alt   = JText::_('COM_JOBS_STATUS_PENDING');
			$class = 'post_pending';
		break;
		case 1:
			$alt =  $row->inactive && $row->inactive < $now
				 ? JText::_('COM_JOBS_STATUS_EXPIRED')
				 : JText::_('COM_JOBS_STATUS_ACTIVE');
			$class = $row->inactive && $row->inactive < $now
				   ? 'post_invalidsub'
				   : 'post_active';
		break;
		case 2:
			$alt   = JText::_('COM_JOBS_STATUS_DELETED');
			$class = 'post_deleted';
		break;
		case 3:
			$alt   = JText::_('COM_JOBS_STATUS_INACTIVE');
			$class = 'post_inactive';
		break;
		case 4:
			$alt   = JText::_('COM_JOBS_STATUS_DRAFT');
			$class = 'post_draft';
		break;
		default:
			$alt   = '-';
			$class = '';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo JHTML::_('grid.id', $i, $row->id, false, 'id'); ?>
				</td>
				<td>
					<?php echo $this->escape($row->code); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_JOBS_PUBLISH_INFO'); ?>::<?php echo $info; ?>">
							<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
						</a>
					<?php } else { ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_('COM_JOBS_PUBLISH_INFO'); ?>::<?php echo $info; ?>">
							<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<span class="glyph company"><?php echo $this->escape($row->companyName); ?></span>, <br />
					<span class="glyph location"><?php echo $this->escape($row->companyLocation); ?></span>
				</td>
				<td>
					<span class="<?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
				</td>
				<td>
					<span <?php echo $adminclass; ?>>
						<span><?php echo ($admin ? JText::_('COM_JOBS_ADMIN') : ''); ?></span>
					</span>
				</td>
				<td>
					<time datetime="<?php echo $row->added; ?>"><?php echo JHTML::_('date', $row->added, JText::_('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td>
					<?php echo $row->applications; ?>
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
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sortby']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>