<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<form id="<?php echo ($this->params->get('moduleclass_sfx')) ? $this->params->get('moduleclass_sfx') : 'poll' . rand(); ?>" method="post" action="<?php echo JRoute::_('index.php?option=com_poll'); ?>">
	<fieldset>
		<h4><?php echo $this->escape($poll->title); ?></h4>
		<ul class="poll">
	<?php for ($i = 0, $n = count($options); $i < $n; $i ++) : ?>
			<li class="<?php echo $this->escape($tabclass_arr[$tabcnt]); ?><?php echo $this->params->get('moduleclass_sfx'); ?>">
				<input type="radio" name="voteid" id="voteid<?php echo $options[$i]->id;?>" value="<?php echo $this->escape($options[$i]->id);?>" />
				<label for="voteid<?php echo $options[$i]->id; ?>" class="<?php echo $this->escape($tabclass_arr[$tabcnt]); ?><?php echo $this->params->get('moduleclass_sfx'); ?>">
					<?php echo $this->escape(str_replace('&#039;', "'", $options[$i]->text)); ?>
				</label>
			</li>
			<?php
				$tabcnt = 1 - $tabcnt;
			?>
	<?php endfor; ?>
		</ul>
		<p>
			<input type="submit" name="task_button" class="button" value="<?php echo JText::_('MOD_POLL_VOTE'); ?>" />
			 &nbsp; 
			<a href="<?php echo JRoute::_('index.php?option=com_poll&view=poll&id=' . $this->escape($poll->slug)); ?>"><?php echo JText::_('MOD_POLL_RESULTS'); ?></a>
		</p>

		<input type="hidden" name="option" value="com_poll" />
		<input type="hidden" name="task" value="vote" />
		<input type="hidden" name="id" value="<?php echo $this->escape($poll->id); ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</fieldset>
</form>