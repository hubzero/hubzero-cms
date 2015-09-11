<?php
/**
 * HUBzero CMS
 *
 * Copyright 2013-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2013-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('tools.css');
?>
<div id="error-wrap">
	<div id="error-box" class="code-403">
		<h2><?php echo Lang::txt('COM_TOOLS_BADPARAMS'); ?></h2>
<?php if ($this->getError()) { ?>
		<p class="error-reasons"><?php echo $this->getError(); ?></p>
<?php } ?>
		<p><?php echo Lang::txt('COM_TOOLS_BADPARAMS_MESSAGE'); ?></p>
		<pre><?php echo $this->escape($this->badparams); ?></pre>
		<p><?php echo Lang::txt('COM_TOOLS_BADPARAMS_OPT_CONTACT_SUPPORT', Route::url('index.php?option=com_support&controller=tickets&task=new')); ?></p>
	</div><!-- / #error-box -->
</div><!-- / #error-wrap -->
