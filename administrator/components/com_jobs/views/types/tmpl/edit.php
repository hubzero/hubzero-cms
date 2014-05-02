<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = JobsHelper::getActions('type');

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Job Types') . ': ' . $text, 'addedit.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('item-form');
	
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	// form field validation
	if (form.category.value == '') {
		alert('Type must have a title');
	} else {
		submitform(pressbutton);
	}
}
</script>
<form action="index.php" method="post" id="item-form" name="adminForm">
	<?php if ($this->task == 'edit') { ?>
	<p class="warning">
		<?php echo JText::_('Warning: changing the type title will affect all currently available job postings with this type.'); ?>
	</p>
	<?php } ?>

	<fieldset class="adminform">
		<legend><span><?php echo JText::_('Edit type title'); ?></span></legend>

		<div class="input-wrap">
			<label for="category"><?php echo JText::_('Type Title'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label>
			<input type="text" name="category" id="category" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" />
		</div>

		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>

	<?php echo JHTML::_('form.token'); ?>
</form>