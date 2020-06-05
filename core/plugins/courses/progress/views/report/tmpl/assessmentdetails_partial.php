<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->details && count($this->details) > 0) : ?>
	<?php ksort($this->details); ?>
	<?php foreach ($this->details as $question_id => $responses) : ?>
		<div class="question-box">
			<div class="question-label">
				Question <?php echo $question_id; ?>
			</div>
			<?php if ($responses && count($responses) > 0) : ?>
				<?php ksort($responses); ?>
				<?php
					$responses_total = 0;
					array_walk($responses, function($val, $idx) use (&$responses_total) {
						$responses_total += $val['count'];
					});
				?>

				<?php foreach ($responses as $response_label => $response) : ?>
					<div class="response">
						<div class="response-label">
							<?php echo ($response_label != 'z') ? $response_label : 'unanswered'; ?>:
						</div>
						<div class="response-bar">
							<?php $correct   = ($response['correct']) ? ' correct' : ''; ?>
							<?php $width     = $response['count'] / $responses_total * 100; ?>
							<?php $count_pos = ($width <= 50) ? 'count-right' : 'count-left'; ?>
							<div data-width="<?php echo $width; ?>" class="response-bar-inner<?php echo $correct; ?>">
								<div class="<?php echo $count_pos; ?>"><?php echo $response['count']; ?></div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
<?php else : ?>
	<p class="warning">There are no responses to display at this time</p>
<?php endif;
