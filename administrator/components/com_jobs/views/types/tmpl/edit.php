<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = JobsHelper::getActions('type');

$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title('<a href="index.php?option=com_jobs&controller=types">' . JText::_('Job Types') . '</a>: <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
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
<?php if ($this->task == 'edit') { ?>
<p class="warning">
	<?php echo JText::_('Warning: changing the type title will affect all currently available job postings with this type.'); ?>
</p>
<?php } ?>
<form action="index.php" method="post" id="item-form" name="adminForm">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('Edit type title'); ?></span></legend>
		
		<table class="admintable">
			<tbody>
				<tr>
					<th class="key"><label for="type"><?php echo JText::_('Type Title'); ?>: <span class="required">*</span></label></th>
					<td><input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" /></td>
				</tr>
			</tbody>
		</table>
	
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>
	<?php echo JHTML::_('form.token'); ?>
</form>