<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'Edit Host' ) : JText::_( 'New Host' ) );

JToolBarHelper::title( JText::_( 'Tools' ).': <small><small>[ '. $text.' ]</small></small>', 'config.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="name"><?php echo JText::_('Name'); ?>:</label></td>
						<td>
							<input type="text" name="fields[name]" id="name" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="value"><?php echo JText::_('Value'); ?>:</label></td>
						<td>
							<input type="text" name="fields[value]" id="value" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->value)); ?>" />
						</td>
					</tr>
					<tr>
						<td class="key"><label for="description"><?php echo JText::_('Description'); ?>:</label></td>
						<td>
							<input type="text" name="fields[description]" id="description" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50">
		<table class="meta" summary="<?php echo JText::_('Metadata for this item'); ?>">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('Bit#'); ?></th>
					<td><?php echo $this->escape($this->bit); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('References'); ?></th>
					<td><?php echo $this->escape($this->refs); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[status]" value="check" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->name; ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>