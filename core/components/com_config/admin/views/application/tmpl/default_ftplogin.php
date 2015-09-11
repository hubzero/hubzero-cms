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
?>
<div class="width-100">
	<fieldset title="<?php echo Lang::txt('COM_CONFIG_FTP_DETAILS'); ?>" class="adminform">
		<legend><span><?php echo Lang::txt('COM_CONFIG_FTP_DETAILS'); ?></span></legend>
		<?php echo Lang::txt('COM_CONFIG_FTP_DETAILS_TIP'); ?>

		<?php if ($this->ftp instanceof Exception): ?>
			<p><?php echo Lang::txt($this->ftp->message); ?></p>
		<?php endif; ?>

		<div class="input-wrap">
			<label for="username"><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></label>
			<input type="text" id="username" name="username" class="input_box" size="70" value="" />
		</div>

		<div class="input-wrap">
			<label for="password"><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></label>
			<input type="password" id="password" name="password" class="input_box" size="70" value="" />
		</div>
	</fieldset>
</div>
