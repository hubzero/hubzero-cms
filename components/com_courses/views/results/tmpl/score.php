<?
$pdf = $dep->getForm();
$title = 'Results: '.$pdf->getTitle();
$doc->setTitle($title);
$path->addItem(htmlentities($title), $_SERVER['REQUEST_URI']);
$resp = $dep->getRespondent();
$record = $resp->getAnswers();
?>
<h2><?= $title ?></h2>
<p>Completed <?= date('r', strtotime($resp->getEndTime())); ?></p>
<p>Score <strong><?= $record['summary']['score'] ?>%</strong></p>
<? if ($dep->getResultsClosed() == 'details'): ?>
	<p>More detailed results will be available <?= date('r', strtotime($dep->getEndTime())) ?> (about <?= timeDiff(strtotime($dep->getEndTime()) - time()) ?> from now). Save this link and come back then.</p>
<? endif; ?>
