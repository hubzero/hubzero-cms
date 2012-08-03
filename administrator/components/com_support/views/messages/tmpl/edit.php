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
JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '. $text.' ]</small></small>', 'support.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('messages.html', true);

$jconfig =& JFactory::getConfig();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancelmsg') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if ($('msg[message]').value == '') {
		alert( '<?php echo JText::_('MESSAGE_ERROR_NO_TEXT'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('MESSAGE_LEGEND'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="title"><?php echo JText::_('MESSAGE_SUMMARY'); ?>: <span class="required">*</span></label></td>
						<td><input type="text" name="msg[title]" id="title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" size="50" /></td>
					</tr>
		 			<tr>
						<td class="key" style="vertical-align: top;"><label for="message"><?php echo JText::_('MESSAGE_TEXT'); ?>: <span class="required">*</span></label></th>
						<td><?php echo $editor->display('msg[message]', $this->escape(stripslashes($this->row->message)), '', '', '50', '10'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<p><?php echo JText::_('MESSAGE_TEXT_EXPLANATION'); ?></p>
		<dl>
			<dt>{ticket#}</dt>
			<dd><?php echo JText::_('MESSAGE_TICKET_NUM_EXPLANATION'); ?></dd>
			
			<dt>{sitename}</dt>
			<dd><?php echo $jconfig->getValue('config.sitename'); ?></dd>
			
			<dt>{siteemail}</dt>
			<dd><?php echo $jconfig->getValue('config.mailfrom'); ?></dd>
		</dl>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="msg[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>