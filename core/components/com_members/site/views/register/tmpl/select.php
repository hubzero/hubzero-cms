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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p>Registering at <?php echo $this->sitename; ?> is easy: just sign in using an account you may already have at one of the listed sites/organizations or create a new <?php echo $this->sitename; ?> account.</p>

			<h4>Why is registration required for parts of the <?php echo $this->sitename; ?>?</h4>

			<p>Our sponsors ask us who uses the <?php echo $this->sitenamw;?> and what they use it for. Registration helps us answer these questions. Usage statistics also focus our attention on improvements, making the <?php echo $this->sitename; ?> experience better for <em>you</em>.</p>
		</div>
		<fieldset>
			<h3>Register with <?php echo $this->sitename; ?></h3>
			<fieldset>
				<legend>Register by signing in with your</legend>

				<?php foreach ($realms as $key => $value) { ?>
					<label>
						<input class="option" type="radio" name="realm" value="<?php echo $key; ?>" />
						<?php echo $value; ?>
					</label>
				<?php } ?>

				<p class="submit">
					<input class="option" type="submit" name="login" value="Log In" />
				</p>
			</fieldset>

			<h3>Or Create a New Account</h3>
			<fieldset>
				<legend>Create a separate account for <?php echo $this->sitename; ?></legend>

				<p class="submit">
					<input class="option" type="submit" name="register" value="Create a New Account" />
				</p>
			</fieldset>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="option" value="com_members" />
		<input type="hidden" name="controller" value="register" />
		<input type="hidden" name="task" value="select" />
		<input type="hidden" name="act" value="submit" />
	</form>
</section><!-- / .main section -->
