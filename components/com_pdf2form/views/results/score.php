<?php
$pdf = $dep->getForm();
$title = 'Results: '.$pdf->getTitle();
$doc->setTitle($title);
$path->addItem(htmlentities($title), $_SERVER['REQUEST_URI']);
$resp = $dep->getRespondent();
$record = $resp->getAnswers();
?>
<h2><?php echo $title ?></h2>
<p>Completed <?php echo date('r', strtotime($resp->getEndTime())); ?></p>
<p>Score <strong><?php echo $record['summary']['score'] ?>%</strong></p>
<?php if ($dep->getResultsClosed() == 'details'): ?>
	<p>More detailed results will be available <?php echo date('r', strtotime($dep->getEndTime())) ?> (about <?php echo timeDiff(strtotime($dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
<?php endif; ?>
