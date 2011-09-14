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
$text = ( $this->task == 'editq' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Question' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::spacer();
JToolBarHelper::save( 'saveq', 'Save Question' );
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

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (document.getElementById('q_subject').value == ''){
		alert( 'Question must have a subject' );
	} else if (document.getElementById('q_tags').value == ''){
		alert( 'Question must have at least one tag' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
			<legend>Details</legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label>Anonymous:</label></td>
						<td><input type="checkbox" name="question[anonymous]" value="1" <?php echo ($this->row->anonymous) ? 'checked="checked"' : ''; ?> /> Hide your name</td>
					</tr>
					<tr>
						<td class="key"><label>Notify:</label></td>
						<td><input type="checkbox" name="question[email]" value="1" <?php echo ($this->row->email) ? 'checked="checked"' : ''; ?> /> Send e-mail when someone posts a response</td>
					</tr>
					<tr>
						<td class="key"><label>Subject: <span class="required">*</span></label></td>
						<td><input type="text" name="question[subject]" id="q_subject" size="30" maxlength="250" value="<?php echo stripslashes($this->row->subject); ?>" /></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align:top;"><label>Question:</label></td>
						<td><?php echo $editor->display('question[question]', stripslashes($this->row->question), '360px', '200px', '50', '10'); ?></td>
					</tr>
					<tr>
						<td class="key"><label>Tags: <span class="required">*</span></label></td>
						<td><input type="text" name="question[tags]" id="q_tags" size="30" value="<?php echo $this->tags; ?>" /></td>
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
						<td class="key"><label for="question[created_by]">Change Creator:</label></td>
						<td colspan="2"><input type="text" name="question[created_by]" id="created_by" size="25" maxlength="50" value="<?php echo $this->row->created_by; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="created">Created Date:</label></td>
						<td><input type="text" name="question[created]" id="created" size="25" maxlength="19" value="<?php echo $this->row->created; ?>" /></td>
						<td></td>
					</tr>
					<tr>
						<td class="key">State:</td>
						<td colspan="2">
							<select name="question[state]">
								<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>>Open</option>
								<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>>Closed</option>
							</select>
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
	
	<input type="hidden" name="question[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="saveq" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
