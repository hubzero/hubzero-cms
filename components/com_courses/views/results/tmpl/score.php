<?
$pdf = $this->pdf;
$resp = $this->resp;
$record = $resp->getAnswers();
?>

<div id="content-header" class="full">
	<h2>Results: <?= $this->title ?></h2>
</div>

<div class="main section">
	<p>Completed <?= date('r', strtotime($resp->getEndTime())); ?></p>
	<p>Score <strong><?= $record['summary']['score'] ?>%</strong></p>
	<? if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>More detailed results will be available <?= date('r', strtotime($this->dep->getEndTime())) ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
	<? endif; ?>
	<? if ($this->dep->getAllowedAttempts() > 1) : ?>
		<? $attempt = $resp->getAttemptNumber(); ?>
		You are allowed <strong><?= $this->dep->getAllowedAttempts() ?></strong> attempts.
		This was your <strong><?= FormHelper::toOrdinal((int)$attempt) ?></strong> attempt.
		<? if ($this->dep->getAllowedAttempts() > $attempt) : ?>
			<a href="<?= JRoute::_('/courses/form/complete?crumb='.$this->dep->getCrumb().'&attempt='.((int)$attempt+1)) ?>">Take your <?= FormHelper::toOrdinal((int)$attempt+1) ?> attempt</a>
		<? endif; ?>
	<? endif; ?>
</div>