<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Tag/Group' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savetg');
JToolBarHelper::cancel('canceltg');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'canceltg') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	if (form.tag.value == '') {
		alert( '<?php echo JText::_('TAG_ERROR_NO_TEXT'); ?>' );
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
					<td class="key"><label for="tag"><?php echo JText::_('TAG_TEXT'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="tag" id="tag" value="<?php echo $this->tag->tag; ?>" size="50" /></td>
				</tr>
				<tr>
					<td class="key"><label for="group"><?php echo JText::_('GROUP_TEXT'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="group" id="group" value="<?php echo $this->group->cn; ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="taggroup[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="taggroup[tagid]" value="<?php echo $this->row->tagid; ?>" />
	<input type="hidden" name="taggroup[groupid]" value="<?php echo $this->row->groupid; ?>" />
	<input type="hidden" name="taggroup[priority]" value="<?php echo $this->row->priority; ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savecat" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>