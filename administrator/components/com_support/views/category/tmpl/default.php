<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Category' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savecat');
JToolBarHelper::cancel('cancelcat');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancelcat') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if ($('category').value == '') {
		alert( '<?php echo JText::_('CATEGORY_ERROR_NO_TEXT'); ?>' );
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
					<td class="key"><label for="section"><?php echo JText::_('CATEGORY_SECTION'); ?>: <span class="required">*</span></label></td>
					<td>
						<select name="cat[section]" id="section">
<?php
					foreach ($this->sections as $anode) 
					{
						$selected = ($anode->txt == $this->row->section)
								  ? ' selected="selected"'
								  : '';
						echo ' <option value="'.$anode->id.'"'.$selected.'>'.stripslashes($anode->txt).'</option>'."\n";
					}
?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="category"><?php echo JText::_('CATEGORY_TEXT'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="cat[category]" id="category" value="<?php echo $this->row->category; ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="cat[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savecat" />


	<?php echo JHTML::_( 'form.token' ); ?>
</form>