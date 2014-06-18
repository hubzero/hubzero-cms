<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('COM_WIKI').': '.JText::_('COM_WIKI_PAGE') . ': ' . JText::_('COM_WIKI_DELETE'), 'wiki.png');
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
<form action="index.php" method="post" name="adminForm" class="editform" id="item-form">
	<table class="adminform">
		<tbody>
			<tr>
				<td><input type="radio" name="confirm" id="confirm" value="1" /> <label for="confirm"><?php echo JText::_('COM_WIKI_CONFIRM_DELETE'); ?></label></td>
				<td><input type="submit" name="Submit" value="<?php echo JText::_('COM_WIKI_NEXT'); ?>" /></td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
<?php foreach ($this->ids as $id) { ?>
	<input type="hidden" name="id[]" value="<?php echo $id; ?>" />
<?php } ?>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>