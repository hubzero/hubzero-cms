<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

$text = ( $this->task == 'edittype' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( '<a href="index.php?option=com_resources&task=types">'.JText::_( 'Job Types' ).'</a>: <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savetype');
JToolBarHelper::cancel('canceltype');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	
	if (pressbutton == 'canceltype') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if (form.category.value == '') {
		alert( 'Type must have a title' );
	} else {
		submitform( pressbutton );
	}
}
</script>
<p style="color:#FF0000;"><?php echo JText::_('Warning: changing the type title will affect all currently available job postings with this type.'); ?></p>
<form action="index.php" method="post" id="adminForm" name="adminForm">			
	<fieldset class="adminform">
		<legend><?php echo JText::_('Edit type title'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="type"><?php echo JText::_('Type Title'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $this->row->category; ?>" /></td>
				</tr>
			</tbody>
		</table>
	
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="savetype" />
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

