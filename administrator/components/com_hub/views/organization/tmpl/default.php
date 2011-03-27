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
$text = ( $this->task == 'editorg' ? JText::_( 'Edit Organization' ) : JText::_( 'New Organization' ) );

JToolBarHelper::title( JText::_( 'HUB Configuration' ).': <small><small>[ '. $text.' ]</small></small>', 'user.png' );
JToolBarHelper::save('saveorg');
JToolBarHelper::cancel('cancelorg');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->org->id; ?>" />
		<input type="hidden" name="task" value="saveorg" />
			
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="organization"><?php echo JText::_('Organization'); ?>:</label></td>
		 			<td><input type="text" name="organization" id="organization" value="<?php echo $this->org->organization; ?>" size="50" /></td>
		 		</tr>
			</tbody>
		</table>
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
