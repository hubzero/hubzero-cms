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
 * @author    Shawn Rice <zooley@purdue.edu>
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
</header>

<section class="main section">
<?php if ($this->getError() && $this->getError() == 'login mismatch') : ?>
	<p class="warning">
		You are currently logged in as <strong><?php echo $this->login; ?></strong>. If you're trying to activate a different account,
		you may do so by <a href="<?php echo $this->redirect; ?>">confirming a different email address</a>.
	</p>
<?php elseif ($this->getError()) : ?>
	<div class="subject">
		<div class="error">
			<h4><?php echo Lang::txt('Invalid Confirmation'); ?></h4>
			<p>The email confirmation link you followed is no longer valid. Your email address "<?php echo $this->escape($this->email); ?>" has not been confirmed.</p>
			<p>Please be sure to click the link from the latest confirmation email received.  Earlier confirmation emails will be invalid. If you cannot locate a newer confirmation email, you may <a href="<?php echo Route::url('index.php?option='.$this->option.'&task=resend'); ?>">resend a new confirmation email</a>.</p>
		</div>
	</div><!-- / .subject -->
	<aside class="aside">
		<h4>Never received or cannot find the confirmation email?</h4>
		<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo Route::url('index.php?option='.$this->option.'&task=resend&return='.$this->redirect); ?>">clicking here</a>.</p>
	</aside><!-- / .aside -->
<?php else : ?>
	<p class="passed">Your email address "<?php echo $this->escape($this->email); ?>" has already been confirmed. You should be able to use <?php echo $this->sitename; ?> now. Thank you.</p>
<?php endif; ?>
</section><!-- / .section -->
