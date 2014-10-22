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

$text = ($this->task == 'edit' ? JText::_('COM_GROUPS_EDIT') : JText::_('COM_GROUPS_NEW'));

$canDo = GroupsHelper::getActions('group');

JToolBarHelper::title(JText::_('COM_GROUPS').': ' . $text, 'groups.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('group');

JHTML::_('behavior.framework');

// are we using the email gateway for group forum
$params =  JComponentHelper::getParams('com_groups');
$allowEmailResponses = $params->get('email_comment_processing', 0);
$autoEmailResponses  = $params->get('email_member_groupsidcussionemail_autosignup', 0);

if ($this->group->get('discussion_email_autosubscribe', null) == 1
	|| ($this->group->get('discussion_email_autosubscribe', null) == null && $autoEmailResponses))
{
	$autoEmailResponses = 1;
}

// get groups params
$gparams              = new JRegistry($this->group->params);
$membership_control   = $gparams->get('membership_control', 1);
$display_system_users = $gparams->get('display_system_users', 'global');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('item-form');

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if ($('#field-description').val() == '') {
		alert('<?php echo JText::_('COM_GROUPS_ERROR_MISSING_INFORMATION'); ?>');
	} else if ($('#field-cn').val() == '') {
		alert('<?php echo JText::_('COM_GROUPS_ERROR_MISSING_INFORMATION'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getErrors()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_DETAILS'); ?></span></legend>

			<input type="hidden" name="group[gidNumber]" value="<?php echo $this->group->gidNumber; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="task" value="save" />

			<div class="input-wrap">
				<label for="field-type"><?php echo JText::_('COM_GROUPS_TYPE'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<select name="group[type]" id="field-type">
					<option value="1"<?php echo ($this->group->type == '1') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_GROUPS_TYPE_HUB'); ?></option>
					<option value="3"<?php echo ($this->group->type == '3') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_GROUPS_TYPE_SUPER'); ?></option>
				<?php if ($canDo->get('core.admin')) { ?>
					<option value="0"<?php echo ($this->group->type == '0') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_GROUPS_TYPE_SYSTEM'); ?></option>
				<?php } ?>
					<option value="2"<?php echo ($this->group->type == '2') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_GROUPS_TYPE_PROJECT'); ?></option>
					<option value="4"<?php echo ($this->group->type == '4') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_GROUPS_TYPE_COURSE'); ?></option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-cn"><?php echo JText::_('COM_GROUPS_CN'); ?>: <span class="required"><?php echo JText::_('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="group[cn]" id="field-cn" value="<?php echo $this->escape(stripslashes($this->group->cn)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-description"><?php echo JText::_('COM_GROUPS_TITLE'); ?>:</label><br />
				<input type="text" name="group[description]" id="field-description" value="<?php echo $this->escape(stripslashes($this->group->description)); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-logo"><?php echo JText::_('COM_GROUPS_LOGO'); ?>:</label><br />
				<input type="text" name="group[logo]" id="field-logo" value="<?php echo $this->escape($this->group->logo); ?>" />
			</div>
 			<div class="input-wrap" data-hint="<?php echo JText::_('COM_GROUPS_EDIT_PUBLIC_TEXT_HINT'); ?>">
				<label for="field-public_desc"><?php echo JText::_('COM_GROUPS_EDIT_PUBLIC_TEXT'); ?>:</label><br />
				<span class="hint"><?php echo JText::_('COM_GROUPS_EDIT_PUBLIC_TEXT_HINT'); ?></span>
				<?php echo JFactory::getEditor()->display('group[public_desc]', $this->escape(stripslashes($this->group->public_desc)), '', '', '40', '10', false, 'field-public_desc'); ?>
			</div>
			<div class="input-wrap" data-hint="<?php echo JText::_('COM_GROUPS_EDIT_PRIVATE_TEXT_HINT'); ?>">
				<label for="field-private_desc"><?php echo JText::_('COM_GROUPS_EDIT_PRIVATE_TEXT'); ?>:</label><br />
				<span class="hint"><?php echo JText::_('COM_GROUPS_EDIT_PRIVATE_TEXT_HINT'); ?></span>
				<?php echo JFactory::getEditor()->display('group[private_desc]', $this->escape(stripslashes($this->group->private_desc)), '', '', '40', '10', false, 'field-private_desc'); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_GROUPS_ID'); ?></th>
					<td><?php echo $this->escape($this->group->gidNumber); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_GROUPS_PUBLISHED'); ?></th>
					<td><?php echo ($this->group->published) ? 'Yes' : 'No'; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_GROUPS_APPROVED'); ?></th>
					<td><?php echo ($this->group->approved) ? 'Yes' : 'No'; ?></td>
				</tr>
			<?php if ($this->group->created) { ?>
				<tr>
					<th><?php echo JText::_('COM_GROUPS_CREATED'); ?></th>
					<td><?php echo $this->escape(date("l F d, Y @ g:ia", strtotime($this->group->created))); ?></td>
				</tr>
			<?php } ?>
			<?php if ($this->group->created_by) { ?>
				<tr>
					<th><?php echo JText::_('COM_GROUPS_CREATED_BY'); ?></th>
					<td><?php
					$creator = JUser::getInstance($this->group->created_by);
					echo $this->escape($creator->get('name')); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_MEMBERSHIP'); ?></span></legend>

			<div class="input-wrap">
				<input type="checkbox" name="group[params][membership_control]" id="field-membership_control" value="1" <?php if ($membership_control == 1) { ?>checked="checked"<?php } ?> />
				<label for="field-membership_control"><?php echo JText::_('COM_GROUPS_MEMBERSHIP_CONTROL'); ?></label>
			</div>
			<fieldset>
				<legend><?php echo JText::_('COM_GROUPS_JOIN_POLICY'); ?>:</legend>
				<div class="input-wrap">
					<input type="radio" name="group[join_policy]" id="field-join_policy0" value="0"<?php if ($this->group->join_policy == 0) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy0"><?php echo JText::_('COM_GROUPS_JOIN_POLICY_PUBLIC'); ?></label><br />
					<input type="radio" name="group[join_policy]" id="field-join_policy1" value="1"<?php if ($this->group->join_policy == 1) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy1"><?php echo JText::_('COM_GROUPS_JOIN_POLICY_RESTRICTED'); ?></label><br />
					<input type="radio" name="group[join_policy]" id="field-join_policy2" value="2"<?php if ($this->group->join_policy == 2) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy2"><?php echo JText::_('COM_GROUPS_JOIN_POLICY_INVITE'); ?></label><br />
					<input type="radio" name="group[join_policy]" id="field-join_policy3" value="3"<?php if ($this->group->join_policy == 3) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy3"><?php echo JText::_('COM_GROUPS_JOIN_POLICY_CLOSED'); ?></label>
				</div>
			</fieldset>
			<div class="input-wrap">
				<label for="restrict_msg"><?php echo JText::_('COM_GROUPS_EDIT_CREDENTIALS'); ?>:</label><br />
				<?php echo JFactory::getEditor()->display('group[restrict_msg]', $this->escape(stripslashes($this->group->restrict_msg)), '', '', 40, 10, false, 'restrict_msg', null, null, array('class' => 'minimal')); ?>
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_GROUPS_ACCESS'); ?></span></legend>

			<fieldset>
				<legend><?php echo JText::_('COM_GROUPS_DISCOVERABILITY'); ?>:</legend>

				<div class="input-wrap">
					<input type="radio" name="group[discoverability]" id="field-discoverability0" value="0"<?php if ($this->group->discoverability == 0) { echo ' checked="checked"'; } ?> />
					<label for="field-discoverability0"><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_VISIBLE'); ?></label>
					<br />
					<input type="radio" name="group[discoverability]" id="field-discoverability1" value="1"<?php if ($this->group->discoverability == 1) { echo ' checked="checked"'; } ?> />
					<label for="field-discoverability1"><?php echo JText::_('COM_GROUPS_DISCOVERABILITY_HIDDEN'); ?></label>
				</div>
			</fieldset>

			<div class="input-wrap">
				<label for="field-plugins"><?php echo JText::_('COM_GROUPS_PLUGIN_ACCESS'); ?>:</label><br />
				<textarea name="group[plugins]" id="field-plugins" rows="10" cols="50"><?php echo $this->escape($this->group->plugins); ?></textarea>
			</div>

			<div class="input-wrap">
				<label for="display_system_users"><?php echo JText::_('COM_GROUPS_SHOW_SYSTEM_USERS'); ?>:</label><br />
				<select name="group[params][display_system_users]" id="display_system_users">
					<option <?php if ($display_system_users == 'global') { echo 'selected="selected"'; } ?> value="global"><?php echo JText::_('COM_GROUPS_SHOW_SYSTEM_USERS_GLOBAL'); ?></option>
					<option <?php if ($display_system_users == 'no') { echo 'selected="selected"'; } ?> value="no"><?php echo JText::_('COM_GROUPS_SHOW_SYSTEM_USERS_NO'); ?></option>
					<option <?php if ($display_system_users == 'yes') { echo 'selected="selected"'; } ?> value="yes"><?php echo JText::_('COM_GROUPS_SHOW_SYSTEM_USERS_YES'); ?></option>
				</select>
			</div>
		</fieldset>

		<?php if ($allowEmailResponses) : ?>
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('COM_GROUPS_EMAIL_SETTINGS'); ?></span></legend>

				<fieldset>
					<legend><?php echo JText::_('COM_GROUPS_DISCUSSION_EMAILS'); ?>:</legend>

					<div class="input-wrap">
						<input type="hidden" name="group[discussion_email_autosubscribe]" value="0" />
						<input type="checkbox" name="group[discussion_email_autosubscribe]" id="field-membership_control" value="1" <?php if ($autoEmailResponses == 1) { ?>checked="checked"<?php } ?> />
						<label for="field-membership_control"><?php echo JText::_('COM_GROUPS_DISCUSSION_EMAIL_AUTOSUBSCRIBE'); ?></label>
					</div>
				</fieldset>
			</fieldset>
		<?php endif; ?>
	</div>
	<div class="clr"></div>

	<?php /*if ($canDo->get('core.admin')): ?>
	<div class="col width-100 fltlft">
		<fieldset class="panelform">
			<legend><span><?php echo JText::_('COM_GROUPS_FIELDSET_RULES'); ?></span></legend>
			<?php echo $this->form->getLabel('rules'); ?>
			<?php echo $this->form->getInput('rules'); ?>
		</fieldset>
	</div>
	<div class="clr"></div>
	<?php endif;*/ ?>

	<?php echo JHTML::_('form.token'); ?>
</form>
