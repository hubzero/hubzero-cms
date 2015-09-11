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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<p>
	You have <strong><?php echo \Components\Courses\Helpers\Form::timeDiff($realLimit*60) ?></strong> to complete this form.
	There are <strong><?php echo $this->pdf->getQuestionCount() ?></strong> questions.
	<?php if ($this->dep->getAllowedAttempts() > 1) : ?>
		You are allowed <strong><?php echo $this->dep->getAllowedAttempts() ?></strong> attempts.
		This is your <strong><?php echo \Components\Courses\Helpers\Form::toOrdinal((int)$this->resp->getAttemptNumber()) ?></strong> attempt.
	<?php endif; ?>
</p>
<?php if ($realLimit == $limit): ?>
	<p><em>Time will begin counting when you click 'Continue' below.</em></p>
<?php else: ?>
	<p><em>Time is already running because the form is close to expiring!</em></p>
<?php endif; ?>
<form action="<?php echo Route::url($this->base); ?>" method="post">
	<fieldset>
		<input type="hidden" name="task" value="startWork" />
		<input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
		<input type="hidden" name="attempt" value="<?php echo (int)$this->resp->getAttemptNumber() ?>" />
		<input type="hidden" name="controller" value="form" />
		<?php echo isset($_GET['tmpl']) ? '<input type="hidden" name="tmpl" value="'.str_replace('"', '&quot;', $_GET['tmpl']).'" />' : '' ?>
		<button type="submit">Continue</button>
	</fieldset>
</form>
