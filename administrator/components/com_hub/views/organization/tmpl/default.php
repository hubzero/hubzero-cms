<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'editorg' ? JText::_( 'Edit Organization' ) : JText::_( 'New Organization' ) );

JToolBarHelper::title( JText::_( 'HUB Configuration' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
JToolBarHelper::save('saveorg');
JToolBarHelper::cancel('cancelorg');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->org->id; ?>" />
		<input type="hidden" name="task" value="saveorg" />
			
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="organization"><?php echo JText::_('Organization'); ?>:</label></td>
		 			<td><input type="text" name="organization" id="organization" value="<?php echo $this->org->organization; ?>" size="50" /></td>
		 		</tr>
			</tbody>
		</table>
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>