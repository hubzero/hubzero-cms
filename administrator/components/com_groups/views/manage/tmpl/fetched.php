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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();

JToolBarHelper::title(JText::_('COM_GROUPS'), 'groups.png');
JToolBarHelper::custom('display','back','back','COM_GROUPS_BACK', false);
JToolBarHelper::spacer();
JToolBarHelper::custom('doupdate', 'merge', '', 'COM_GROUPS_MERGE_CODE', false);

JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">

	<?php if (!empty($this->success)) : ?>
		<table class="adminlist success">
			<thead>
			 	<tr>
					<th scope="col"><?php echo JText::_('COM_GROUPS_FETCH_SUCCESS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->success as $success) : ?>
					<tr>
						<td class="merge-success">
							<?php
								$group = \Hubzero\User\Group::getInstance($success['group']);
								echo '<strong>' . $group->get('description') . ' (' . $group->get('cn') . ')</strong>';
								echo '<p>' . JText::_('COM_GROUPS_FETCH_SUCCESS_DESC') . '</p>';
							?>
							<hr />
							<code><?php echo implode('<br>', $success['message']); ?></code>

							<?php if ($success['message'][0] != JText::_('COM_GROUPS_FETCH_CODE_UP_TO_DATE')) : ?>
								<label class="merge">
									<?php echo JText::_('COM_GROUPS_MERGE'); ?>
									<input type="checkbox" name="id[]" checked="checked" value="<?php echo $group->get('gidNumber'); ?>" />
								</label>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<br /><br />

	<?php if (!empty($this->failed)) : ?>
		<table class="adminlist failed">
			<thead>
			 	<tr>
					<th scope="col"><?php echo JText::_('COM_GROUPS_FETCH_FAIL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->failed as $failed) : ?>
					<tr>
						<td>
							<?php
								$group = \Hubzero\User\Group::getInstance($failed['group']);
								echo '<strong>' . $group->get('description') . ' (' . $group->get('cn') . ')</strong>';
							?>
							<br />
							<br />
							<pre><?php echo $failed['message']; ?></pre>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="doupdate" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>