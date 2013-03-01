<?
$pdf = $this->pdf;
$resp = $this->dep->getRespondent();
$record = $resp->getAnswers();
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
	<p>Score <strong><?= $record['summary']['score'] ?>%</strong></p>
	<? if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>More detailed results will be available <?= date('r', strtotime($this->dep->getEndTime())) ?> (about <?= FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
	<? endif; ?>
</div>