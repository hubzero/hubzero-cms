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

$tmpl = JRequest::getVar('tmpl', '');

$text = ($this->task == 'edit' ? JText::_('COM_GROUPS_EDIT') : JText::_('COM_GROUPS_NEW'));

$canDo = GroupsHelper::getActions('group');

if ($tmpl != 'component')
{
	JToolBarHelper::title(JText::_('COM_GROUPS').': ' . $text, 'groups.png');
	if ($canDo->get('core.edit'))
	{
		JToolBarHelper::save();
	}
	JToolBarHelper::cancel();
}

JHTML::_('behavior.framework');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if (form.usernames.value == '') {
		alert('<?php echo JText::_('COM_GROUPS_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
	window.top.setTimeout("window.parent.location='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&gid=<?php echo $this->group->get('cn'); ?>'", 700);
}

jQuery(document).ready(function($){
	$(window).on('keypress', function(){
		if (window.event.keyCode == 13) {
			submitbutton('addusers');
		}
	})
});
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="component-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration" >
			<div class="fltrt configuration-options">
				<button type="button" onclick="submitbutton('addusers');"><?php echo JText::_( 'COM_GROUPS_MEMBER_SAVE' );?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo JText::_( 'COM_GROUPS_MEMBER_CANCEL' );?></button>
			</div>
			<?php echo JText::_('COM_GROUPS_MEMBER_ADD') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_DETAILS'); ?></span></legend>

			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
			<input type="hidden" name="task" value="addusers" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="field-usernames"><?php echo JText::_('COM_GROUPS_ADD_USERNAME'); ?>:</label></td>
						<td><input type="text" name="usernames" class="input-username" id="field-usernames" value="" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="field-tbl"><?php echo JText::_('COM_GROUPS_TO'); ?>:</label></td>
						<td>
							<select name="tbl" id="field-tbl">
								<option value="invitees"><?php echo JText::_('COM_GROUPS_INVITEES'); ?></option>
								<option value="applicants"><?php echo JText::_('COM_GROUPS_APPLICANTS'); ?></option>
								<option value="members" selected="selected"><?php echo JText::_('COM_GROUPS_MEMBERS'); ?></option>
								<option value="managers"><?php echo JText::_('COM_GROUPS_MANAGERS'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo JHTML::_('form.token'); ?>
</form>
