<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'CJ DATA SOURCE' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ ADDITIONAL DATA SOURCE' ); ?>::<?php echo JText::_('CJ ADDITIONAL DATA SOURCE DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ ADDITIONAL DATA SOURCE' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['additional_data_source']; ?>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
