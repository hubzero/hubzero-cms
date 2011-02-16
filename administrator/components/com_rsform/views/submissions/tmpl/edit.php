<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<table class="admintable">
		<?php foreach ($this->staticHeaders as $header) { ?>
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key">
				<span class="hasTip" title="<?php echo JText::_('RSFP_'.$header); ?>">
					<?php echo JText::_('RSFP_'.$header); ?>
				</span>
			</td>
			<td>
				<input class="inputbox" type="text" name="formStatic[<?php echo $header; ?>]" value="<?php echo $this->staticFields->$header; ?>" size="105" />
			</td>
		</tr>
		<?php } ?>
		<?php foreach ($this->fields as $field) { ?>
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key">
				<span class="hasTip" title="<?php echo $field[0]; ?>">
					<?php echo $field[0]; ?>
				</span>
			</td>
			<td>
				<?php echo $field[1]; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<input type="hidden" name="option" value="com_rsform">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="cid" value="<?php echo $this->submissionId; ?>">
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>">
</form>
<?php JHTML::_('behavior.keepalive'); ?>