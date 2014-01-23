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

<div id="content-header-extra">
	<ul>
		<li>
			<a class="icon-back back btn" href="<?php echo JRoute::_($this->base); ?>">
			<?php echo JText::_('Back to course'); ?>
			</a>
		</li>
	</ul>
</div>

<div class="main section">
	<p>Completed <?= JHTML::_('date', $resp->getEndTime(), 'r'); ?></p>
	<p>Score <strong><?= $record['summary']['score'] ?>%</strong></p>

	<? if ($this->dep->getAllowedAttempts() > 1) : ?>
		<? $attempt = $resp->getAttemptNumber(); ?>
		You are allowed <strong><?= $this->dep->getAllowedAttempts() ?></strong> attempts.
		This was your <strong><?= FormHelper::toOrdinal((int)$attempt) ?></strong> attempt.
		<form action="<?= JRoute::_($this->base . '&task=form.complete') ?>">
			<input type="hidden" name="crumb" value="<?= $this->dep->getCrumb() ?>" />
			View another attempt: 
			<select name="attempt">
				<? for ($i = 1; $i <= $this->dep->getAllowedAttempts(); $i++) { ?>
					<?
						if ($i == $attempt) :
							continue;
						endif; 
					?>
					<option value="<?= $i ?>"><?= FormHelper::toOrdinal($i) ?> attempt</option>
				<? } ?>
				?>
			</select>
			<input class="btn btn-secondary" type="submit" value="GO" />
		</form>
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
						if ($record['detail'][$qid]['answer_id'] == 0) :
							echo '<div class="no-answer" style="top: '.($ans['top'] - 4).'px; left: '.$ans['left'].'px">No answer provided</div>';
							continue 2;
						elseif ($record['detail'][$qid]['correct_answer_id'] == $ans['id']):
							echo '<div name="question-'.$qid.'" value="'.$ans['id'].'" class="answer-marker correct" type="radio" style="top: '.($ans['top'] - 4).'px; left: '.$ans['left'].'px">&#10004;</div>';
						elseif ($record['detail'][$qid]['answer_id'] == $ans['id']):
							echo '<div name="question-'.$qid.'" value="'.$ans['id'].'" class="answer-marker incorrect" type="radio" style="top: '.($ans['top'] - 4).'px; left: '.$ans['left'].'px">&#10008;</div>';
						endif;
					endforeach;
					++$qidx;
				endforeach;
			endif; ?>
		</li>
	<? }); ?>
	</ol>
</div>