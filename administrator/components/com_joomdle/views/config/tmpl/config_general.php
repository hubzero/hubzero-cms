<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'CJ GENERAL CONFIG' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ MOODLE SERVER URL' ); ?>::<?php echo JText::_('CJ MOODLE SERVER URL DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ MOODLE SERVER URL' ); ?>
				</span>
			</td>
			<td>
				<input name="MOODLE_URL" type="text" value="<?php echo $this->comp_params->get ('MOODLE_URL'); ?>" size="50">
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ MOODLE VERSION' ); ?>::<?php echo JText::_('CJ MOODLE VERSION DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ MOODLE VERSION' ); ?>
				</span>
			</td>
			<td>
			<?php echo $this->lists['moodle_version']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ MOODLE AUTH TOKEN' ); ?>::<?php echo JText::_('CJ MOODLE AUTH TOKEN DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ MOODLE AUTH TOKEN' ); ?>
				</span>
			</td>
			<td>
				<input name="auth_token" type="text" value="<?php echo $this->comp_params->get ('auth_token'); ?>" size="50">
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ CONNECTION METHOD' ); ?>::<?php echo JText::_('CJ CONNECTION METHOD DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ CONNECTION METHOD' ); ?>
				</span>
			</td>
			<td>
			<?php echo $this->lists['connection_method']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ AUTO CREATE USERS' ); ?>::<?php echo JText::_('CJ AUTO CREATE USERS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ AUTO CREATE USERS' ); ?>
				</span>
			</td>
			<td>
			<?php echo $this->lists['auto_create_users']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ AUTO DELETE USERS' ); ?>::<?php echo JText::_('CJ AUTO DELETE USERS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ AUTO DELETE USERS' ); ?>
				</span>
			</td>
			<td>
			<?php echo $this->lists['auto_delete_users']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ AUTO LOGIN USERS' ); ?>::<?php echo JText::_('CJ AUTO LOGIN USERS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ AUTO LOGIN USERS' ); ?>
				</span>
			</td>
			<td>
			<?php echo $this->lists['auto_login_users']; ?>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
