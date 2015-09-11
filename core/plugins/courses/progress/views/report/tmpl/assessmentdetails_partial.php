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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
<?php endif; ?>