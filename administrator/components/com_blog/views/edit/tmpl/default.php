<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

if ($this->task == 'add') {
	$txt = JText::_('Add');
} else {
	$txt = JText::_('Edit');
}

JToolBarHelper::title( JText::_( 'Hello' ).' <small><small>[ '.$txt.' ]</small></small>','generic.png' );
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel();

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
	if (form.greeting.value == ''){
		alert( <?php echo JText::_('Error! You must fill in a title!'); ?> );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform">
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('Details'); ?></legend>

		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label><?php echo JText::_('Greeting'); ?>:</label></td>
					<td><input type="text" name="greeting" size="30" maxlength="255" value="<?php echo htmlentities(stripslashes($this->row->greeting),ENT_QUOTES); ?>" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savecat" />

</form>
