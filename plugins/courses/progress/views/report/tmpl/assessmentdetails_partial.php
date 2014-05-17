<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
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