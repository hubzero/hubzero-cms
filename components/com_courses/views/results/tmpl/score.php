<?
$pdf    = $this->pdf;
$resp   = $this->resp;
$dep    = $this->dep;
$record = $resp->getAnswers();
?>

<div id="content-header" class="full">
	<h2>Results: <?= $this->title ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="icon-back back btn" href="<?php echo JRoute::_($this->base); ?>">
			<?php echo JText::_('Back to course'); ?>
			</a>
		</li>
	</ul>
</div>

<div class="main section">
	<p>Completed <?= JHTML::_('date', $resp->getEndTime(), 'r'); ?></p>
	<p>Score <strong><?= $record['summary']['score'] ?>%</strong></p>
	<? if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>More detailed results will be available <?= ($dep->getEndTime()) ? JHTML::_('date', $dep->getEndTime(), 'r') . " (about " . FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - strtotime(JFactory::getDate())) . " from now)" : 'soon'; ?>. Check the course progress page for more details.</p>
	<? endif; ?>
	<? if ($this->dep->getAllowedAttempts() > 1) : ?>
		<? $attempt = $resp->getAttemptNumber(); ?>
		You are allowed <strong><?= $this->dep->getAllowedAttempts() ?></strong> attempts.
		This was your <strong><?= FormHelper::toOrdinal((int)$attempt) ?></strong> attempt.
		<form action="<?= JRoute::_($this->base . '&task=form.complete') ?>">
			<input type="hidden" name="crumb" value="<?= $this->dep->getCrumb() ?>" />
			View another attempt: 
			<select name="attempt">
				<? for ($i = 1; $i <= $this->dep->getAllowedAttempts(); $i++) { ?>
					<?
						if ($i == $attempt) :
							continue;
						endif; 
					?>
					<option value="<?= $i ?>"><?= FormHelper::toOrdinal($i) ?> attempt</option>
				<? } ?>
				?>
			</select>
			<input class="btn btn-secondary" type="submit" value="GO" />
		</form>
	<? endif; ?>
</div>