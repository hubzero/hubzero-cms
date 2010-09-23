<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'editcat' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( '<a href="index.php?option=com_jobs&task=categories">'.JText::_( 'Job Categories' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savecat');
JToolBarHelper::cancel('cancelcat');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	
	if (pressbutton == 'cancelcat') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if (form.category.value == '') {
		alert( 'Category must have a title' );
	} else {
		submitform( pressbutton );
	}
}
</script>
<p style="color:#FF0000;"><?php echo JText::_('Warning: changing the category title will affect all currently available job postings in this category.'); ?></p>
<form action="index.php" method="post" id="adminForm" name="adminForm">			
	<fieldset class="adminform">
		<legend><?php echo JText::_('Edit category title'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="type"><?php echo JText::_('Category Title'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $this->row->category; ?>" /></td>
				</tr>
                <tr>
					<td class="key"><label for="description"><?php echo JText::_('Description'); ?>: </label></td>
					<td><input type="text" name="description" id="description"  maxlength="255" value="<?php echo $this->row->description; ?>" /></td>
				</tr>
			</tbody>
		</table>
	
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="savecat" />
	</fieldset>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>