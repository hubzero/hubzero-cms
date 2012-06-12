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
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Tickets' ).' ]</small></small>', 'support.png' );
JToolBarHelper::preferences('com_support', '550');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('SUPPORT_FIND'); ?>:</label> 
		<input type="text" name="find" id="filter_search" value="<?php echo ($this->filters['_show'] == '') ? $this->escape($this->filters['_find']) : ''; ?>" />
		
		<a title="<?php echo JText::_('SUPPORT_KEYWORD_GUIDE'); ?>::<table id='keyword-guide' summary='<?php echo JText::_('SUPPORT_KEYWORD_TBL_SUMMARY'); ?>'>
			<tbody>
				<tr>
					<th>q:</th>
					<td>&quot;search term&quot;</td>
				</tr>
				<tr>
					<th>status:</th>
					<td>new, open, waiting, closed, all</td>
				</tr>
				<tr>
					<th>reportedby:</th>
					<td>me, [username]</td>
				</tr>
				<tr>
					<th>owner:</th>
					<td>me, none, [username]</td>
				</tr>
				<tr>
					<th>severity:</th>
					<td>critical, major, normal, minor, trivial</td>
				</tr>
				<tr>
					<th>type:</th>
					<td>automatic, submitted, tool</td>
				</tr>
				<tr>
					<th>tag:</th>
					<td>[tag]</td>
				</tr>
				<tr>
					<th>group:</th>
					<td>[group]</td>
				</tr>
			</tbody>
		</table>" class="editlinktip hasTip" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&c=' . $this->controller . '#help'); ?>"><?php echo JText::_('SUPPORT_HELP'); ?></a>
		
		<label><span><?php echo JText::_('OR'); ?></span> <?php echo JText::_('SHOW'); ?>:</label> 
		<select name="show">
			<option value=""<?php if ($this->filters['_show'] == '') { echo ' selected="selected"'; } ?>>--</option>
			<option value="status:new"<?php if ($this->filters['_show'] == 'status:new') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_NEW'); ?></option>
			<option value="status:open"<?php if ($this->filters['_show'] == 'status:open') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_OPEN'); ?></option>
			<option value="owner:none"<?php if ($this->filters['_show'] == 'owner:none') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_UNASSIGNED'); ?></option>
			<option value="status:waiting"<?php if ($this->filters['_show'] == 'status:waiting') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_WAITING'); ?></option>
			<option value="status:closed"<?php if ($this->filters['_show'] == 'status:closed') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_CLOSED'); ?></option>
			<option value="status:all"<?php if ($this->filters['_show'] == 'status:all') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_ALL'); ?></option>
			<option value="reportedby:me"<?php if ($this->filters['_show'] == 'reportedby:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_REPORTED_BY_ME'); ?></option>
			<option value="status:open owner:me"<?php if ($this->filters['_show'] == 'status:open owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_OPEN'); ?></option>
			<option value="status:closed owner:me"<?php if ($this->filters['_show'] == 'status:closed owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_CLOSED'); ?></option>
			<option value="status:all owner:me"<?php if ($this->filters['_show'] == 'status:all owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_ALL'); ?></option>
		</select>
		
		<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
		
		<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist" id="tktlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_NUM'), 'id', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_SUMMARY'), 'summary', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_STATUS'), 'status', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_GROUP'), 'group', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_OWNER'), 'owner', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_AGE'), 'created', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JText::_('SUPPORT_COL_COMMENTS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
			<tr>
				<td colspan="8">
					<ul id="legend">
						<li><span class="critical">&nbsp;</span> <?php echo JText::_('critical'); ?></li>
						<li><span class="major">&nbsp;</span> <?php echo JText::_('major'); ?></li>
						<li><span class="normal">&nbsp;</span> <?php echo JText::_('normal'); ?></li>
						<li><span class="minor">&nbsp;</span> <?php echo JText::_('minor'); ?></li>
						<li><span class="trivial">&nbsp;</span> <?php echo JText::_('trivial'); ?></li>
					</ul>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
ximport('Hubzero_View_Helper_Html');

$k = 0;
$database =& JFactory::getDBO();
$sc = new SupportComment( $database );
$st = new SupportTags( $database );

for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$comments = $sc->countComments(true, $row->id);
	$lastcomment = '0000-00-00 00:00:00';
	if ($comments > 0) {
		$lastcomment = $sc->newestComment(true, $row->id);
	}

	if ($row->status == 2) {
		$status = 'closed';
	} elseif ($comments == 0 && $row->status == 0 && $row->owner == '' && $row->resolved == '') {
		$status = 'new';
	} elseif ($row->status == 1) {
		$status = 'waiting';
	} else {
		if ($row->resolved != '') {
			$status = 'reopened';
		} else {
			$status = 'open';
		}
	}
	
	if (!trim($row->summary)) 
	{
		$row->summary = substr($row->report, 0, 70);
		if (strlen($row->summary) >= 70) 
		{
			$row->summary .= '...';
		}
	}

	$tags = $st->get_tag_cloud( 3, 1, $row->id );
?>
			<tr class="<?php echo ($row->status == 2) ? 'closed' : $row->severity; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><?php echo $row->id; ?></td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<? echo $row->id; ?>"><?php echo stripslashes($row->summary); ?></a>
					<span class="reporter">by <?php echo $row->name; echo ($row->login) ? ' (<a href="index.php?option=com_members&amp;task=edit&amp;id[]='.$this->escape($row->login).'">'.$this->escape($row->login).'</a>)' : ''; ?>, tags: <?php echo $tags; ?></span>
				</td>
				<td><span class="<?php echo $status; ?> status"><?php echo ($row->status == 2) ? '&radic; ' : ''; echo $status; echo ($row->status == 2) ? ' ('.$this->escape($row->resolved).')' : ''; ?></span></td>
				<td><?php echo $this->escape($row->group); ?></td>
				<td><?php echo $this->escape($row->owner); ?></td>
				<td><?php echo Hubzero_View_Helper_Html::timeAgo(Hubzero_View_Helper_Html::mkt($row->created)); ?></td>
				<td><?php echo $comments; echo ($comments > 0) ? ' ('.Hubzero_View_Helper_Html::timeAgo(Hubzero_View_Helper_Html::mkt($lastcomment)).')' : ''; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>