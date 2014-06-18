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

$canDo = MembersHelper::getActions('component');

JToolBarHelper::title(JText::_('MEMBER') . ': <small><small>[ ' . JText::_('NEW') . ' ]</small></small>', 'user.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save('new');
}
JToolBarHelper::cancel();

?>
<script type="text/javascript">
	function submitbutton(pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}

		// do field validation
		submitform(pressbutton);
	}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-100 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('MEMBERS_PROFILE'); ?></span></legend>

			<input type="hidden" name="option" value="<?php echo $this->option ?>" />
			<input type="hidden" name="task" value="edit" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="username"><?php echo JText::_('COL_USERNAME'); ?>:</label></td>
						<td><input type="text" name="profile[username]" id="username" /></td>
					</tr>
					<tr>
						<td class="key"><label for="email"><?php echo JText::_('COL_EMAIL'); ?>:</label></td>
						<td><input type="text" name="profile[email]" id="email" /></td>
					</tr>
					<tr>
						<td class="key"><label for="password"><?php echo JText::_('COL_PASSWORD'); ?>:</label></td>
						<td><input type="text" name="profile[password]" id="password" /></td>
					</tr>
					<tr>
						<td class="key"><label for="givenName"><?php echo JText::_('FIRST_NAME'); ?>:</label></td>
						<td><input type="text" name="profile[givenName]" id="givenName" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="middleName"><?php echo JText::_('MIDDLE_NAME'); ?>:</label></td>
						<td><input type="text" name="profile[middleName]" id="middleName" size="50" /></td>
					</tr>
					<tr>
						<td class="key"><label for="surname"><?php echo JText::_('LAST_NAME'); ?>:</label></td>
						<td><input type="text" name="profile[surname]" id="surname" size="50" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<?php echo JHTML::_('form.token'); ?>
	</div>
</form>