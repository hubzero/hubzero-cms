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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();
if ($juser->get('guest')) { ?>
	<p class="warning"><?php echo JText::_('MOD_MYSUBMISSIONS_WARNING'); ?></p>
<?php } else {
	$steps = $this->steps;

	if ($this->rows) {
		$stepchecks = array();
		$laststep = (count($steps) - 1);

		foreach ($this->rows as $row)
		{
?>
	<div class="submission">
		<h4>
			<?php echo $this->escape(stripslashes($row->title)); ?>
			<a class="edit" href="<?php echo JRoute::_('index.php?option=com_resources&task=draft&step=1&id=' . $row->id); ?>">
				<?php echo JText::_('MOD_MYSUBMISSIONS_EDIT'); ?>
			</a>
		</h4>
		<table summary="<?php echo JText::_('MOD_MYSUBMISSIONS_EXPLANATION'); ?>">
			<tbody>
				<tr>
					<th><?php echo JText::_('MOD_MYSUBMISSIONS_TYPE'); ?></th>
					<td colspan="2"><?php echo $this->escape($row->typetitle); ?></td>
				</tr>
			<?php
			for ($i=1, $n=count($steps); $i < $n; $i++)
			{
				if ($i != $laststep) {
					$check = 'step_' . $steps[$i] . '_check';
					$stepchecks[$steps[$i]] = $this->$check($row->id);

					if ($stepchecks[$steps[$i]]) {
						$completed = '<span class="yes">' . JText::_('MOD_MYSUBMISSIONS_COMPLETED') . '</span>';
					} else {
						$completed = '<span class="no">' . JText::_('MOD_MYSUBMISSIONS_NOT_COMPLETED') . '</span>';
					}
					?>
				<tr>
					<th><?php echo $steps[$i]; ?></th>
					<td><?php echo $completed; ?></td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_resources&task=draft&step=' . $i . '&id=' . $row->id); ?>">
							<?php echo JText::_('MOD_MYSUBMISSIONS_EDIT'); ?>
						</a>
					</td>
				</tr>
				<?php
				}
			}
			?>
		</table>
		<p class="discrd">
			<a href="<?php echo JRoute::_('index.php?option=com_resources&task=discard&id=' . $row->id); ?>">
				<?php echo JText::_('MOD_MYSUBMISSIONS_DELETE'); ?>
			</a>
		</p>
		<p class="review">
			<a href="<?php echo JRoute::_('index.php?option=com_com_resources&task=draft&step=' . $laststep . '&id=' . $row->id); ?>">
				<?php echo JText::_('MOD_MYSUBMISSIONS_REVIEW_SUBMIT'); ?>
			</a>
		</p>
		<div class="clear"></div>
	</div>
<?php
		}
	} else {
?>
	<p><?php echo JText::_('MOD_MYSUBMISSIONS_NONE'); ?></p>
<?php
	}
}
