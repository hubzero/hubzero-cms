<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$canDo = XPollHelper::getActions('component');

$text = ($this->task == 'edit' ? JText::_('EDIT_XPOLL') : JText::_('NEW_XPOLL'));

JToolBarHelper::title(JText::_('XPOLL_MANAGER') . ': <small><small>[ ' . $text . ' ]</small></small>', 'poll.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	// Do field validation
	if ($('polltitle').value == "") {
		alert("<?php echo JText::_('POLL_MUST_HAVE_A_TITLE', true); ?>");
	} else if (isNaN(parseInt($('polllag').value))) {
		alert("<?php echo JText::_('POLL_MUST_HAVE_A_NON-ZERO_LAG_TIME', true); ?>");
	} else {
		submitform(pressbutton);
	}
}
</script>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('PARAMETERS'); ?></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><?php echo JText::_('POLL_TITLE'); ?>:</th>
						<td><input type="text" name="poll[title]" id="polltitle" size="60" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><?php echo JText::_('LAG'); ?>:</th>
						<td><input type="text" name="poll[lag]" id="polllag" size="10" value="<?php echo $this->escape($this->row->lag); ?>" /> <?php echo JText::_('SECONDS_BETWEEN_VOTES'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('OPTIONS'); ?></span></legend>

			<table class="admintable">
				<tbody>
			<?php
					for ($i=0, $n=count($this->options); $i < $n; $i++) { 
			?>
					<tr>
						<th class="key"><?php echo ($i+1); ?></th>
						<td><input type="text" name="polloption[<?php echo $this->options[$i]->id; ?>]" value="<?php echo $this->escape(stripslashes($this->options[$i]->text)); ?>" size="60" /></td>
					</tr>
			<?php	
					}
					for (; $i < 12; $i++) { 
			?>
					<tr>
						<th class="key"><?php echo ($i+1); ?></th>
						<td><input type="text" name="polloption[]" value="" size="60" /></td>
					</tr>
			<?php	
					}
			?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('SHOW_ON_MENU_ITEMS'); ?>:</span></legend>

			<?php echo $this->lists['select']; ?>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="poll[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>