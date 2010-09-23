<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edittype' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( '<a href="index.php?option=com_resources&task=types">'.JText::_( 'Job Types' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savetype');
JToolBarHelper::cancel('canceltype');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	
	if (pressbutton == 'canceltype') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if (form.category.value == '') {
		alert( 'Type must have a title' );
	} else {
		submitform( pressbutton );
	}
}
</script>
<p style="color:#FF0000;"><?php echo JText::_('Warning: changing the type title will affect all currently available job postings with this type.'); ?></p>
<form action="index.php" method="post" id="adminForm" name="adminForm">			
	<fieldset class="adminform">
		<legend><?php echo JText::_('Edit type title'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="type"><?php echo JText::_('Type Title'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $this->row->category; ?>" /></td>
				</tr>
			</tbody>
		</table>
	
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="savetype" />
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>