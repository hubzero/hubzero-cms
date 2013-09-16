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

$canDo = AnswersHelper::getActions('question');

JToolBarHelper::title(JText::_('Answers Manager'), 'answers.png');
if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList();
}
JToolBarHelper::spacer();
JToolBarHelper::help('questions.html', true);

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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('SEARCH'); ?>:</label> 
		<input type="text" name="q" id="filter_search" value="<?php echo $this->escape($this->filters['q']); ?>" />

		<label for="filterby"><?php echo JText::_('Filter by:'); ?></label> 
		<select name="filterby" id="filterby" onchange="document.adminForm.submit( );">
			<option value="open"<?php if ($this->filters['filterby'] == 'open') { echo ' selected="selected"'; } ?>><?php echo JText::_('Open Questions'); ?></option>
			<option value="closed"<?php if ($this->filters['filterby'] == 'closed') { echo ' selected="selected"'; } ?>><?php echo JText::_('Closed Questions'); ?></option>
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('All Questions'); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Subject', 'subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'State', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Created by', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Answers', 'answers', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
for ($i=0, $n=count($this->results); $i < $n; $i++)
{
	$row =& $this->results[$i];

	switch ($row->get('state'))
	{
		case '1':
			$task = 'open';
			$img = 'publish_x.png';
			$alt = JText::_( 'Closed' );
			$cls = 'unpublished';
		break;
		case '0':
			$task = 'close';
			$img = 'publish_g.png';
			$alt = JText::_( 'Open' );
			$cls = 'published';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->get('id'); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('subject'))); ?></span>
					</a>
				<?php } else { ?>
					<span>
						<span><?php echo $this->escape(stripslashes($row->get('subject'))); ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->get('id'); ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task; ?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
					</a>
				<?php } else { ?>
					<span class="state <?php echo $cls; ?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img; ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
					</span>
				<?php } ?>
				</td>
				<td style="white-space: nowrap;">
					<time datetime="<?php echo $row->published(); ?>"><?php echo $row->published('date'); ?></time>
				</td>
				<td>
					<a class="glyph user" href="index.php?option=com_members&amp;controller=members&amp;task=edit&amp;id[]=<?php echo $row->creator('id'); ?>">
						<?php echo $this->escape(stripslashes($row->creator('name'))); ?>
					</a>
				<?php if ($row->get('anonymous')) { ?>
					<br /><span>(anonymous)</span>
				<?php } ?>
				</td>
			<?php if ($row->comments('count', array('filterby' => 'all')) > 0) { ?>
				<td style="white-space: nowrap;">
					<a class="glyph comment" href="index.php?option=<?php echo $this->option ?>&amp;controller=answers&amp;qid=<?php echo $row->get('id'); ?>" title="<?php echo JText::_('View the answers for this Question'); ?>">
						<span><?php echo JText::sprintf('%s response(s)', $row->comments('count')); ?></span>
					</a>
				</td>
			<?php } else { ?>
				<td>
					<span class="glyph comment">
						<span>0</span>
					</span>
				</td>
			<?php } ?>
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
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>