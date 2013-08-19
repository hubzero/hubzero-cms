<?php
$pdf = $this->pdf;
$progress = $this->resp->getProgress();
$realLimit = $this->dep->getRealTimeLimit();
$incomplete = $this->incomplete;
?>

<div id="content-header" class="full">
	<h2><?= htmlentities($this->title) ?></h2>
</div>

<div class="main section">
	<?php
	if (($limit = $this->dep->getTimeLimit()) && is_null($this->resp->getStartTime())):
		require 'timed_landing.php';
	else:
		if ($this->dep->getTimeLimit()):
	?>
	<script type="text/javascript">
		window.timeLeft = <? echo  max(($this->dep->getTimeLimit() * 60) - (time() - strtotime($this->resp->getStartTime())), 0); ?>;
	</script>
	<?php
		endif;
		$layout = $pdf->getPageLayout();
		if ($incomplete):
			echo '<p class="error incomplete">Please ensure you have selected an answer for each question.</p>';
		endif;
	?>
	<form action="" method="post">
	<ol id="pages" class="complete">	
	<? $pdf->eachPage(function($url, $idx) use($layout, $progress, $incomplete) { ?>
		<li>
			<img src="<?= $url ?>" />
			<?	
			if (isset($layout[$idx - 1])):
				$qidx = 0;
				foreach ($layout[$idx - 1] as $qid=>$group):
					foreach ($group['answers'] as $aidx=>$ans):
						echo '<input name="question-'.$qid.'" value="'.$ans['id'].'" '.((isset($_POST['question-'.$qid]) && $_POST['question-'.$qid] == $ans['id']) || (!isset($_POST['question-'.$qid]) && isset($progress[$qid]) && $progress[$qid]['answer_id'] == $ans['id']) ? ' checked="checked" ' : '').'class="placeholder" type="radio" style="top: '.$ans['top'].'px; left: '.$ans['left'].'px" />';
						if (isset($incomplete[$qid])):
							echo '<div class="incomplete-marker" style="top: '.$ans['top'].'px; left: '.($ans['left'] - 20).'px">*</div>';
						endif;
					endforeach;
					++$qidx;
				endforeach;
			endif; ?>
		</li>
	<? }); ?>
	</ol>
	<fieldset>
		<p>
			<input type="hidden" name="option" value="com_courses" />
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="submit" />
			<input type="hidden" name="crumb" value="<?= $this->dep->getCrumb() ?>" />
			<input type="hidden" name="attempt" value="<?= $this->resp->getAttemptNumber() ?>" />
			<button type="submit">Submit</button>
		</p>
	</fieldset>
	</form>
	<?php endif; ?>
</div>