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
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->success) { ?>
	<p class="passed"><?php echo Lang::txt('Your account has been updated successfully.'); ?></p>
<?php } else { ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=change'); ?>" method="post" id="hubForm">
	<?php if (($this->email_confirmed != 1) && ($this->email_confirmed != 3)) { ?>
		<div class="explaination">
			<h4>Never received or cannot find the confirmation email?</h4>
			<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=resend&return=' . $this->return); ?>">clicking here</a>.</p>
		</div>
	<?php } ?>
		<fieldset>
			<h3><?php echo Lang::txt('Correct Email Address'); ?></h3>
			<label<?php if (!$this->email || !\Components\Members\Helpers\Utility::validemail($this->email)) { echo' class="fieldWithErrors"'; } ?>>
				<?php echo Lang::txt('Valid E-mail:'); ?>
				<input name="email" id="email" type="text" size="51" value="<?php echo $this->escape($this->email); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="change" />
		<input type="hidden" name="act" value="show" />

		<p class="submit"><input type="submit" name="update" value="<?php echo Lang::txt('Update Email'); ?>" /></p>
	</form>
<?php } ?>
</section><!-- / .section -->
