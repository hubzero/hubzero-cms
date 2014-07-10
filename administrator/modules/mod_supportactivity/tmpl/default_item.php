<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
				<li data-parent="activity-list<?php echo $this->module->id; ?>" data-time="<?php echo $result->created; ?>" class="<?php echo $this->escape($result->category); ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_support&controller=tickets&task=edit&id=' . $result->ticket . ($result->id ? '#c' . $result->id : '')); ?>">
						<span class="activity-event">
							<?php echo JText::sprintf('MOD_SUPPORTACTIVITY_' . strtoupper($result->category), $result->ticket); ?>
						</span>
						<span class="activity-details">
							<span class="activity-time"><time datetime="<?php echo $result->created; ?>"><?php echo JHTML::_('date', $result->created, JText::_('TIME_FORMAT_HZ1')); ?></time></span>
							<span class="activity-date"><time datetime="<?php echo $result->created; ?>"><?php echo JHTML::_('date', $result->created, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
						</span>
					</a>
				</li>