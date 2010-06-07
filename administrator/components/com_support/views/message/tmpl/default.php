<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Ticket Message' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savemsg');
JToolBarHelper::cancel('cancelmsg');

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancelmsg') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if ($('msg[message]').value == '') {
		alert( '<?php echo JText::_('MESSAGE_ERROR_NO_TEXT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('MESSAGE_LEGEND'); ?></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="title"><?php echo JText::_('MESSAGE_SUMMARY'); ?>: <span class="required">*</span></label></td>
						<td><input type="text" name="msg[title]" id="title" value="<?php echo $this->row->title; ?>" size="50" /></td>
					</tr>
		 			<tr>
						<td class="key" style="vertical-align: top;"><label for="message"><?php echo JText::_('MESSAGE_TEXT'); ?>: <span class="required">*</span></label></th>
						<td><?php echo $editor->display('msg[message]', stripslashes($this->row->message), '360px', '200px', '50', '10'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<p><?php echo JText::_('MESSAGE_TEXT_EXPLANATION'); ?></p>
		<p><?php echo JText::_('MESSAGE_TICKET_NUM_EXPLANATION'); ?></p>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="msg[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savemsg" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>