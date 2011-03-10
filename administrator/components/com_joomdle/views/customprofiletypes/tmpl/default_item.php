<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JToolBarHelper::save('save_mapping');

?>
<form action="index.php" method="post" name="adminForm" autocomplete="off">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'CJ DATA MAPPING CONFIGURATION' ); ?></legend>
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
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ JOOMLA FIELD' ); ?>::<?php echo JText::_('CJ JOOMLA FIELD DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ JOOMLA FIELD' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['joomla_field']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ MOODLE FIELD' ); ?>::<?php echo JText::_('CJ MOODLE FIELD DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ MOODLE FIELD' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['moodle_field']; ?>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
  <input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' );?>"/>
       <input type="hidden" name="task" value=""/>
       <input type="hidden" name="boxchecked" value="0"/>
       <input type="hidden" name="hidemainmenu" value="0"/>
       <input type="hidden" name="mapping_id" value="<?php echo $this->mapping_id ?>"/>
       <input type="hidden" name="joomla_app" value="<?php echo $this->joomla_app ?>"/>
       <?php echo JHTML::_( 'form.token' ); ?>

</form>
