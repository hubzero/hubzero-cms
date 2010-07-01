<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_('HUB Configuration').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
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
	if (form.name.value == '') {
		alert( 'You must fill in a variable name' );
	} else if (form.value.value == '') {
		alert( 'You must fill in a value' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<h2><?php echo ($this->name) ? 'Edit' : 'New'; ?> Variable</h2>

	<fieldset class="adminform">
		<table class="admintable">
		 <tbody>
		  <tr>
		   <td class="key"><label for="name">Variable:</label></td>
		   <td>
<?php 
		if ($this->name) {
			echo $this->name.' <input type="hidden" name="editname" value="' . $this->name . '" />';
		} else { 
			echo '<input type="text" name="name" id="name" size="30" maxlength="250" value="' . $this->name . '" />';
		} 
?>
           </td>
		  </tr>
		  <tr>
		   <td style="vertical-align: top;" class="key"><label for="value">Value:</label></td>
		   <td><textarea name="value" id="value" cols="50" rows="15"><?php echo $this->value; ?></textarea></td>
		  </tr>
		 </tbody>
		</table>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<p style="text-align:center;">Note: These variable settings can be overridden with the file <span style="text-decoration:underline;">hubconfiguration-local.php</span></p>