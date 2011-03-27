<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$text = ( $this->task == 'edita' ? JText::_( 'Edit' ) : JText::_( 'New' ) );

JToolBarHelper::title( JText::_( 'Answer' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save( 'savea', 'Save Answer' );
JToolBarHelper::cancel();

$create_date = NULL;
if (intval( $this->row->created ) <> 0) {
	$create_date = JHTML::_('date', $this->row->created );
}

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-mos.css" title="green" />
<script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/calendar/lang/calendar-en.js"></script>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton =='resethelpful') {
		if (confirm('Are you sure you want to reset the Helpful counts to zero? \nAny unsaved changes to this content will be lost.')){
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	/*if (form.answer.value == ''){
		alert( 'Answer must have a response' );
	} else {*/
		submitform( pressbutton );
	//}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform">
	<div class="col width-50">
		<fieldset class="adminform">
			<legend>Details</legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label>Anonymous:</label></td>
						<td><input type="checkbox" name="answer[anonymous]" value="1" <?php echo ($this->row->anonymous) ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<td class="key"><label>Question:</label></td>
						<td><?php echo stripslashes($this->question->subject); ?></td>
					</tr>
					<tr>
						<td class="key"><label>Answer</label></td>
						<td><?php echo $editor->display('answer[answer]', stripslashes($this->row->answer), '360px', '200px', '50', '10'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50">
		<fieldset class="adminform">
			<legend>Parameters</legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="state">Accept:</label></td>
						<td colspan="2"><input type="checkbox" name="answer[state]" id="state" value="1" <?php echo $this->row->state ? 'checked="checked"' : ''; ?> /> (<?php echo ($this->row->state == 1) ? 'Accepted answer' : 'Unaccepted'; ?>)</td>
					</tr>
					<tr>
						<td class="key"><label for="created_by">Change Creator:</label></td>
						<td colspan="2"><input type="text" name="answer[created_by]" id="created_by" size="25" maxlength="50" value="<?php echo $this->row->created_by; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="created">Created Date:</label></td>
						<td><input type="text" name="answer[created]" id="created" size="25" maxlength="19" value="<?php echo $this->row->created; ?>" /></td>
						<td><a class="icon_calendar" id="reset" title="View a calendar to select a date from" onclick="return showCalendar('created', 'y-mm-dd');">calendar</a></td>
					</tr>
					<tr>
						<td class="key">Helpful:</td>
						<td colspan="2">
							+<?php echo $this->row->helpful; ?> -<?php echo $this->row->nothelpful; ?>
							<?php if ( $this->row->helpful > 0 || $this->row->nothelpful > 0 ) { ?>
								<input type="button" name="reset_helpful" value="Reset Helpful" onclick="submitbutton('resethelpful');" />
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="key">Created:</td>
						<td colspan="2"><?php echo ($this->row->created != '0000-00-00 00:00:00') ? $create_date.'</td></tr><tr><td class="key">By:</td><td colspan="2">'.$this->row->created_by : 'New question'; ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="answer[qid]" value="<?php echo $this->question->id; ?>" />
	<input type="hidden" name="answer[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savea" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
