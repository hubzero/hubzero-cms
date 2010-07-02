<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Site'), 'addedit.png' );
JToolBarHelper::preferences('com_hub', '550');
JToolBarHelper::save('savesite');
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
		<legend>Site Settings</legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
							Short Name
						</span>
					</td>
					<td>
						<input class="text_area" type="text" name="settings[hubShortName]" size="30" value="<?php echo (isset($this->arr['hubShortName'])) ? $this->arr['hubShortName'] : ''; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
							Short URL
						</span>
					</td>
					<td>
						<input class="text_area" type="text" name="settings[hubShortURL]" size="30" value="<?php echo (isset($this->arr['hubShortURL'])) ? $this->arr['hubShortURL'] : ''; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
							Long URL
						</span>
					</td>
					<td>
						<input class="text_area" type="text" name="settings[hubLongURL]" size="30" value="<?php echo (isset($this->arr['hubLongURL'])) ? $this->arr['hubLongURL'] : ''; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
							Support Email
						</span>
					</td>
					<td>
						<input class="text_area" type="text" name="settings[hubSupportEmail]" size="30" value="<?php echo (isset($this->arr['hubSupportEmail'])) ? $this->arr['hubSupportEmail'] : ''; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
							Monitor Email
						</span>
					</td>
					<td>
						<input class="text_area" type="text" name="settings[hubMonitorEmail]" size="30" value="<?php echo (isset($this->arr['hubMonitorEmail'])) ? $this->arr['hubMonitorEmail'] : ''; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="editlinktip hasTip" title="Site Name::Enter the name of your web site. This will be used in various locations for example, the Back-end browser title bar and <em>Site Offline</em> pages.">
							Home Dir
						</span>
					</td>
					<td>
						<input class="text_area" type="text" name="settings[hubHomeDir]" size="30" value="<?php echo (isset($this->arr['hubHomeDir'])) ? $this->arr['hubHomeDir'] : ''; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend>Forge settings</legend>
		<table class="admintable">
			<tbody>
<?php
foreach ($this->arr as $field => $value) 
{
if (substr($field, 0, strlen('forge')) == 'forge') {
?>
<tr>
	<td class="key"><?php echo str_replace('forge', '', $field); ?></td>
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
	<div class="col width-50">
		<fieldset class="adminform">
			<legend>LDAP Settings</legend>
			<table class="admintable">
				<tbody>
<?php
foreach ($this->arr as $field => $value) 
{
if (substr($field, 0, strlen('hubLDAP')) == 'hubLDAP') {
	?>
	<tr>
		<td class="key"><?php echo str_replace('hubLDAP', '', $field); ?></td>
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
	<input type="hidden" name="task" value="savesite" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>