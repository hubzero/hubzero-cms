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
?>

<div id="link-existing">
	<p class="passed">
		You've logged in successfully with your <?php echo ucfirst($this->hzad->authenticator); ?> account, 
		but it doesn't seem to be linked to a current hub account. You can:
	</p>

	<div id="option1-link-existing" class="options">
		<div class="clickable">Log in with an existing <?php echo $this->sitename; ?> account, 
			and link your <?php echo ucfirst($this->hzad->authenticator); ?> account to it
		</div>
		<div id="option1-inner" class="inner">

<?php if($this->conflict) { ?>
			<p>
				<span class="important">Is one of these you?</span> Based on your email address, we found the following accounts that may be yours.
				Click one of them to login with that existing account
				and link it up with your <?php echo ucfirst($this->hzad->authenticator); ?> account.
			</p>

			<div id="account-suggestions">

<?php foreach($this->username as $u)
{
		$user_id  = JUserHelper::getUserId($u);
		$user     = JFactory::getUser($u);
		$pname    = (Hubzero_Auth_Link::find_by_user_id($user->id)) ? Hubzero_Auth_Link::find_by_user_id($user->id) : array(array("auth_domain_name" => 'hubzero'));

		foreach($pname as $p) { ?>
			<a href="<?php echo JRoute::_('/logout?return=' .
				base64_encode(JRoute::_('/login?authenticator=' . $p['auth_domain_name'] . '&return=' .
				base64_encode(JRoute::_('/login?authenticator=' . $this->hzad->authenticator))))); ?>">
				<div class="account-group">
					<div class="auth_link_icon" id="<?php echo $p['auth_domain_name']; ?>"></div>
					<p>
						<span class="user-icon"><?php echo $user->name; ?></span><br />
						<span class="email-icon"><?php echo $user->email; ?></span>
					</p>
				</div>
			</a>
<?php } // close foreach pname
} // close foreach $this->username ?>
			</div><!-- / #account-suggestions -->
<?php } // close if $this->conflict ?>

			<div class="clear"></div>

			<div id="other-links">
				<p>
					<span class="important">Have another account<?php echo ($this->conflict) ? ", but don't see it above" : ''; ?>?
					</span> Choose one of the following that best matches your scenario:
				</p>

<?php foreach($this->plugins as $plugin) {
	$name = ($plugin->name == 'hubzero') ? 'local' : $plugin->name;
	if($plugin->name != $this->hzad->authenticator) {
		echo '<a href="' . JRoute::_('/logout?return=' .
			base64_encode(JRoute::_('/login?authenticator=' . $plugin->name . '&return=' .
			base64_encode(JRoute::_('/login?authenticator=' . $this->hzad->authenticator))))) .
			'">My current ' . $this->sitename . ' account was setup using a ' . $name  . ' account</a><br />';
	}
} ?>

			</div><!-- / #other-links -->
		</div><!-- / #option1-inner -->
	</div><!-- / #option1-link-existing -->

	<div id="option2-create-new" class="options">
		<div class="clickable">Create a new account using your <?php echo ucfirst($this->hzad->authenticator); ?> identity</div>
		<div id="option2-inner" class="inner">
			<a id="new-account" href="<?php echo JRoute::_('index.php?option=com_register&task=update'); ?>">Create a new account</a>
		</div>
	</div>

</div><!-- / #link-existing -->
<div class="clear"></div>