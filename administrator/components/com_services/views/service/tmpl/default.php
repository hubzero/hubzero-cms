<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::save('saveservice', 'Save Changes');
JToolBarHelper::cancel();

?>
<script type="text/javascript">
<!--
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
}
//-->
</script>

<form action="index.php" method="post" name="adminForm">
	<?php
	if ($this->getError()) {
		echo '<p>'.JText::_('Error:').' '.$this->getError().'</p>';
	}
	?>
	<fieldset class="adminform">

	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>