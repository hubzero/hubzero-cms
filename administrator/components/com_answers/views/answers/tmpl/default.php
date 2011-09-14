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
JToolBarHelper::title( '<a href="index.php?option='.$this->option.'">'.JText::_( 'Answers Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::addNew( 'newa', 'New Answer' );
JToolBarHelper::editList();
JToolBarHelper::deleteList( '', 'deletea', 'Delete' );

ximport('Hubzero_View_Helper_Html');

?>
<script type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<h3>
    <a href="index.php?option=<?php echo $this->option; ?>&amp;task=editq&amp;id[]=<?php echo $this->filters['qid']; ?>" title="Edit this question"><?php echo stripslashes($this->question->subject); ?></a>
</h3>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<label>
			Filter by:
			<select name="filterby" onchange="document.adminForm.submit( );">
				<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>>All Responses</option>
				<option value="accepted"<?php if ($this->filters['filterby'] == 'accepted') { echo ' selected="selected"'; } ?>>Accepted Response</option>
				<option value="rejected"<?php if ($this->filters['filterby'] == 'rejected') { echo ' selected="selected"'; } ?>>Unaccepted Responses</option>
			</select>
		</label> 

		<label>
			Sort by:
			<select name="sortby" onchange="document.adminForm.submit( );">
				<option value="m.title"<?php if ($this->filters['sortby'] == 'm.title') { echo ' selected="selected"'; } ?>>Subject</option>
				<option value="m.id DESC"<?php if ($this->filters['sortby'] == 'm.id DESC') { echo ' selected="selected"'; } ?>>ID number</option>
				<option value="m.created_by"<?php if ($this->filters['sortby'] == 'm.created_by') { echo ' selected="selected"'; } ?>>Creator</option>
			</select>
		</label> 
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th>Answer</th>
				<th>State</th>
				<th>Created</th>
				<th>Created by</th>
				<th>Helpful</th>
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
for ($i=0, $n=count( $this->results ); $i < $n; $i++)
{
	$row =& $this->results[$i];

	switch ($row->state)
	{
		case '1':
			$task = 'reject';
			$img = 'publish_g.png';
			$alt = JText::_( 'Accepted' );
			break;
		case '0':
			$task = 'accept';
			$img = 'publish_x.png';
			$alt = JText::_( 'Unaccepted' );
			break;

	}

	$row->answer = stripslashes($row->answer);
	$row->answer = Hubzero_View_Helper_Html::shortenText($row->answer, 75);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edita&amp;id[]=<?php echo $row->id; ?>&amp;qid=<?php echo $this->question->id; ?>" title="Edit this Answer"><?php echo $row->answer; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;qid=<?php echo $this->question->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>"><span><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></span></a></td>
				<td><?php echo $row->created; ?></td>
				<td><?php echo stripslashes($row->name).' ('.$row->created_by.')'; if ($row->anonymous) { echo ' (anon)'; } ?></td>
				<td>+<?php echo $row->helpful; ?> -<?php echo $row->nothelpful; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="qid" value="<?php echo $this->question->id ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="answers" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<p>State: (click icon above to toggle state)</p>
<ul class="key">
	<li class="published"><img src="images/publish_g.png" width="16" height="16" border="0" alt="Accepted" /> = Accepted Answer</li>
	<li class="unpublished"><img src="images/publish_x.png" width="16" height="16" border="0" alt="Unaccepted" /> = Unaccepted Answer</li>
</ul>

