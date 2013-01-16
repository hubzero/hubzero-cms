<?
$resp = $this->dep->getRespondent();
?>

<div id="content-header" class="full">
	<h2>Results: <?= $this->title ?></h2>
</div>

<div class="main section">
	<p>Completed <?= date('r', strtotime($resp->getEndTime())); ?></p>
	<? if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>Detailed results will be available <?= date('r', strtotime($this->dep->getEndTime())) ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
	<? elseif ($this->dep->getResultsClosed() == 'score'): ?>
		<p>Your score will be available <?= date('r', strtotime($this->dep->getEndTime())) ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
	<? endif; ?>
</div>