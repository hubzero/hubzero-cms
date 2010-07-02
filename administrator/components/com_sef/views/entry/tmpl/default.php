<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( '<a href="index.php?option=com_sef">'.JText::_( 'SEF Manager' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
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
	// do field validation
	if (form.newurl.value == "") {
		alert( "You must provide a URL for the redirection." );
	} else {
		submitform( pressbutton );
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
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="oldurl">New <acronym title="Search Engine Friendly">SEF</acronym> URL:</label></td>
					<td><input type="text" size="80" name="oldurl" id="oldurl" value="<?php echo $this->row->oldurl; ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="newurl">Old Non-<acronym title="Search Engine Friendly">SEF</acronym> URL:</label></td>
					<td>
						<input type="text" size="80" name="newurl" id="newurl" value="<?php echo $this->row->newurl; ?>" />
						<p class="info">only relative redirection from the document root <em>without</em> a '/' at the begining</p>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>