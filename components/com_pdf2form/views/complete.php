<?
$pdf = $dep->getForm();
$title = $pdf->getTitle();
$doc->setTitle($title);
$doc->addScript('/components/com_pdf2form/resources/complete.js');
$path->addItem(htmlentities($title), $_SERVER['REQUEST_URI']);
$resp = $dep->getRespondent();
$progress = $resp->getProgress();
$realLimit = $dep->getRealTimeLimit();

if (($limit = $dep->getTimeLimit()) && is_null($resp->getStartTime())):
	require 'timed_landing.php';
else:
	if ($dep->getTimeLimit()):
?>
<script type="text/javascript">
	window.timeLeft = <? echo  max(($dep->getTimeLimit() * 60) - (time() - strtotime($resp->getStartTime())), 0); ?>;
</script>
<?
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
		<input type="hidden" name="task" value="submit" />
		<input type="hidden" name="crumb" value="<?= $dep->getCrumb() ?>" />
		<button type="submit">Submit</button>
	</p>
</fieldset>
</form>
<? endif;
