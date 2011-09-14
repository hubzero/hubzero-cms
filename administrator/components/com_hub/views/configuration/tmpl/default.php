<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_('HUB Configuration').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
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

	// do field validation
	if (form.name.value == '') {
		alert( 'You must fill in a variable name' );
	} else if (form.value.value == '') {
		alert( 'You must fill in a value' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<h2><?php echo ($this->name) ? 'Edit' : 'New'; ?> Variable</h2>

	<fieldset class="adminform">
		<table class="admintable">
		 <tbody>
		  <tr>
		   <td class="key"><label for="name">Variable:</label></td>
		   <td>
<?php 
		if ($this->name) {
			echo $this->name.' <input type="hidden" name="editname" value="' . $this->name . '" />';
		} else {
			echo '<input type="text" name="name" id="name" size="30" maxlength="250" value="' . $this->name . '" />';
		}
?>
           </td>
		  </tr>
		  <tr>
		   <td style="vertical-align: top;" class="key"><label for="value">Value:</label></td>
		   <td><textarea name="value" id="value" cols="50" rows="15"><?php echo $this->value; ?></textarea></td>
		  </tr>
		 </tbody>
		</table>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<p style="text-align:center;">Note: These variable settings can be overridden with the file <span style="text-decoration:underline;">hubconfiguration-local.php</span></p>
