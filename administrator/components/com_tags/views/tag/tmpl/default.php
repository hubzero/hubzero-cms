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

$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_( 'TAGS' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

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
	if (form.raw_tag.value == '') {
		alert( '<?php echo JText::_('ERROR_EMPTY_TAG'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<?php
if ($this->getError()) {
	echo '<p>ERROR: '.$this->getError().'</p>';
}
?>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="admin"><?php echo JText::_('ADMIN'); ?>:</label></td>
						<td><input type="checkbox" name="admin" id="admin" value="1" <?php if ($this->tag->admin == 1) { echo 'checked="checked"'; } ?> /></td>
					</tr>
					<tr>
						<td class="key"><label for="raw_tag"><?php echo JText::_('TAG'); ?>:</label></td>
						<td><input type="text" name="raw_tag" id="raw_tag" size="30" maxlength="250" value="<?php echo htmlentities(stripslashes($this->tag->raw_tag), ENT_COMPAT, 'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="alias"><?php echo JText::_('ALIAS'); ?>:</label></td>
						<td><input type="text" name="alias" id="alias" size="30" maxlength="250" value="<?php echo $this->tag->alias; ?>" /></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align:top;"><label><?php echo JText::_('DESCRIPTION'); ?>:</label></td>
						<td><?php echo $editor->display('description', stripslashes($this->tag->description), '360px', '200px', '50', '10'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50">
		<p><?php echo JText::_('NORMALIZED_EXPLANATION'); ?></p>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="id" value="<?php echo $this->tag->id; ?>" />
	<input type="hidden" name="tag" value="<?php echo $this->tag->tag; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

