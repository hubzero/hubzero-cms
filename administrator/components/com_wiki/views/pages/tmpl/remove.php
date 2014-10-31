<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_('COM_WIKI') . ': ' . JText::_('COM_WIKI_PAGE') . ': ' . JText::_('COM_WIKI_DELETE'), 'wiki.png');
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
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_WIKI_CONFIRM_DELETE'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="confirm" id="confirm" value="1" />
				<label for="confirm"><?php echo JText::_('COM_WIKI_DELETE'); ?></label>
			</div>

			<div class="input-wrap">
				<input type="submit" value="<?php echo JText::_('COM_WIKI_NEXT'); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<p class="warning"><?php echo JText::_('COM_WIKI_DELETE_WARNING'); ?></p>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<?php foreach ($this->ids as $id) { ?>
		<input type="hidden" name="id[]" value="<?php echo $id; ?>" />
	<?php } ?>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>