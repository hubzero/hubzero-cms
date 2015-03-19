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

?>
<br />
	<ul class="key">
		<li class="draft"><?php echo JText::_('COM_PUBLICATIONS_VERSION_DRAFT'); ?></li>
		<li class="ready"><?php echo JText::_('COM_PUBLICATIONS_VERSION_READY'); ?></li>
		<li class="new"><?php echo JText::_('COM_PUBLICATIONS_VERSION_PENDING'); ?></li>
		<li class="preserving"><?php echo JText::_('COM_PUBLICATIONS_VERSION_PRESERVING'); ?></li>
		<li class="wip"><?php echo JText::_('COM_PUBLICATIONS_VERSION_WIP'); ?></li>
		<li class="published"><?php echo JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED'); ?></li>
		<li class="unpublished"><?php echo JText::_('COM_PUBLICATIONS_VERSION_UNPUBLISHED'); ?></li>
		<li class="deleted"><?php echo JText::_('COM_PUBLICATIONS_VERSION_DELETED'); ?></li>
	</ul>