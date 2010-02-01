<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div class="main section">
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p>Registering at <?php echo $this->hubShortName; ?> is easy: just sign in using an account you may already have at one of the listed sites/organizations or create a new <?php echo $this->hubShortName; ?> account.</p>

			<h4>Why is registration required for parts of the <?php echo $this->hubShortName; ?>?</h4>

			<p>Our sponsors ask us who uses the <?php echo $this->hubShortName;?> and what they use it for. Registration helps us answer these questions. Usage statistics also focus our attention on improvements, making the <?php echo $this->hubShortName; ?> experience better for <em>you</em>.</p>
		</div>
		<fieldset>
			<h3>Register with <br /><?php echo $this->hubShortName; ?></h3>
			<fieldset>
				<legend>Register by signing in with your</legend>
				<?php
				foreach($realms as $key=>$value)
				{
				?>
				<label>
					<input class="option" type="radio" name="realm" value="<?php echo $key; ?>" /> 
					<?php echo $value; ?>
				</label>
				<?php
				}
				?>
				<p style="text-align:center;margin: 1em 0 0 0;">
					<input class="option" type="submit" name="login" value="Log In" />
				</p>
			</fieldset>
			<h3>Or Create a<br />New Account</h3>
			<fieldset>
				<br />
				<legend>Create a separate account for <?php echo $this->hubShortName; ?></legend>
				<p style="text-align:center;margin: 1em 0 0 0;">
				<input class="option" type="submit" name="register" value="Create a New Account" /></p>
			</fieldset>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="option" value="com_hub" />
		<input type="hidden" name="view" value="registration" />
		<input type="hidden" name="task" value="select" />
		<input type="hidden" name="act" value="submit" />
	</form>
</div><!-- / .main section -->