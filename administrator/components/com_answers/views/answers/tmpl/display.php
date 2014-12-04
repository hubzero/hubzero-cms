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

$canDo = AnswersHelperPermissions::getActions('answer');

JToolBarHelper::title(JText::_('COM_ANSWERS_TITLE') . ': ' . JText::_('COM_ANSWERS_RESPONSES'), 'answers.png');
if ($canDo->get('core.create') && $this->filters['question_id'])
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList();
	JToolBarHelper::spacer();
}
JToolBarHelper::help('responses');

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
		<label for="filterby"><?php echo JText::_('COM_ANSWERS_FILTER_BY'); ?></label>
		<select name="filterby" id="filterby" onchange="document.adminForm.submit();">
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_ANSWERS_FILTER_BY_ALL_RESPONSES'); ?></option>
			<option value="accepted"<?php if ($this->filters['filterby'] == 'accepted') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_ANSWERS_FILTER_BY_ACCEPTED'); ?></option>
			<option value="rejected"<?php if ($this->filters['filterby'] == 'rejected') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_ANSWERS_FILTER_BY_UNACCEPTED'); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="6">
					<?php if ($this->question->exists()) { ?>
						#<?php echo $this->escape(stripslashes($this->question->get('id'))); ?> -
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=questions&task=edit&id=' . $this->question->get('id')); ?>">
							<?php echo $this->escape($this->question->subject('clean')); ?>
						</a>
					<?php } else { ?>
						<?php echo JText::_('COM_ANSWERS_RESPONSES_TO_ALL'); ?>
					<?php } ?>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_ANSWERS_COL_ANSWER', 'answer', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_ANSWERS_COL_ACCEPTED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_ANSWERS_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_ANSWERS_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_ANSWERS_COL_VOTES', 'helpful', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->results); $i < $n; $i++)
{
	$row =& $this->results[$i];

	switch (intval($row->get('state')))
	{
		case 1:
			$task = 'reject';
			$alt = JText::_('COM_ANSWERS_STATE_ACCEPTED');
			$cls = 'published';
		break;
		case 0:
			$task = 'accept';
			$alt = JText::_('COM_ANSWERS_STATE_UNACCEPTED');
			$cls = 'unpublished';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id') . '&qid=' . $this->question->get('id')); ?>">
						<span><?php echo $row->content('clean', 75); ?></span>
					</a>
				</td>
				<td>
					<a class="state <?php echo $cls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&qid=' . $this->question->get('id') . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_SET_STATE', $task); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
				<td>
					<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
				</td>
				<td>
					<a class="glyph user" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=members&task=edit&id=' . $row->creator('id')); ?>">
						<span><?php echo $this->escape(stripslashes($row->creator('name'))).' ('.$row->creator('id').')'; ?></span>
					</a>
					<?php if ($row->get('anonymous')) { ?>
						<br /><span>(<?php echo JText::_('COM_ANSWERS_ANONYMOUS'); ?>)</span>
					<?php } ?>
				</td>
				<td>
					<span class="vote like" style="color:green;">+<?php echo $row->get('helpful', 0); ?></span>
					<span class="vote dislike" style="color:red;">-<?php echo $row->get('nothelpful', 0); ?></span>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="qid" value="<?php echo $this->question->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>