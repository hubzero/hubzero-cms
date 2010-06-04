<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

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
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<label><?php echo JText::_('USERNAME'); ?>: 
		<input type="text" name="username" value="" /></label>
		<input type="submit" name="submit" value="<?php echo JText::_('NEXT'); ?>" />
		<p><?php echo JText::_('ADD_CONTRIBUTOR_EXPLANATION'); ?></p>
		<input type="hidden" name="option" value="<?php echo $this->option ?>" />
		<input type="hidden" name="task" value="edit" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</fieldset>
</form>