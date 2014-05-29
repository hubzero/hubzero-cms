<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="subject">
<?php if ($this->poll->id) { ?>
	<table class="pollresults" summary="<?php echo JText::_('Results for this pull'); ?>">
		<thead>
			<tr>
				<th colspan="3" class="sectiontableheader">
					<?php echo $this->escape($this->poll->title); ?>
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
					<?php echo $this->escape($vote->hits); ?>
				</td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
<?php } else { ?>
	<p>
		<?php echo JText::_('Select a poll from the list.'); ?>
	</p>
<?php } ?>
</div><!-- / .subject -->
<aside class="aside">
	<p>
		<strong><?php echo JText::_( 'Number of Voters' ); ?></strong><br />
		<?php echo (isset($this->votes[0])) ? $this->votes[0]->voters : '--'; ?>
	</p>
	<p>
		<strong><?php echo JText::_( 'First Vote' ); ?></strong><br />
		<?php echo ($this->first_vote) ? $this->escape($this->first_vote) : '--'; ?>
	</p>
	<p>
		<strong><?php echo JText::_( 'Last Vote' ); ?></strong><br />
		<?php echo ($this->last_vote) ? $this->escape($this->last_vote) : '--'; ?>
	</p>
</aside><!-- / .aside -->