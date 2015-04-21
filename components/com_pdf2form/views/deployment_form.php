<?php $showErrors = $_SERVER['REQUEST_METHOD'] == 'POST'; ?>
	<fieldset>
		<h3>Times</h3>
		<p>
			<label>
				<span>Start time:</span><input type="datetime" name="deployment[startTime]" value="<?php echo htmlentities(($val = $dep->getStartTime()) ? $val : date('Y-m-d H:i')) ?>" />
			</label>
			<?php if ($showErrors && $dep->hasErrors('startTime')): ?>
			<ul class="errors">
				<?php foreach ($dep->getErrors('startTime') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
		<p>
			<label>
				<span>End time:</span><input type="datetime" name="deployment[endTime]" value="<?php echo htmlentities(($val = $dep->getEndTime()) ? $val : '') ?>" />
			</label>
			<?php if ($showErrors && $dep->hasErrors('endTime')): ?>
			<ul class="error">
				<?php foreach ($dep->getErrors('endTime') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
		<p>
			<label>
				<span>Time limit:</span><input type="number" class="minutes" name="deployment[timeLimit]" value="<?php echo htmlentities(($val = $dep->getTimeLimit()) ? $val : '') ?>" /> minutes
			</label>
			<?php if ($showErrors && $dep->hasErrors('timeLimit')): ?>
			<ul class="error">
				<?php foreach ($dep->getErrors('timeLimit') as $err): ?>
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
				'open' => $dep->getResultsOpen(),
				'closed' => $dep->getResultsClosed()
			);
		?>
		<h3>Results</h3>
		<p>While the form is open, show users:<br />
			<input type="radio" name="deployment[resultsOpen]" value="confirmation" <?php if ($resultPages['open'] == 'confirmation') echo 'checked="checked" '; ?>/> only confirmation that there submission was accepted<br />
			<input type="radio" name="deployment[resultsOpen]" value="score" <?php if ($resultPages['open'] == 'score' || !$resultPages['open']) echo 'checked="checked" '; ?>/> their score<br />
			<input type="radio" name="deployment[resultsOpen]" value="details" <?php if ($resultPages['open'] == 'details') echo 'checked="checked" '; ?>/> a complete comparison of their answers to the correct answers<br />
			<?php if ($showErrors && $dep->hasErrors('resultsOpen')): ?>
			<ul class="error">
				<?php foreach ($dep->getErrors('resultsOpen') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
		<p>After the form is closed, show users:<br />
			<input type="radio" name="deployment[resultsClosed]" value="confirmation" <?php if ($resultPages['closed'] == 'confirmation') echo 'checked="checked" '; ?>/> only confirmation that there submission was accepted<br />
			<input type="radio" name="deployment[resultsClosed]" value="score" <?php if ($resultPages['closed'] == 'score') echo 'checked="checked" '; ?>/> their score<br />
			<input type="radio" name="deployment[resultsClosed]" value="details" <?php if ($resultPages['closed'] == 'details' || !$resultPages['closed']) echo 'checked="checked" '; ?>/> a complete comparison of their answers to the correct answers<br />
			<?php if ($showErrors && $dep->hasErrors('resultsClosed')): ?>
			<ul class="error">
				<?php foreach ($dep->getErrors('resultsClosed') as $err): ?>
				<li><?php echo $err ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</p>
	</fieldset>
