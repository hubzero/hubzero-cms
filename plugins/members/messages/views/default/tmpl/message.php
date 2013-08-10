<?php

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

?>

<div class="hub-mail">
<table class="hub-message" summary="<?php echo JText::_('PLG_MEMBERS_MESSAGES_TBL_SUMMARY_OVERVIEW'); ?>">
	<thead>
		<tr>
			<th colspan="2">Message Details</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th><?php echo JText::_('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
			<td><?php echo JHTML::_('date', $this->xmessage->created, $dateFormat, $tz); ?></td>
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
					if($this->xmessage->component == 'support') {
						$fg = explode(' ',$subject);
						$fh = array_pop($fg);
						echo implode(' ',$fg);
					} else {
						echo $subject;
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
