<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p>Registering at <?php echo $this->sitename; ?> is easy: just sign in using an account you may already have at one of the listed sites/organizations or create a new <?php echo $this->sitename; ?> account.</p>

			<h4>Why is registration required for parts of the <?php echo $this->sitename; ?>?</h4>

			<p>Our sponsors ask us who uses the <?php echo $this->sitename;?> and what they use it for. Registration helps us answer these questions. Usage statistics also focus our attention on improvements, making the <?php echo $this->sitename; ?> experience better for <em>you</em>.</p>
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
