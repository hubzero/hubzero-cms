<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'CJ LINKS BEHAVIOUR' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ OPEN MOODLE LINKS' ); ?>::<?php echo JText::_('CJ OPEN MOODLE LINKS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ OPEN MOODLE LINKS' ); ?>
				</span>
			</td>
			<td>
			<?php echo $this->lists['linkstarget']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ WRAPPER SCROLL BARS' ); ?>::<?php echo JText::_('CJ WRAPPER SCROLL BARS DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ WRAPPER SCROLL BARS' ); ?>
				</span>
			</td>
			<td>
			<?php echo $this->lists['scrolling']; ?>
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ WRAPPER WIDTH' ); ?>::<?php echo JText::_('CJ WRAPPER WIDTH DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ WRAPPER WIDTH' ); ?>
				</span>
			</td>
			<td>
			<input name="width" type="text" value="<?php echo $this->comp_params->get ('width'); ?>" size="5">
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ WRAPPER HEIGHT' ); ?>::<?php echo JText::_('CJ WRAPPER HEIGHT DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ WRAPPER HEIGHT' ); ?>
				</span>
			</td>
			<td>
			<input name="height" type="text" value="<?php echo $this->comp_params->get ('height'); ?>" size="5">
			</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ WRAPPER AUTOHEIGHT' ); ?>::<?php echo JText::_('CJ WRAPPER AUTOHEIGHT DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ WRAPPER AUTOHEIGHT' ); ?>
				</span>
			</td>
                        <td>
                        <?php echo $this->lists['autoheight']; ?>
                        </td>
						<td>
						<b>Note:</b> This does not work with Moodle 2.0 (yet)
						</td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ WRAPPER TRANSPARENCY' ); ?>::<?php echo JText::_('CJ WRAPPER TRANSPARENCY DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ WRAPPER TRANSPARENCY' ); ?>
				</span>
			</td>
                        <td>
                        <?php echo $this->lists['transparency']; ?>
                        </td>
		</tr>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'CJ DEFAULT ITEMID' ); ?>::<?php echo JText::_('CJ DEFAULT ITEMID DESCRIPTION'); ?>">
					<?php echo JText::_( 'CJ DEFAULT ITEMID' ); ?>
				</span>
			</td>
			<td>
			<input name="default_itemid" type="text" value="<?php echo $this->comp_params->get ('default_itemid'); ?>" size="3">
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
