<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$text = ( $this->task == 'edit' ? JText::_( 'EDIT_XPOLL' ) : JText::_( 'NEW_XPOLL' ) );

JToolBarHelper::title( JText::_( 'XPOLL_MANAGER' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	// Do field validation
	if ($('polltitle').value == "") {
		alert( "<?php echo JText::_( 'POLL_MUST_HAVE_A_TITLE', true ); ?>" );
	} else if ( isNaN( parseInt( $('polllag').value ) ) ) {
		alert( "<?php echo JText::_( 'POLL_MUST_HAVE_A_NON-ZERO_LAG_TIME', true ); ?>" );
	} else {
		submitform( pressbutton );
	}
}
</script>
<form action="index.php" method="post" name="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('PARAMETERS'); ?></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><?php echo JText::_('POLL_TITLE'); ?>:</td>
						<td><input type="text" name="poll[title]" id="polltitle" size="60" value="<?php echo htmlspecialchars( stripslashes($this->row->title), ENT_QUOTES ); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('LAG'); ?>:</td>
						<td><input type="text" name="poll[lag]" id="polllag" size="10" value="<?php echo $this->row->lag; ?>" /> <?php echo JText::_('SECONDS_BETWEEN_VOTES'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('OPTIONS'); ?></legend>

			<table class="admintable">
				<tbody>
			<?php
					for ($i=0, $n=count( $this->options ); $i < $n; $i++ ) {
			?>
					<tr>
						<td class="key"><?php echo ($i+1); ?></td>
						<td><input type="text" name="polloption[<?php echo $this->options[$i]->id; ?>]" value="<?php echo htmlspecialchars( stripslashes($this->options[$i]->text), ENT_QUOTES ); ?>" size="60" /></td>
					</tr>
			<?php	
					}
					for (; $i < 12; $i++) {
			?>
					<tr>
						<td class="key"><?php echo ($i+1); ?></td>
						<td><input type="text" name="polloption[]" value="" size="60" /></td>
					</tr>
			<?php	
					}
			?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<fieldset class="adminform">
			<legend><?php echo JText::_('SHOW_ON_MENU_ITEMS'); ?>:</legend>

			<?php echo $this->lists['select']; ?>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="poll[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
