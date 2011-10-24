<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Session Settings' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Session Lifetime' ); ?>::<?php echo JText::_( 'TIPAUTOLOGOUTTIMEOF' ); ?>">
						<?php echo JText::_( 'Session Lifetime' ); ?>
					</span>
				</td>
				<td>
					<input class="text_area" type="text" name="lifetime" size="10" value="<?php echo $row->lifetime; ?>" />
					&nbsp;<?php echo JText::_('minutes'); ?>&nbsp;
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Session Handler' ); ?>::<?php echo JText::_( 'TIPSESSIONHANDLER' ); ?>">
						<?php echo JText::_( 'Session Handler' ); ?>
					</span>
				</td>
				<td>
					<strong><?php echo $lists['session_handlers']; ?></strong>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Session Cookie Domain' ); ?>::<?php echo JText::_( 'TIPSESSIONCOOKIEDOMAIN' ); ?>">
						<?php echo JText::_( 'Session Cookie Domain' ); ?>
					</span>
				</td>
				<td>
					<input type="radio" name="cookiesubdomains" id="cookiesubdomains0" value="0" checked="checked" class="inputbox" /> 
					<label for="cookiesubdomains0">Site</label> 
					<input type="radio" name="cookiesubdomains" id="cookiesubdomains1" value="1" class="inputbox" /> 
					<label for="cookiesubdomains1">All Subdomains</label> 
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
