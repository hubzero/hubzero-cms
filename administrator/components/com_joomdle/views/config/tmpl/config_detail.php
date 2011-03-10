<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'CJ DETAIL VIEW' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ SHOW TOPICS LINKS' ); ?>::<?php echo JText::_('CJ SHOW TOPICS LINKS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ SHOW TOPICS LINKS' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_topìcs_link']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ SHOW GRADING SYSTEM LINKS' ); ?>::<?php echo JText::_('CJ SHOW GRADING SYSTEM LINKS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ SHOW GRADING SYSTEM LINKS' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_grading_system_link']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ SHOW TEACHERS LINKS' ); ?>::<?php echo JText::_('CJ SHOW TEACHERS LINKS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ SHOW TEACHERS LINKS' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_teachers_link']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ SHOW ENROL LINKS' ); ?>::<?php echo JText::_('CJ SHOW ENROL LINKS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ SHOW ENROL LINKS' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_enrol_link']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ SHOW PAYPAL BUTTON' ); ?>::<?php echo JText::_('CJ SHOW PAYPAL BUTTON DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ SHOW PAYPAL BUTTON' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['show_paypal_button']; ?>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
