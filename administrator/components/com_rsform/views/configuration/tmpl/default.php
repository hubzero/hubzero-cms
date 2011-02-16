<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	
	if (pressbutton == 'configuration.cancel')
	{
		submitform(pressbutton);
		return;
	}
	else
	{
		var dt = $('configuration').getElements('dt');
		for (var i=0; i<dt.length; i++)
		{
			if (dt[i].className.indexOf('open') != -1)
				$('tabposition').value = i;
		}
		submitform(pressbutton);
	}
}

<?php if (RSFormProHelper::isJ16()) { ?>
	Joomla.submitbutton = submitbutton;
<?php } ?>
</script>

<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">
	<?php echo $this->tabs->startPane('configuration'); ?>
	<?php echo $this->tabs->startPanel(JText::_('RSFP_CONFIG'), 'config'); ?>
	<table class="admintable">
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RSFP_CODE_DESC'); ?>">
					<?php echo JText::_('RSFP_CODE'); ?>
				</span>
			</td>
			<td>
				<input name="rsformConfig[global.register.code]" value="<?php echo $this->code; ?>" size="100" maxlength="50" /><br />
			</td>
		</tr>
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RSFP_DEBUG_DESC'); ?>">
					<?php echo JText::_('RSFP_DEBUG'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['global.debug.mode']; ?>
			</td>
		</tr>
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RSFP_IIS_DESC'); ?>">
					<?php echo JText::_('RSFP_IIS'); ?>
				</span>
				
			</td>
			<td>
				<?php echo $this->lists['global.iis']; ?>
			</td>
		</tr>
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RSFP_EDITOR_DESC'); ?>">
					<?php echo JText::_('RSFP_EDITOR'); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['global.editor']; ?>
			</td>
		</tr>
	</table>
	<?php
	echo $this->tabs->endPanel();
	$this->triggerEvent('rsfp_bk_onAfterShowConfigurationTabs');
	echo $this->tabs->endPane();
	?>
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tabposition" id="tabposition" value="0" />
</form>