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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Document::setTitle(\Lang::txt('COM_HELP'));
?>
<a name="help-top"></a>
<div class="help-header">
	<?php if ($this->page != 'index') : ?>
		<button class="back" onclick="window.history.back();" title="<?php echo Lang::txt('COM_HELP_GO_BACK'); ?>"><?php echo Lang::txt('COM_HELP_GO_BACK'); ?></button>
	<?php endif; ?>
</div>

<?php echo $this->content; ?>

<div class="help-footer">
	<a class="top" href="#help-top"><?php echo Lang::txt('COM_HELP_BACK_TO_TOP'); ?></a>
	<?php if ($this->page != 'index') : ?>
		<a class="index" href="<?php echo Route::url('index.php?option=com_help&component=' . str_replace('com_', '', $this->component) . '&page=index'); ?>">
			<?php echo Lang::txt('COM_HELP_INDEX'); ?>
		</a>
	<?php endif; ?>
	<p class="modified">
		<?php echo Lang::txt('COM_HELP_LAST_MODIFIED', date('l, F d, Y @ g:ia', $this->modified)); ?>
	</p>
</div>

<script>
var $ = (typeof(jq) !== "undefined" ? jq : jQuery);

$(document).ready(function() {
	var history = window.history;
	if (history.length > 1) {
		$('.back').show();
	}
});
</script>