<?php
$pdf = $dep->getForm();
$title = 'Results: '.$pdf->getTitle();
$doc->setTitle($title);
$path->addItem(htmlentities($title), $_SERVER['REQUEST_URI']);
$resp = $dep->getRespondent();
?>
<p>Completed <?php echo date('r', strtotime($resp->getEndTime())); ?></p>
<?php if ($dep->getResultsClosed() == 'details'): ?>
	<p>Detailed results will be available <?php echo date('r', strtotime($dep->getEndTime())) ?> (about <?php echo timeDiff(strtotime($dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
<?php elseif ($dep->getResultsClosed() == 'score'): ?>
	<p>Your score will be available <?php echo date('r', strtotime($dep->getEndTime())) ?> (about <?php echo timeDiff(strtotime($dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
<?php endif; ?>
