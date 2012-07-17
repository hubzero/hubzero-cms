<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_('COM_TOOLS').': <small><small>[ '. $text.' ]</small></small>', 'tools.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('item-form');
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('TOOL_DETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
			
			<?php echo JHTML::_( 'form.token' ); ?>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="tooltitle"><?php echo JText::_('TOOL_TITLE'); ?>:</label></td>
						<td><input type="text" name="tooltitle" id="fields[title]" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" size="50" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta" summary="<?php echo JText::_('Metadata for this entry'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('TOOL_ID'); ?>:</th>
					<td><?php echo $this->escape($this->row->id);?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('TOOL_NAME'); ?>:</th>
					<td><?php echo $this->escape(stripslashes($this->row->toolname)); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>