<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	<script type="text/javascript">
		window.timeLeft = <?php echo $timeLeft; ?>;
	</script>
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