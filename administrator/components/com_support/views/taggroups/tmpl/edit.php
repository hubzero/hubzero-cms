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
$text = ( $this->task == 'edit' ? JText::_( 'Edit' ) : JText::_( 'New' ) );
JToolBarHelper::title( JText::_( 'Tag/Group' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'canceltg') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	if (form.tag.value == '') {
		alert( '<?php echo JText::_('TAG_ERROR_NO_TEXT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="tag"><?php echo JText::_('TAG_TEXT'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="tag" id="tag" value="<?php echo $this->tag->tag; ?>" size="50" /></td>
				</tr>
				<tr>
					<td class="key"><label for="group"><?php echo JText::_('GROUP_TEXT'); ?>: <span class="required">*</span></label></td>
					<td><input type="text" name="group" id="group" value="<?php echo (is_object($this->group)) ? $this->escape($this->group->cn) : ''; ?>" size="50" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="taggroup[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="taggroup[tagid]" value="<?php echo $this->row->tagid; ?>" />
	<input type="hidden" name="taggroup[groupid]" value="<?php echo $this->row->groupid; ?>" />
	<input type="hidden" name="taggroup[priority]" value="<?php echo $this->row->priority; ?>" />
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
