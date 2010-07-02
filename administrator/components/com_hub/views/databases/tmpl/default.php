<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Databases'), 'addedit.png' );
JToolBarHelper::save('savedb');
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

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
			<legend>IP Database</legend>
			<table class="admintable">
				<tbody>
					<?php
					foreach ($this->arr as $field => $value) 
					{
						if (substr($field, 0, strlen('ipDB')) == 'ipDB') {
							?>
					<tr>
						<td class="key"><?php echo str_replace('ipDB', '', $field); ?></td>
						<td>
							<input class="text_area" type="text" name="settings[<?php echo $field; ?>]" size="30" value="<?php echo $value; ?>" />
						</td>
					</tr>
					<?php
						}	
					}
					?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="com_hub" />
	<input type="hidden" name="task" value="savedb" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>