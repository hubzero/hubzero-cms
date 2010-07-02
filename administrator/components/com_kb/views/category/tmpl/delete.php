<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'KNOWLEDGE_BASE' ), 'addedit.png' );
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
}
</script>
<form action="index.php?option=<?php echo $this->option; ?>&amp;task=<?php echo $this->task; ?>&amp;step=2" method="post" name="adminForm">
	<table class="adminform">
		<thead>
 			<tr>
				<th><?php echo JText::_('CHOOSE_DELETE_OPTION'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="radio" name="action" id="action_delete" value="deletefaqs" checked="checked" /> <label for="action_delete"><?php echo JText::_('DELETE_ALL'); ?></label></td>
			</tr>
			<tr>
				<td><input type="radio" name="action" id="action_remove" value="removefaqs" /> <label for="action_remove"><?php echo JText::_('DELETE_ONLY_CATEGORY'); ?></label></td>
			</tr>
			<tr>
				<td><input type="submit" name="Submit" value="<?php echo JText::_('NEXT'); ?>" /></td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>">
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>