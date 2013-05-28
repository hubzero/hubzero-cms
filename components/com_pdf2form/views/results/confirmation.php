<?
$pdf = $dep->getForm();
$title = 'Results: '.$pdf->getTitle();
$doc->setTitle($title);
$path->addItem(htmlentities($title), $_SERVER['REQUEST_URI']);
$resp = $dep->getRespondent();
?>
<p>Completed <?= date('r', strtotime($resp->getEndTime())); ?></p>
<? if ($dep->getResultsClosed() == 'details'): ?>
	<p>Detailed results will be available <?= date('r', strtotime($dep->getEndTime())) ?> (about <?= timeDiff(strtotime($dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
<? elseif ($dep->getResultsClosed() == 'score'): ?>
	<p>Your score will be available <?= date('r', strtotime($dep->getEndTime())) ?> (about <?= timeDiff(strtotime($dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
<? endif; ?>
