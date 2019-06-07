<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$votes = 0;
?>
<div class="section-inner hz-layout-with-aside">
	<div class="subject">
		<?php if ($this->poll->get('id')) { ?>
			<table class="pollresults">
				<caption class="sectiontableheader"><?php echo $this->escape($this->poll->get('title')); ?></caption>
				<thead>
					<tr>
						<th scope="col">
							<?php echo Lang::txt('COM_POLL_PERCENTAGE'); ?>
						</th>
						<th scope="col">
							<?php echo Lang::txt('COM_POLL_OPTION'); ?>
						</th>
						<th scope="col">
							<?php echo Lang::txt('COM_POLL_NUMBER_OF_VOTERS'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
			<?php foreach ($this->votes as $vote) : ?>
					<tr class="sectiontableentry<?php echo $vote->odd; ?>">
						<td>
							<?php
							$this->css('
								.' . $this->option .' .option' . $vote->id . ' {
									width: ' . $vote->percent . '%;
								}
							');
							?>
							<div class="graph">
								<strong class="bar <?php echo $vote->class . ' option' . $vote->id; ?>"><span><?php echo $this->escape($vote->percent); ?>%</span></strong>
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
</div>