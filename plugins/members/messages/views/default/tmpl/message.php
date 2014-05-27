<?php

?>

<div class="hub-mail">
	<table class="hub-message">
		<thead>
			<tr>
				<th colspan="2"><?php echo JText::_('PLG_MEMBERS_MESSAGES_DETAILS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
				<td><?php echo JHTML::_('date', $this->xmessage->created, JText::_('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
				<td><?php echo $this->from; ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
				<td>
					<?php 
						$subject = stripslashes($this->xmessage->subject);
						if ($this->xmessage->component == 'support') 
						{
							$fg = explode(' ', $subject);
							$fh = array_pop($fg);
							echo implode(' ', $fg);
						} 
						else 
						{
							echo $this->escape($subject);
						}
					?>
				</td>
			</tr>
			<tr>	
				<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_MESSAGE'); ?></th>
				<td><?php echo $this->xmessage->message; ?></td>
			</tr>
		</tbody>
	</table>
</div>
