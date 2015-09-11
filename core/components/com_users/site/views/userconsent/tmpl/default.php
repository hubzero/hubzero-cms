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

\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'userconsent.css');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_USERS_USERCONSENT'); ?></h2>
</header>

<section class="section consent">
	<div><?php echo Lang::txt('COM_USERS_USERCONSENT_MESSAGE'); ?></div>

	<form method="POST" action="<?php echo Route::url('index.php?option=com_users&task=user.consent'); ?>">
		<input type="hidden" name="return" value="<?php echo base64_encode(Request::current(true)); ?>" />
		<div class="actions">
			<a class="btn btn-secondary" href="/"><?php echo Lang::txt('COM_USERS_USERCONSENT_CANCEL'); ?></a>
			<button class="btn btn-success" type="submit"><?php echo Lang::txt('COM_USERS_USERCONSENT_AGREE'); ?></button>
		</div>
	</form>
</section>