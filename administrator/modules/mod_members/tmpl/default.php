<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();

$total = $this->confirmed + $this->unconfirmed;
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="stats-overview">
		<tbody>
			<tr>
				<td colspan="3">
					<div>
						<div class="graph">
							<strong class="bar" style="width: <?php echo round(($this->confirmed / $total) * 100, 2); ?>%"><span><?php echo round(($this->confirmed / $total) * 100, 2); ?>%</span></strong>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="confirmed">
					<a href="index.php?option=com_members&amp;emailConfirmed=1&amp;registerDate=" title="<?php echo JText::_('MOD_MEMBERS_CONFIRMED_TITLE'); ?>">
						<?php echo $this->escape($this->confirmed); ?>
						<span><?php echo JText::_('MOD_MEMBERS_CONFIRMED'); ?></span>
					</a>
				</td>
				<td class="unconfirmed">
					<a href="index.php?option=com_members&amp;emailConfirmed=-1&amp;registerDate=" title="<?php echo JText::_('MOD_MEMBERS_UNCONFIRMED_TITLE'); ?>">
						<?php echo $this->escape($this->unconfirmed); ?>
						<span><?php echo JText::_('MOD_MEMBERS_UNCONFIRMED'); ?></span>
					</a>
				</td>
				<td class="newest">
					<a href="index.php?option=com_members&amp;emailConfirmed=0&amp;registerDate=<?php echo gmdate("Y-m-d H:i:s", strtotime('-1 day')); ?>" title="<?php echo JText::_('MOD_MEMBERS_NEW_TITLE'); ?>">
						<?php echo $this->escape($this->pastDay); ?>
						<span><?php echo JText::_('MOD_MEMBERS_NEW'); ?></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>