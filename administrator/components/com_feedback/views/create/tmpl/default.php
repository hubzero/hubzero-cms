<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );
?>
<form action="index.php" method="post" name="adminForm">
	<fieldset>
		<label>
			<?php echo JText::_('FEEDBACK_USERNAME'); ?>: 
			<input type="text" name="username" value="" />
		</label>
	
		<input type="submit" name="submit" value="<?php echo JText::_('FEEDBACK_NEXT'); ?>" />
	
		<p><?php echo JText::_('FEEDBACK_NO_USERNAME'); ?></p>
		
		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="edit" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</fieldset>
</form>