<?
$pdf = $this->pdf;
$dep = $this->dep;
$resp = $this->resp;
$record = $resp->getAnswers();
$layout = $pdf->getPageLayout($record['summary']['version']);
?>

<div id="content-header" class="full">
	<h2>Results: <?= $this->title ?></h2>
</div>

<div class="main section">
	<p>Completed <?= date('r', strtotime($resp->getEndTime())); ?></p>
	<p>Score <strong><?= $record['summary']['score'] ?>%</strong></p>

	<? if ($this->dep->getAllowedAttempts() > 1) : ?>
		<? $attempt = $resp->getAttemptNumber(); ?>
		You are allowed <strong><?= $this->dep->getAllowedAttempts() ?></strong> attempts.
		This was your <strong><?= FormHelper::toOrdinal((int)$attempt) ?></strong> attempt.
		<? if ($this->dep->getAllowedAttempts() > $attempt) : ?>
			<a href="<?= JRoute::_('/courses/form/complete?crumb='.$this->dep->getCrumb().'&attempt='.((int)$attempt+1)) ?>">View your <?= FormHelper::toOrdinal((int)$attempt+1) ?> attempt</a>
		<? endif; ?>
	<? endif; ?>

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
</div>