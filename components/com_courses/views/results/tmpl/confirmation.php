<?
$resp = $this->resp;
?>

<div id="content-header" class="full">
	<h2>Results: <?= $this->title ?></h2>
</div>

<? if($course = $this->dep->getCourseInfo()) : ?>
	<div id="content-header-extra">
		<ul>
			<li>
				<? $base = 'index.php?option=com_courses&controller=offering&gid=' . $course->get('alias') . '&offering=' . $course->offering()->get('alias'); ?>
				<a class="back btn" href="<?php echo JRoute::_($base); ?>">
					<?php echo JText::_('Back to course'); ?>
				</a>
			</li>
		</ul>
	</div>
<? endif; ?>

<div class="main section">
	<p>Completed <?= date('r', strtotime($resp->getEndTime())); ?></p>
	<? if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>Detailed results will be available <?= date('r', strtotime($this->dep->getEndTime())) ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
	<? elseif ($this->dep->getResultsClosed() == 'score'): ?>
		<p>Your score will be available <?= date('r', strtotime($this->dep->getEndTime())) ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
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