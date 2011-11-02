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
$text = ( $this->task == 'edittype' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_( 'Citation Type' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save('savetype');
JToolBarHelper::cancel('types');


$id = NULL;
$type = NULL;
$title = NULL;
$desc = NULL;
if($this->type) {
	$id = $this->type->id;
	$type = $this->type->type;
	$title = $this->type->type_title;
	$desc = $this->type->type_desc;
}

?>

<script type="text/javascript">
function submitbutton(pressbutton) 
{
	return submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-70">
		<fieldset class="adminform">
			<legend><?php echo JText::_('Citation Type'); ?></legend>
			<table class="admintable">
				<tbody>
					<?php if($id) : ?>
						<tr>
							<td class="key">Type ID</td>
							<td><?php echo $id; ?><input type="hidden" name="type[id]" value="<?php echo $id; ?>" /></td>
						</tr>
					<?php endif ;?>
					<tr>
						<td class="key">Type Alias</td>
						<td><input type="text" name="type[type]" value="<?php echo $type; ?>" size="50" /></td>
					</tr>
					<tr>
						<td class="key">Type Title</td>
						<td><input type="text" name="type[type_title]" value="<?php echo $title; ?>" size="100" /></td>
					</tr>
					<tr>
						<td class="key">Type Description</td>
						<td><textarea name="type[type_desc]" rows="5" cols="58"><?php echo $desc; ?></textarea></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savetype" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
