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

$resp = $this->resp;
$dep  = $this->dep;
?>

<header id="content-header">
	<h2>Results: <?php echo $this->title ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev back btn" href="<?php echo Route::url($this->base); ?>">
				<?php echo Lang::txt('Back to course'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<p>Completed <?php echo Date::of($resp->getEndTime())->toLocal('r'); ?></p>
	<?php if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>Detailed results will be available <?php echo ($dep->getEndTime()) ? Date::of($dep->getEndTime())->toLocal('r') . " (about " . \Components\Courses\Helpers\Form::timeDiff(strtotime($this->dep->getEndTime()) - strtotime(Date::of('now'))) . " from now)" : 'soon'; ?>. Check the course progress page for more details.</p>
	<?php elseif ($this->dep->getResultsClosed() == 'score'): ?>
		<p>Your score will be available <?php echo ($dep->getEndTime()) ? Date::of($dep->getEndTime())->toLocal('r') . " (about " . \Components\Courses\Helpers\Form::timeDiff(strtotime($this->dep->getEndTime()) - strtotime(Date::of('now'))) . " from now)" : 'soon'; ?>. Check the course progress page for more details.</p>
	<?php endif; ?>
	<?php if ($this->dep->getAllowedAttempts() > 1) : ?>
		<?php $attempt = $resp->getAttemptNumber(); ?>
		<p>
			You are allowed <strong><?php echo $this->dep->getAllowedAttempts() ?></strong> attempts.
			This was your <strong><?php echo \Components\Courses\Helpers\Form::toOrdinal((int)$attempt) ?></strong> attempt.
		</p>
		<form action="<?php echo Route::url($this->base . '&task=form.complete') ?>">
			<input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
			<?php $completedAttempts = $resp->getCompletedAttempts(); ?>
			<?php if ($completedAttempts && count($completedAttempts) > 0) : ?>
				<p>
					View another completed attempt:
					<select name="attempt">
						<?php foreach ($completedAttempts as $completedAttempt) : ?>
							<option value="<?php echo $completedAttempt ?>"<?php echo ($completedAttempt == $attempt) ? ' selected="selected"' : ''; ?>><?php echo \Components\Courses\Helpers\Form::toOrdinal($completedAttempt) ?> attempt</option>
						<?php endforeach; ?>
					</select>
					<input class="btn btn-secondary" type="submit" value="GO" />
				</p>

				<?php $nextAttempt = (count($completedAttempts) < $dep->getAllowedAttempts()) ? (count($completedAttempts)+1) : null; ?>
			<?php endif; ?>

			<?php if ($dep->getState() == 'active' && isset($nextAttempt)) : ?>
				<p>
					<a class="btn btn-warning" href="<?php echo Route::url($this->base . '&task=form.complete&crumb=' . $this->dep->getCrumb() . '&attempt=' . $nextAttempt) ?>">
						Take your next attempt!
					</a>
				</p>
			<?php endif; ?>
		</form>
	<?php endif; ?>
</section>