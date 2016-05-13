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

$this->css('register.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="grid">
		<div class="col span-half">
			<div class="<?php echo ($this->getError() ? 'error' : 'success'); ?>-message">
				<p><?php echo ($this->getError() ? Lang::txt('COM_MEMBERS_REGISTER_ERROR_OCCURRED') : Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_CREATED')); ?></p>
			</div>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<?php if ($this->getError()) { ?>
				<p class="error"><?php echo $this->getError(); ?></p>
			<?php } else if ($this->xprofile->get('activation') < 0){ ?>
				<div class="account-activation">
					<div class="instructions">
						<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_CREATED_MESSAGE', $this->sitename, \Hubzero\Utility\String::obfuscate($this->xprofile->get('email'))); ?></p>
						<ol>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_FIND_EMAIL'); ?></li>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_ACTIVATE'); ?></li>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_LOGIN'); ?></li>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_SUCCESS'); ?></li>
						</ol>
					</div>
					<div class="notes">
						<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_NOTE', Route::url('index.php?option=com_support')); ?></p>
					</div>
				</div>
			<?php } ?>
		</div><!-- / .col span-half omega -->
	</div><!-- / .grid -->
</section><!-- / .main section -->
