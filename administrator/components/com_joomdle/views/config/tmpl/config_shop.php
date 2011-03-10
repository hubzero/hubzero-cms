<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'CJ SHOP INTEGRATION' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ SHOP INTEGRATION' ); ?>::<?php echo JText::_('CJ SHOP INTEGRATION DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ SHOP INTEGRATION' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['shop_integration']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ SHOP CATEGORY FOR COURSES' ); ?>::<?php echo JText::_('CJ SHOP CATEGORY FOR COURSES DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ SHOP CATEGORY FOR COURSES' ); ?>
				</span>
			</td>
			<td>
				<input name="courses_category" type="text" value="<?php echo $this->comp_params->get ('courses_category'); ?>" size="5">
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ BUY FOR CHILDREN' ); ?>::<?php echo JText::_('CJ BUY FOR CHILDREN DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ BUY FOR CHILDREN' ); ?>
				</span>
			</td>
			<td>
				<?php echo $this->lists['buy_for_children']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ ENROL EMAIL SUBJECT' ); ?>::<?php echo JText::_('CJ ENROL EMAIL SUBJECT DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ ENROL EMAIL SUBJECT' ); ?>
				</span>
			</td>
			<td>
				<input name="enrol_email_subject" type="text" value="<?php echo $this->comp_params->get ('enrol_email_subject'); ?>" size="50">
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ ENROL EMAIL TEXT' ); ?>::<?php echo JText::_('"CJ ENROL EMAIL TEXT DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ ENROL EMAIL TEXT' ); ?>
				</span>
			</td>
			<td>
				<textarea name="enrol_email_text" cols="50" rows="7"><?php echo $this->comp_params->get ('enrol_email_text'); ?></textarea>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
