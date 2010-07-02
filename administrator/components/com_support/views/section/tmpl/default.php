<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Ticket Section' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savesec');
JToolBarHelper::cancel('cancelsec');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancelsec') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if ($('section').value == '') {
		alert( '<?php echo JText::_('SECTION_ERROR_NO_TEXT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="section"><?php echo JText::_('SECTION_TEXT'); ?>:</label></td>
					<td><input type="text" name="sec[section]" id="section" value="<?php echo $this->row->section; ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="sec[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savesec" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>