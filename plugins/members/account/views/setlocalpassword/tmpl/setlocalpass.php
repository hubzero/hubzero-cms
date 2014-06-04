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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
     ->css('providers.css', 'com_users')
     ->js()
     ->js('jquery.hoverIntent', 'system');
?>

<h3 class="section-header"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_SET_LOCAL_PASSWORD'); ?></h3>

<?php if (isset($this->notifications) && count($this->notifications) > 0) {
	foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } // close foreach
} // close if count ?>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php endif; ?>

<div id="members-account-section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option .
										'&id=' . $this->id .
										'&active=account' .
										'&task=setlocalpass'); ?>" method="post">
		<fieldset>
			<legend><?php echo JText::_('PLG_MEMBERS_ACCOUNT_SET_LOCAL_PASSWORD'); ?></legend>

			<div class="clear"></div>

			<div class="fieldset-grouping">
				<p class="error" id="section-edit-errors"></p>
			</div>

			<div id="password-group"<?php echo (count($this->password_rules) > 0) ? ' class="split-left"' : ""; ?>>
				<div class="fieldset-grouping">
					<label for="password1"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_PASSWORD'); ?>:</label>
					<input id="password1" name="password1" type="password" />
				</div>
				<div class="fieldset-grouping">
					<label for="password2"><?php echo JText::_('PLG_MEMBERS_ACCOUNT_VERIFY_PASSWORD'); ?>:</label>
					<input id="password2" name="password2" type="password" />
				</div>
			</div>

			<?php
				if (count($this->password_rules) > 0)
				{
					echo '<div id="passrules-container" class="setlocal">';
					echo '<div id="passrules-subcontainer">';
					echo '<h5>Password Rules</h5>';
					echo '<ul id="passrules">';
					foreach ($this->password_rules as $rule)
					{
						if (!empty($rule))
						{
							if (!empty($this->change) && is_array($this->change))
							{
								$err = in_array($rule, $this->change);
							}
							else
							{
								$err = '';
							}
							$mclass = ($err)  ? ' class="error"' : ' class="empty"';
							echo "<li $mclass>".$rule."</li>";
						}
					}
					if (!empty($this->change) && is_array($this->change))
					{
						foreach ($this->change as $msg)
						{
							if (!in_array($msg, $this->password_rules))
							{
								echo '<li class="error">'.$msg."</li>";
							}
						}
					}
					echo "</ul>";
					echo "</div>";
					echo "</div>";
				}
			?>

		</fieldset>

		<div class="clear"></div>
		<p class="submit">
			<input type="hidden" name="change" value="1" />
			<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_ACCOUNT_SUBMIT'); ?>" id="password-change-save" />
			<input type="hidden" name="no_html" id="pass_no_html" value="0" />
			<input type="hidden" name="redirect" id="pass_redirect" value="1" />
		</p>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
<div class="clear"></div>