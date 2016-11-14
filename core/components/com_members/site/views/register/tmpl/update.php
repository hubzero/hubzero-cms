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

$this->css('register')
     ->js('register');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">

<?php if (isset($this->self) && $this->self) { ?>
	<p class="passed">Your account has been updated successfully.</p>
	<?php if ($this->updateEmail) { ?>
		<p>Thank you for updating your account. In order to continue to use this account you must verify your new email address.</p>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p>A confirmation email has been sent to <?php echo $this->xprofile->get('email'); ?>. You must click the link in that email to activate your account and begin using <?php echo $this->sitename; ?>.</p>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<p class="passed">The account has been updated successfully.</p>
	<?php if ($this->updateEmail) { ?>
		<p>The user of this account has been notified of the change. In order to continue to use this account they will need to verify the new email address.</p>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p>A confirmation email has been sent to <?php echo $this->xprofile->get('email'); ?>. They must click the link in that email to activate your account and begin using <?php echo $this->sitename; ?>.</p>
		<?php } ?>
	<?php } ?>
<?php } ?>
</section><!-- / .main section -->

