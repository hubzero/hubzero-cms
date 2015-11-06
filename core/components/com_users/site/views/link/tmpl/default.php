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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$step = (int) Request::getInt('step', 1);
?>

<header id="content-header">
	<h2>Account Setup</h2>
</header>

<section class="section">
	<div class="prompt-wrap">
		<div class="prompt-container prompt1" style="display:<?php echo ($step === 1) ? 'block': 'none'; ?>">
			<div class="prompt">
				Have you ever logged into <?php echo $this->sitename; ?> before?
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=com_users&view=link&step=2'); ?>">
					<div data-step="1" class="button next forward">Yes</div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=update'); ?>">
					<div data-step="1" class="button backwards">No</div>
				</a>
			</div>
		</div>

		<div class="prompt-container prompt2" style="display:<?php echo ($step === 2) ? 'block': 'none'; ?>">
			<div class="prompt">
				Great! Did you want to link your <?php echo $this->display_name; ?> account to that existing account or create a new account?
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=com_users&view=link&step=3'); ?>">
					<div data-step="2" class="button next link">Link</div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=update'); ?>">
					<div data-step="2" class="button create-new">Create new</div>
				</a>
			</div>
		</div>

		<div class="prompt-container prompt3" style="display:<?php echo ($step === 3) ? 'block': 'none'; ?>">
			<div class="prompt">
				We can do that. Just login with that existing account now and we'll link them up!
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=com_users&view=logout&return=' .
					base64_encode(
						Route::url('index.php?option=com_users&view=login&reset=1&return=' .
							base64_encode(
								Route::url('index.php?option=com_users&view=login&authenticator=' . $this->hzad->authenticator, false)
							),
						false)
					)); ?>">
					<div data-step="3" class="button ok">OK</div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_users&view=link&step=2'); ?>">
					<div data-step="3" class="button previous back">Go back</div>
				</a>
			</div>
		</div>
	</div>
</section>