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
 * @author    Steve Snyder
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="overlay"></div>
<div id="questions">
	<p>
		<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_THANK_YOU'); ?>
		<?php if ($award): ?>
			<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_AWARDS_EARNED', $award); ?>
		<?php endif; ?>
		<?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_REDIRECTED_SOON'); ?>
	</p>

	<a href="<?php echo Request::getVar('REQUEST_URI', Request::getVar('REDIRECT_REQUEST_URI', '', 'server'), 'server'); ?>"><?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_CLICK_IF_NOT_REDIRECTED'); ?></a>

	<script type="text/javascript">
		setTimeout(function() {
			var divs = ['overlay', 'questions'];
			for (var idx = 0; idx < divs.length; ++idx) {
				var div = document.getElementById(divs[idx]);
				div.parentNode.removeChild(div);
			}
		}, 4000);
	</script>
</div>
