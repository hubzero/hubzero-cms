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

defined('_HZEXEC_') or die();

$votes = 0;
?>
<div class="subject">
	<?php if ($this->poll->get('id')) { ?>
		<table class="pollresults">
			<thead>
				<tr>
					<th colspan="3" class="sectiontableheader">
						<?php echo $this->escape($this->poll->get('title')); ?>
					</th>
				</tr>
			</thead>
			<tbody>
		<?php foreach ($this->votes as $vote) : ?>
				<tr class="sectiontableentry<?php echo $vote->odd; ?>">
					<td>
						<div class="graph">
							<strong class="bar <?php echo $vote->class; ?>" style="width: <?php echo $this->escape($vote->percent); ?>%;"><span><?php echo $this->escape($vote->percent); ?>%</span></strong>
						</div>
					</td>
					<td>
						<?php echo stripslashes($vote->text); ?>
					</td>
					<td class="votes">
						<?php
						$votes += $vote->hits;
						echo $this->escape($vote->hits); ?>
					</td>
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
	<?php } else { ?>
		<p>
			<?php echo Lang::txt('COM_POLL_SELECT_POLL'); ?>
		</p>
	<?php } ?>
</div><!-- / .subject -->
<aside class="aside">
	<p>
		<strong><?php echo Lang::txt('COM_POLL_NUMBER_OF_VOTERS'); ?></strong><br />
		<?php echo ($votes) ? $votes : '--'; ?>
	</p>
	<p>
		<strong><?php echo Lang::txt('COM_POLL_FIRST_VOTE'); ?></strong><br />
		<?php echo ($this->first_vote) ? $this->escape($this->first_vote) : '--'; ?>
	</p>
	<p>
		<strong><?php echo Lang::txt('COM_POLL_LAST_VOTE'); ?></strong><br />
		<?php echo ($this->last_vote) ? $this->escape($this->last_vote) : '--'; ?>
	</p>
</aside><!-- / .aside -->