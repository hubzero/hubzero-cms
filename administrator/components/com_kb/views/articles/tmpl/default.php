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

if (isset($this->filters['orphans'])) {
	$ttle = JText::_('COM_KB_ARTICLES').' (orphans)';
} else {
	$ttle = JText::_('COM_KB_ARTICLES');
}

JToolBarHelper::title( '<a href="index.php?option='.$this->option.'">'.JText::_('COM_KB').'</a>: '.$ttle, 'addedit.png' );
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::addNew( 'newfaq' );
JToolBarHelper::editList();
JToolBarHelper::deleteList( '', 'deletefaq', JText::_('COM_KB_DELETE') );

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


<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
<?php if (!isset($this->filters['orphans'])) { ?>
		<label>
			<?php echo JText::_('COM_KB_CATEGORY'); ?>: 
			<?php
			if ($this->filters['cid']) {
				echo KbHtml::sectionSelect( $this->sections, $this->filters['cid'], 'id' );
			} else {
				echo KbHtml::sectionSelect( $this->sections, $this->filters['id'], 'id' );
			}
			?>
		</label>
<?php } ?>
		<label>
			<?php echo JText::_('COM_KB_SORT_BY'); ?>: 
			<select name="filterby" onchange="document.adminForm.task='articles';document.adminForm.submit();">
				<option value="m.modified"<?php if ($this->filters['filterby'] == 'm.modified') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_KB_MODIFIED'); ?></option>
				<option value="m.title"<?php if ($this->filters['filterby'] == 'm.title') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_KB_TITLE'); ?></option>
				<option value="m.id"<?php if ($this->filters['filterby'] == 'm.id') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_KB_ID'); ?></option>
			</select>
		</label>
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
 				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
 				<th><?php echo JText::_('COM_KB_QUESTION'); ?></th>
 				<th><?php echo JText::_('COM_KB_PUBLISHED'); ?></th>
 				<th><?php echo JText::_('COM_KB_CATEGORY'); ?></th>
 				<th><?php echo JText::_('COM_KB_VOTES'); ?></th>
 				<th><?php echo JText::_('COM_KB_CHECKED_OUT'); ?></th>
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
$database =& JFactory::getDBO();
//$sc = new SupportComment( $database );
$st = new KbTags( $database );

for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	switch ($row->state)
	{
		case '1':
			$class = 'published';
			$task = 'unpublish';
			$alt = JText::_('COM_KB_PUBLISHED');
			break;
		case '2':
			$class = 'expired';
			$task = 'publish';
			$alt = JText::_('COM_KB_TRASHED');
			break;
		case '0':
			$class = 'unpublished';
			$task = 'publish';
			$alt = JText::_('COM_KB_UNPUBLISHED');
			break;
	}

	$tags = $st->get_tag_cloud( 3, 1, $row->id );
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;task=editfaq&amp;id[]=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_KB_EDIT_ARTICLE'); ?>"><?php echo $this->escape(stripslashes($row->title)); ?></a><br />
					<span><?php echo JText::_('COM_KB_TAGS'); ?>: <?php echo $tags; ?></span>
				</td>
				<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;cid=<?php echo $this->filters['id']; ?>" title="<?php echo JText::sprintf('COM_KB_SET_TASK',$task);?>"><span><?php echo $alt; ?></span></a></td>
				<td><?php echo $row->ctitle; echo ($row->cctitle) ? ' ('.$row->cctitle.')' : ''; ?></td>
				<td>+<?php echo $row->helpful; ?> -<?php echo $row->nothelpful; ?></td>
				<td><?php echo $this->escape($row->editor); ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['cid']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<p><?php echo JText::_('COM_KB_PUBLISH_KEY'); ?></p>
