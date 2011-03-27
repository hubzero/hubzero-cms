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
$text = ( $this->task == 'editpage' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_('Wiki').': '.JText::_('Page').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save( 'savepage', JText::_('SAVE') );
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton =='resethits') {
		if (confirm( <?php echo JText::_('RESET_HITS_WARNING'); ?> )){
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if ($('pagetitle').value == '') {
		alert( <?php echo JText::_('ERROR_MISSING_TITLE'); ?> );
	} else if ($('pagename').value == '') {
		alert( <?php echo JText::_('ERROR_MISSING_PAGENAME'); ?> );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" class="editform">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('DETAILS'); ?></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label><?php echo JText::_('Created by'); ?>:</label></td>
						<td><input type="hidden" name="page[created_by]" id="pagecreatedby" value="<?php echo $this->row->created_by; ?>" /><?php echo stripslashes($this->creator->get('name')); ?> (<?php echo $this->creator->get('username'); ?>)</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Title'); ?>:</label></td>
						<td><input type="text" name="page[title]" id="pagetitle" size="30" maxlength="255" value="<?php echo htmlentities(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Pagename'); ?>:</label></td>
						<td><input type="text" name="page[pagename]" id="pagename" size="30" maxlength="255" value="<?php echo stripslashes($this->row->pagename); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Scope'); ?>:</label></td>
						<td><input type="text" name="page[scope]" id="pagescope" size="30" maxlength="255" value="<?php echo stripslashes($this->row->scope); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Group'); ?>:</label></td>
						<td><input type="text" name="page[group]" id="pagegroup" size="30" maxlength="255" value="<?php echo stripslashes($this->row->group); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('Revisions'); ?>:</label></td>
						<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=revisions&amp;pageid=<?php echo $this->row->id; ?>" title="<?php echo JText::_('View revisions'); ?>"><?php echo $this->row->getRevisionCount(); ?> revisions</a></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Hits'); ?>:</td>
						<td><input type="text" name="page[hits]" id="pagehits" size="11" maxlength="255" value="<?php echo $this->row->hits; ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<fieldset class="adminform">
			<legend><?php echo JText::_('PARAMETERS'); ?></legend>
			
			<?php 
			$params =& new JParameter( $this->row->params, JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->option.DS.'wiki.xml' );
			echo $params->render();
			?>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="authors"><?php echo JText::_('Authors'); ?>:</label></td>
						<td><input type="text" name="page[authors]" id="pageauthors" value="<?php echo htmlentities($this->row->authors); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="state"><?php echo JText::_('Locked'); ?>:</label></td>
						<td><input type="checkbox" name="page[state]" id="pagestate" value="1" <?php echo $this->row->state ? 'checked="checked"' : ''; ?> /> <?php echo JText::_('Only admins/group managers can edit'); ?></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align: top;"><label><?php echo JText::_('Access Level'); ?>:</label></td>
						<td><?php echo JHTML::_('list.accesslevel', $this->row); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="page[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savepage" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
