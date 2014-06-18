<?
$pdf = $dep->getForm();
$title = 'Results: '.$pdf->getTitle();
$doc->setTitle($title);
$path->addItem(htmlentities($title), $_SERVER['REQUEST_URI']);
$resp = $dep->getRespondent();
$record = $resp->getAnswers();
$layout = $pdf->getPageLayout($record['summary']['version']);
?>
<h2><?= $title ?></h2>
<p>Completed <?= date('r', strtotime($resp->getEndTime())); ?></p>
<p>Score <strong><?= $record['summary']['score'] ?>%</strong></p>
<ol id="pages" class="complete">
<? $pdf->eachPage(function($url, $idx) use($layout, $record) { ?>
	<li>
		<img src="<?= $url ?>" />
		<?
		if (isset($layout[$idx - 1])):
			$qidx = 0;
			foreach ($layout[$idx - 1] as $qid=>$group):
				foreach ($group['answers'] as $aidx=>$ans):
					if ($record['detail'][$qid]['correct_answer_id'] == $ans['id']):
						echo '<div name="question-'.$qid.'" value="'.$ans['id'].'" class="answer-marker correct" type="radio" style="top: '.($ans['top'] - 4).'px; left: '.$ans['left'].'px">&#10003;</div>';
					elseif ($record['detail'][$qid]['answer_id'] == $ans['id']):
						echo '<div name="question-'.$qid.'" value="'.$ans['id'].'" class="answer-marker incorrect" type="radio" style="top: '.($ans['top'] - 4).'px; left: '.$ans['left'].'px">&#10003;</div>';
					endif;
				endforeach;
				++$qidx;
			endforeach;
		endif; ?>
	</li>
<? }); ?>
</ol>
