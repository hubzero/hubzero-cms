<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css('form.css')
     ->js('complete.js');

$pdf = $this->pdf;
$progress = $this->resp->getProgress();
$realLimit = $this->dep->getRealTimeLimit();
$incomplete = $this->incomplete;

// This is the time left for the form
// It's the lesser of the allowed time and the time until the form closes
$timeLeft = max($realLimit*60, 0);

// First, see if they've already started the form
if ($this->resp->getStartTime())
{
	// This is the time left since starting form
	$timeLeft2 = max(($this->dep->getTimeLimit() * 60) - (strtotime(Date::of('now')) - strtotime($this->resp->getStartTime())), 0);

	// Take individual time remaining...assuming it's less than actual time remaining
	if ($timeLeft2 < $timeLeft)
	{
		$timeLeft = $timeLeft2;
	}
}
?>

<header id="content-header">
	<h2><?php echo $this->escape($this->title) ?></h2>
</header>

<section class="main section">
	<?php
	if (($limit = $this->dep->getTimeLimit()) && is_null($this->resp->getStartTime())):
		require 'timed_landing.php';
	else:
		if ($this->dep->getTimeLimit()):
	?>
	<span id="time-left" data-time="<?php echo $timeLeft; ?>"></span>
	<?php
		endif;
		$layout = $pdf->getPageLayout();
		if ($incomplete):
			echo '<p class="error incomplete">' . Lang::txt('COM_COURSES_SELECT_ANSWER_FOR_EACH_QUESTION') . '</p>';
		endif;
	?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
		<ol id="pages" class="complete">
		<?php $pdf->eachPage(function($url, $idx) use ($layout, $progress, $incomplete) { ?>
			<li>
				<img src="<?php echo $url ?>" alt="" />
				<?php
				if (isset($layout[$idx - 1])):
					$qidx = 0;
					foreach ($layout[$idx - 1] as $qid => $group):
						foreach ($group['answers'] as $aidx => $ans):
							// phpcs:ignore PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass
							$this->css('
								#question-'.$qid.'-'.$aidx.' {
									top: '.$ans['top'].'px;
									left: '.$ans['left'].'px;
								}
							');
							echo '<input name="question-'.$qid.'" id="question-'.$qid.'-'.$aidx.'" value="'.$ans['id'].'" '.((isset($_POST['question-'.$qid]) && $_POST['question-'.$qid] == $ans['id']) || (!isset($_POST['question-'.$qid]) && isset($progress[$qid]) && $progress[$qid]['answer_id'] == $ans['id']) ? ' checked="checked" ' : '').'class="placeholder" type="radio" />';
							if (isset($incomplete[$qid])):
								// phpcs:ignore PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass
								$this->css('
									#question-'.$qid.'-incomplete-marker {
										top: '.$ans['top'].'px;
										left: '.($ans['left'] - 20).'px;
									}
								');
								echo '<div class="incomplete-marker" id="question-'.$qid.'-incomplete-marker">*</div>';
							endif;
						endforeach;
						++$qidx;
					endforeach;
				endif; ?>
			</li>
		<?php }); ?>
		</ol>
		<fieldset>
			<p>
				<input type="hidden" name="option" value="com_courses" />
				<input type="hidden" name="controller" value="form" />
				<input type="hidden" name="task" value="submit" />
				<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
				<input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
				<input type="hidden" name="attempt" value="<?php echo $this->resp->getAttemptNumber() ?>" />
				<button class="btn btn-primary" type="submit"><?php echo Lang::txt('COM_COURSES_SUBMIT'); ?></button>
			</p>
		</fieldset>
	</form>
	<?php endif; ?>
</section>
