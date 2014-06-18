<? $showErrors = $_SERVER['REQUEST_METHOD'] == 'POST'; ?>
	<fieldset>
		<h3>Times</h3>
		<p>
			<label>
				<span>Start time:</span><input type="datetime" name="deployment[startTime]" value="<?= htmlentities(($val = $dep->getStartTime()) ? $val : date('Y-m-d H:i')) ?>" />
			</label>
			<? if ($showErrors && $dep->hasErrors('startTime')): ?>
			<ul class="errors">
				<? foreach ($dep->getErrors('startTime') as $err): ?>
				<li><?= $err ?></li>
				<? endforeach; ?>
			</ul>
			<? endif; ?>
		</p>
		<p>
			<label>
				<span>End time:</span><input type="datetime" name="deployment[endTime]" value="<?= htmlentities(($val = $dep->getEndTime()) ? $val : '') ?>" />
			</label>
			<? if ($showErrors && $dep->hasErrors('endTime')): ?>
			<ul class="error">
				<? foreach ($dep->getErrors('endTime') as $err): ?>
				<li><?= $err ?></li>
				<? endforeach; ?>
			</ul>
			<? endif; ?>
		</p>
		<p>
			<label>
				<span>Time limit:</span><input type="number" class="minutes" name="deployment[timeLimit]" value="<?= htmlentities(($val = $dep->getTimeLimit()) ? $val : '') ?>" /> minutes
			</label>
			<? if ($showErrors && $dep->hasErrors('timeLimit')): ?>
			<ul class="error">
				<? foreach ($dep->getErrors('timeLimit') as $err): ?>
				<li><?= $err ?></li>
				<? endforeach; ?>
			</ul>
			<? endif; ?>
		</p>
		<p class="info">If this deployment is timed, a landing page will be shown before the form to prevent users from triggering the countdown before they are ready.</p>
	</fieldset>
	<fieldset>
		<?
			$resultPages = array(
				'open' => $dep->getResultsOpen(),
				'closed' => $dep->getResultsClosed()
			);
		?>
		<h3>Results</h3>
		<p>While the form is open, show users:<br />
			<input type="radio" name="deployment[resultsOpen]" value="confirmation" <? if ($resultPages['open'] == 'confirmation') echo 'checked="checked" '; ?>/> only confirmation that there submission was accepted<br />
			<input type="radio" name="deployment[resultsOpen]" value="score" <? if ($resultPages['open'] == 'score' || !$resultPages['open']) echo 'checked="checked" '; ?>/> their score<br />
			<input type="radio" name="deployment[resultsOpen]" value="details" <? if ($resultPages['open'] == 'details') echo 'checked="checked" '; ?>/> a complete comparison of their answers to the correct answers<br />
			<? if ($showErrors && $dep->hasErrors('resultsOpen')): ?>
			<ul class="error">
				<? foreach ($dep->getErrors('resultsOpen') as $err): ?>
				<li><?= $err ?></li>
				<? endforeach; ?>
			</ul>
			<? endif; ?>
		</p>
		<p>After the form is closed, show users:<br />
			<input type="radio" name="deployment[resultsClosed]" value="confirmation" <? if ($resultPages['closed'] == 'confirmation') echo 'checked="checked" '; ?>/> only confirmation that there submission was accepted<br />
			<input type="radio" name="deployment[resultsClosed]" value="score" <? if ($resultPages['closed'] == 'score') echo 'checked="checked" '; ?>/> their score<br />
			<input type="radio" name="deployment[resultsClosed]" value="details" <? if ($resultPages['closed'] == 'details' || !$resultPages['closed']) echo 'checked="checked" '; ?>/> a complete comparison of their answers to the correct answers<br />
			<? if ($showErrors && $dep->hasErrors('resultsClosed')): ?>
			<ul class="error">
				<? foreach ($dep->getErrors('resultsClosed') as $err): ?>
				<li><?= $err ?></li>
				<? endforeach; ?>
			</ul>
			<? endif; ?>
		</p>
	</fieldset>
