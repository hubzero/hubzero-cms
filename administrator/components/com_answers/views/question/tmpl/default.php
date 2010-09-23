<?php
// No direct access
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
						<td><input type="text" name="question[subject]" size="30" maxlength="250" value="<?php echo stripslashes($this->row->subject); ?>" /></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align:top;"><label>Question:</label></td>
						<td><?php echo $editor->display('question[question]', stripslashes($this->row->question), '360px', '200px', '50', '10'); ?></td>
					</tr>
					<tr>
						<td class="key"><label>Tags: <span class="required">*</span></label></td>
						<td><input type="text" name="question[tags]" size="30" value="<?php echo $this->tags; ?>" /></td>
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