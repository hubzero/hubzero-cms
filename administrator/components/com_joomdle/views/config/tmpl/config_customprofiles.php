<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'CJ CUSTOM PROFILES TYPES' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ USE XIPT INTEGRATION' ); ?>::<?php echo JText::_('CJ USE XIPT INTEGRATION DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ USE XIPT INTEGRATION' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['use_xipt_integration']; ?>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
