<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('form.css');

$pdf     = $this->pdf;
$dep     = $this->dep;
$resp    = $this->resp;
$record  = $resp->getAnswers();
$layout  = $pdf->getPageLayout($record['summary']['version']);
$version = $record['summary']['version'];
?>

<header id="content-header">
	<h2>Results: <?php echo $this->title ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev back btn" href="<?php echo Route::url($this->base); ?>">
				<?php echo Lang::txt('Back to course'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<p>Completed <?php echo Date::of($resp->getEndTime())->toLocal('r'); ?></p>
	<p>Score <strong><?php echo $record['summary']['score'] ?>%</strong></p>

	<?php if ($this->dep->getAllowedAttempts() > 1) : ?>
		<?php $attempt = $resp->getAttemptNumber(); ?>
		<p>
			You are allowed <strong><?php echo $this->dep->getAllowedAttempts() ?></strong> attempts.
			This was your <strong><?php echo \Components\Courses\Helpers\Form::toOrdinal((int)$attempt) ?></strong> attempt.
		</p>
		<form action="<?php echo Route::url($this->base . '&task=form.complete') ?>">
			<input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
			<?php $completedAttempts = $resp->getCompletedAttempts(); ?>
			<?php if ($completedAttempts && count($completedAttempts) > 0) : ?>
				<p>
					View another completed attempt:
					<select name="attempt">
						<?php foreach ($completedAttempts as $completedAttempt) : ?>
							<option value="<?php echo $completedAttempt ?>"<?php echo ($completedAttempt == $attempt) ? ' selected="selected"' : ''; ?>><?php echo \Components\Courses\Helpers\Form::toOrdinal($completedAttempt) ?> attempt</option>
						<?php endforeach; ?>
					</select>
					<input class="btn btn-secondary" type="submit" value="GO" />
				</p>

				<?php $nextAttempt = (count($completedAttempts) < $dep->getAllowedAttempts()) ? (count($completedAttempts)+1) : null; ?>
			<?php endif; ?>

			<?php if ($dep->getState() == 'active' && isset($nextAttempt)) : ?>
				<p>
					<a class="btn btn-warning" href="<?php echo Route::url($this->base . '&task=form.complete&crumb=' . $this->dep->getCrumb() . '&attempt=' . $nextAttempt) ?>">
						Take your next attempt!
					</a>
				</p>
			<?php endif; ?>
		</form>
	<?php endif; ?>

	<ol id="pages" class="complete">
	<?php $pdf->eachPage(function($url, $idx) use($layout, $record) { ?>
		<li>
			<img src="<?php echo $url ?>" />
			<?php
			if (isset($layout[$idx - 1])):
				$qidx = 0;
				foreach ($layout[$idx - 1] as $qid => $group):
					foreach ($group['answers'] as $aidx => $ans):
                                                if (!isset($record['detail'][$qid]) || $record['detail'][$qid]['answer_id'] == 0) :
                                                        \Document::addstyleDeclaration('
                                                                #question-'.$qid.'-marker {
                                                                        top: '.($ans['top'] - 4).'px;
                                                                        left: '.$ans['left'].'px;
                                                                }
                                                        ');

                                                        echo '<div class="no-answer" id="question-'.$qid.'-marker">No answer provided</div>';
                                                        continue 2;
                                                elseif ($record['detail'][$qid]['correct_answer_id'] == $ans['id']):
                                                       \Document::addstyleDeclaration('
                                                                #question-'.$qid.'-marker-correct {
                                                                        top: '.($ans['top'] - 4).'px;
                                                                        left: '.$ans['left'].'px;
                                                                }
                                                        ');
                                                        echo '<div name="question-'.$qid.'" id="question-'.$qid.'-marker-correct" value="'.$ans['id'].'" class="answer-marker correct" type="radio">&#10004;</div>';
                                                elseif ($record['detail'][$qid]['answer_id'] == $ans['id']):
                                                      \Document::addstyleDeclaration('
                                                                #question-'.$qid.'-marker-incorrect {
                                                                        top: '.($ans['top'] - 4).'px;
                                                                        left: '.$ans['left'].'px;
                                                                }
                                                        ');
                                                        echo '<div name="question-'.$qid.'" id="question-'.$qid.'-marker-incorrect" value="'.$ans['id'].'" class="answer-marker incorrect" type="radio">&#10008;</div>';
                                                endif;
                                        endforeach;
					++$qidx;
				endforeach;
			endif; ?>
		</li>
	<?php }, $version); ?>
	</ol>
</section>
