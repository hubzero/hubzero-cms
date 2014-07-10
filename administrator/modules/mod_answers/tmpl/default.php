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
defined('_JEXEC') or die( 'Restricted access' );

$this->css();

$total = $this->closed + $this->open;
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<tr>
				<td colspan="2">
					<div>
						<div class="graph">
							<strong class="bar" style="width: <?php echo round(($this->closed / $total) * 100, 2); ?>%"><span><?php echo JText::sprintf('MOD_ANSWERS_TOTAL_CLOSED', round(($this->closed / $total) * 100, 2)); ?></span></strong>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="closed">
					<a href="index.php?option=com_answers&amp;filterby=closed" title="<?php echo JText::_('MOD_ANSWERS_CLOSED_TITLE'); ?>"><?php echo $this->escape($this->closed); ?></a>
					<span><?php echo JText::_('MOD_ANSWERS_CLOSED'); ?></span>
				</td>
				<td class="asked">
					<a href="index.php?option=com_answers&amp;filterby=open" title="<?php echo JText::_('MOD_ANSWERS_ASKED_TITLE'); ?>"><?php echo $this->escape($this->open); ?></a>
					<span><?php echo JText::_('MOD_ANSWERS_ASKED'); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>