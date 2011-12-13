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
defined('_JEXEC') or die('Restricted access');
$text = ($this->task == 'editcollection' ? JText::_('BILLBOARDS_COLLECTION_EDIT') : JText::_('BILLBOARDS_COLLECTION_NEW'));
JToolBarHelper::title(JText::_('BILLBOARDS_MANAGER') . ': <small><small>[ ' . $text . ' ]</small></small>', 'addedit.png');
JToolBarHelper::save('savecollection');
JToolBarHelper::cancel('cancelcollection');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancelcollection') {
		submitformpressbutton);
		return;
	}
	
	// form field validation
	if ($('name').value == '') {
		alert('<?php echo JText::_('BILLBOARDS_ERROR_COLLECTION_NO_NAME'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="name"><?php echo JText::_('BILLBOARD_COLLECTION_NAME'); ?>:</label></td>
					<td><input type="text" name="collection[name]" id="name" value="<?php echo htmlentities(stripslashes($this->row->name), ENT_QUOTES); ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="collection[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savesec" />

	<?php echo JHTML::_('form.token'); ?>
</form>
