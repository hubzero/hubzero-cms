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

$this->css('jquery.ui.css', 'system');
?>
<?php $showErrors = $_SERVER['REQUEST_METHOD'] == 'POST'; ?>
	<fieldset>
		<h3>Times</h3>
		<p>
			<label>
				<span>Time limit:</span><input type="number" step="1" min="0" class="minutes" name="deployment[timeLimit]" value="<?php echo htmlentities(($val = $this->dep->getTimeLimit()) ? $val : '') ?>" /> minutes
			</label>
			<?php if ($showErrors && $this->dep->hasErrors('timeLimit')): ?>
			<ul class="error">
				<?php foreach ($this->dep->getErrors('timeLimit') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
		<p>
			<label>
				<span>Attempts:</span><input type="number" step="1" min="1" class="minutes" name="deployment[allowedAttempts]" value="<?php echo htmlentities(($val = $this->dep->getAllowedAttempts()) ? $val : '1') ?>" /> allowed attempts
			</label>
			<?php if ($showErrors && $this->dep->hasErrors('allowedAttempts')): ?>
			<ul class="error">
				<?php foreach ($this->dep->getErrors('allowedAttempts') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
		<p class="info">If this deployment is timed, a landing page will be shown before the form to prevent users from triggering the countdown before they are ready.</p>
	</fieldset>
	<fieldset>
		<?php
			$resultPages = array(
				'open' => $this->dep->getResultsOpen(),
				'closed' => $this->dep->getResultsClosed()
			);
		?>
		<h3>Results</h3>
		<p>While the form is open, show users:<br />
			<input type="radio" name="deployment[resultsOpen]" value="confirmation" <?php if ($resultPages['open'] == 'confirmation') echo 'checked="checked" '; ?>/> only confirmation that their submission was accepted<br />
			<input type="radio" name="deployment[resultsOpen]" value="score" <?php if ($resultPages['open'] == 'score' || !$resultPages['open']) echo 'checked="checked" '; ?>/> their score<br />
			<input type="radio" name="deployment[resultsOpen]" value="details" <?php if ($resultPages['open'] == 'details') echo 'checked="checked" '; ?>/> a complete comparison of their answers to the correct answers<br />
			<?php if ($showErrors && $this->dep->hasErrors('resultsOpen')): ?>
			<ul class="error">
				<?php foreach ($this->dep->getErrors('resultsOpen') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
		<p>After the form is closed, show users:<br />
			<input type="radio" name="deployment[resultsClosed]" value="confirmation" <?php if ($resultPages['closed'] == 'confirmation') echo 'checked="checked" '; ?>/> only confirmation that their submission was accepted<br />
			<input type="radio" name="deployment[resultsClosed]" value="score" <?php if ($resultPages['closed'] == 'score') echo 'checked="checked" '; ?>/> their score<br />
			<input type="radio" name="deployment[resultsClosed]" value="details" <?php if ($resultPages['closed'] == 'details' || !$resultPages['closed']) echo 'checked="checked" '; ?>/> a complete comparison of their answers to the correct answers<br />
			<?php if ($showErrors && $this->dep->hasErrors('resultsClosed')): ?>
			<ul class="error">
				<?php foreach ($this->dep->getErrors('resultsClosed') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
	</fieldset>
