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
JToolBarHelper::title( '<a href="index.php?option='.$this->option.'">'.JText::_('Wiki').'</a>: '.JText::_('Delete page(s)'), 'addedit.png' );
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
}
</script>
<form action="index.php" method="post" name="adminForm" class="editform">
	<table class="adminform">
		<tbody>
			<tr>
				<td><input type="radio" name="confirm" id="confirm" value="1" /> <label for="confirm"><?php echo JText::_('Confirm delete'); ?></label></td>
				<td><input type="submit" name="Submit" value="<?php echo JText::_('NEXT'); ?>" /></td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
<?php foreach ($this->ids as $id) { ?>
	<input type="hidden" name="id[]" value="<?php echo $id; ?>" />
<?php } ?>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="pageid" value="<?php echo $this->pageid; ?>">
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
