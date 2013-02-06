<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Members') . '</a>: <small><small>[ '.JText::_('Dashboard').': ' . JText::_('Push Module to Users') . ' ]</small></small>', 'user.png');
JToolBarHelper::save('push');
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
	// do field validation
	submitform( pressbutton );
}
</script>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=manage&amp;plugin=dashboard" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="manage" />
	<input type="hidden" name="plugin" value="dashboard" />
	<input type="hidden" name="action" value="push" />

	<p class="warning"><strong>Warning!</strong> This can be a resource intensive process and should not be performed frequently.</p>
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="module">Module:</label></td>
					<td>
						<select name="module" id="module">
							<option value="">Select...</option>
							<?php
							foreach ($this->modules as $module) 
							{
								echo '<option value="' . $module->id . '">' . $this->escape(stripslashes($module->title)) . '</option>' . "\n";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="column">Column:</label></td>
					<td>
						<select name="column" id="column">
							<option value="0">One</option>
							<option value="1">Two</option>
							<option value="2">Three</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="position">Position:</label></td>
					<td>
						<select name="position" id="position">
							<option value="first">First</option>
							<option value="last">Last</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>