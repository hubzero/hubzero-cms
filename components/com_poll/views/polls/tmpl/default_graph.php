<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access'); ?>

<div class="subject">
<?php if ($this->poll->id) { ?>
	<table class="pollresults">
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
		<?php echo JText::_('COM_POLL_SELECT_POLL'); ?>
	</p>
<?php } ?>
</div><!-- / .subject -->
<aside class="aside">
	<p>
		<strong><?php echo JText::_('COM_POLL_NUMBER_OF_VOTERS'); ?></strong><br />
		<?php echo (isset($this->votes[0])) ? $this->votes[0]->voters : '--'; ?>
	</p>
	<p>
		<strong><?php echo JText::_('COM_POLL_FIRST_VOTE'); ?></strong><br />
		<?php echo ($this->first_vote) ? $this->escape($this->first_vote) : '--'; ?>
	</p>
	<p>
		<strong><?php echo JText::_('COM_POLL_LAST_VOTE'); ?></strong><br />
		<?php echo ($this->last_vote) ? $this->escape($this->last_vote) : '--'; ?>
	</p>
</aside><!-- / .aside -->