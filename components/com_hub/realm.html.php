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
<form action="" method="post" id="hubForm">
	<div class="explaination">
		<p>You can sign in by using an existing account from a recognized organization, or by using
		an account created previously on our registration page.</p>
		<p>If you use an account from another organization, you may be redirected to that organization
		to sign in, and then redirected back to this site.</p>
		<h4>Why is registration required for parts of the <?php echo $hubShortName; ?>?</h4>

		<p>Our sponsors ask us who uses the <?php echo $hubShortName;?> and what they use it for. Registration
		helps us answer these questions. Usage statistics also focus our attention on improvements, making the
		<?php echo $hubShortName; ?> experience better for <em>you</em>.</p>

	</div>
	<fieldset>
		<br />
		<h3>Log in to <?php echo $hubShortName;?></h3>
 		<fieldset>
			<legend>Sign in with your</legend>
			<br />
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
			<p style="text-align:center;margin: 1em 0 0 0;"><input class="option" type="submit" name="login" value="Log In" /></p>
		</fieldset>
		<h3>Or Create a<br />New Account</h3>
		<fieldset>
			<br />
			<legend>Create a seperate account for <?php echo $hubShortName; ?></legend>
			<p style="text-align:center;margin: 1em 0 0 0;"><input class="option" type="submit" name="create" value="Create a New Account" /></p>
		</fieldset>
	</fieldset>
	<div class="clear"></div>
	<input type="hidden" name="option" value="com_hub" />
	<input type="hidden" name="view" value="login" />
	<input type="hidden" name="task" value="realm" />
	<input type="hidden" name="act" value="submit" />
	<input type="hidden" name="return" value="<?php echo base64_encode($return);?>" />
</form>

