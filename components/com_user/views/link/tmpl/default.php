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

<div id="content-header" class="full">
	<h2>Connect With</h2>
</div>

<div class="main section" id="link-existing">
	<p class="info">
		You have successfully connected to <?php echo $this->sitename; ?> with your <?php echo $this->display_name; ?> account.<br />
		BUT, it isn't currently linked to an existing <?php echo $this->sitename; ?> account.  To proceed, either:
	</p>

	<div id="option1-link-existing" class="options">
		<div class="clickable">Log in with an existing <?php echo $this->sitename; ?> account, 
			and link your <?php echo $this->display_name; ?> account to it
		</div>
		<div id="option1-inner" class="inner-content">

<?php if($this->conflict) { ?>
			<p>
				<span class="important">Is one of these you?</span> Based on your email address, we found the following accounts that may be yours.
				Click one of them to login with that existing account
				and link it up with your <?php echo $this->display_name; ?> account.
			</p>

			<div id="account-suggestions">
<?php foreach($this->conflict as $c) { ?>
				<a href="<?php echo JRoute::_('/logout?return=' .
					base64_encode(JRoute::_('/login?authenticator=' . $c['auth_domain_name'] . '&return=' .
					base64_encode(JRoute::_('/login?authenticator=' . $this->hzad->authenticator))))); ?>">
					<div class="account-group" id="<?php echo $c['auth_domain_name']; ?>">
						<p>
							<span class="user-icon"><?php echo $c['name']; ?></span><br />
							<span class="email-icon"><?php echo $c['email']; ?></span>
						</p>
					</div>
				</a>
<?php } // close foreach conflict ?>
			</div><!-- / #account-suggestions -->
<?php } // close if $this->conflict ?>

			<div class="clear"></div>

			<div id="other-links">
				<p>
					<span class="important">Have another account<?php echo ($this->conflict) ? ", but don't see it above" : ''; ?>?
					</span> Choose one of the following that best matches your scenario:
				</p>

<?php foreach($this->plugins as $plugin) {
	$paramsClass = 'JParameter';
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$paramsClass = 'JRegistry';
	}

	$pparams = new $paramsClass($plugin->params);
	$display = $pparams->get('display_name', ucfirst($plugin->name));

	$name = ($plugin->name == 'hubzero') ? 'Local hub' : $display;
	if($plugin->name != $this->hzad->authenticator) {
		echo '<a href="' . JRoute::_('/logout?return=' .
			base64_encode(JRoute::_('/login?authenticator=' . $plugin->name . '&return=' .
			base64_encode(JRoute::_('/login?authenticator=' . $this->hzad->authenticator))))) .
			'">I login to ' . $this->sitename . ' using my ' . $name  . ' account</a><br />';
	}
} ?>

			</div><!-- / #other-links -->
		</div><!-- / #option1-inner -->
	</div><!-- / #option1-link-existing -->

	<p class="or">OR</p>

	<div id="option2-create-new" class="options">
		<div class="clickable">Create a new account using your <?php echo $this->display_name; ?> identity</div>
		<div id="option2-inner" class="inner-content">
			<a id="new-account" href="<?php echo JRoute::_('index.php?option=com_register&task=update'); ?>">Create a new account</a>
		</div>
	</div>

</div><!-- / #link-existing -->
<div class="clear"></div>