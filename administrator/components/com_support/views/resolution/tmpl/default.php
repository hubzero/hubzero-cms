<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Ticket Resolution' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('saveres');
JToolBarHelper::cancel('cancelres');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancelres') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if ($('title').value == '') {
		alert( '<?php echo JText::_('RESOLUTION_ERROR_NO_TEXT'); ?>' );
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
					<td class="key"><label for="title"><?php echo JText::_('RESOLUTION_TEXT'); ?>:</label></td>
					<td><input type="text" name="res[title]" id="title" value="<?php echo htmlentities(stripslashes($this->row->title), ENT_QUOTES); ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="res[alias]" value="<?php echo $this->row->alias; ?>" />
	<input type="hidden" name="res[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savesec" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>