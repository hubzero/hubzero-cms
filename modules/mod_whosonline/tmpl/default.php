<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_whosonline
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div class="<?php echo $this->params->get('moduleclass_sfx', ''); ?>">
	<?php if ($this->params->get('showmode', 0) == 0 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th><?php echo JText::_('MOD_WHOSONLINE_LOGGEDIN'); ?></th>
					<th><?php echo JText::_('MOD_WHOSONLINE_GUESTS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo number_format($this->loggedInCount); ?></td>
					<td><?php echo number_format($this->guestCount); ?></td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<?php if ($this->params->get('showmode', 0) == 1 || $this->params->get('showmode', 0) == 2) : ?>
		<table>
			<thead>
				<tr>
					<th colspan="2"><?php echo JText::_('MOD_WHOSONLINE_LOGGEDIN_NAME'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->loggedInList as $loggedin) : ?>
					<tr>
						<td><?php echo $loggedin->get('name'); ?></td>
						<td align="right">
							<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $loggedin->get('uidNumber')); ?>">
								<?php echo JText::_('MOD_WHOSONLINE_LOGGEDIN_VIEW_PROFILE'); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<table>
		<tbody>
			<tr>
				<td align="center">
					<a class="btn btn-secondary opposite icon-next" href="<?php echo JRoute::_('index.php?option=com_members&task=activity'); ?>">
						<?php echo JText::_('MOD_WHOSONLINE_VIEW_ALL_ACTIVITIY'); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>