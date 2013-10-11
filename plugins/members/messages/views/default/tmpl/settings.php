<?php if (!$this->components) { ?>
		<p class="error"><?php echo JText::_('PLG_MEMBERS_MESSAGES_NO_COMPONENTS_FOUND'); ?></p>
<?php } else { ?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->member->get('uidNumber').'&active=messages'); ?>" method="post" id="hubForm" class="full">
			<input type="hidden" name="action" value="savesettings" />
			<table class="settings">
				<caption>
					<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
				</caption>
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('PLG_MEMBERS_MESSAGES_SENT_WHEN'); ?></th>
<?php
	foreach ($this->notimethods as $notimethod) 
	{
?>
						<th scope="col"><input type="checkbox" name="override[<?php echo $notimethod; ?>]" value="all" onclick="HUB.MembersMsg.checkAll(this, 'opt-<?php echo $notimethod; ?>');" /> <?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></th>
<?php
	}
?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo (count($this->notimethods) + 1); ?>">
							<input type="submit" value="<?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS'); ?>" />
						</td>
					</tr>
				</tfoot>
				<tbody>
<?php
				$cls = 'even';

				$sheader = '';
				foreach ($this->components as $component) 
				{
					if ($component->name != $sheader) {
						$sheader = $component->name;
?>
					<tr class="section-header">
						<th scope="col"><?php echo $this->escape($component->name); ?></th>
<?php
						foreach ($this->notimethods as $notimethod) 
						{
?>
						<th scope="col"><span class="<?php echo $notimethod; ?> iconed"><?php echo JText::_('PLG_MEMBERS_MESSAGES_MSG_'.strtoupper($notimethod)); ?></span></th>
<?php
						}
?>
					</tr>
<?php
					}
					$cls = (($cls == 'even') ? 'odd' : 'even');
?>
					<tr class="<?php echo $cls; ?>">
						<th scope="col"><?php echo $this->escape($component->title); ?></th>
						<?php echo plgMembersMessages::selectMethod($this->notimethods, $component->action, $this->settings[$component->action]['methods'], $this->settings[$component->action]['ids']); ?>
					</tr>
<?php
				}
?>
				</tbody>
			</table>
		</form>
<?php } ?>