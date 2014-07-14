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

$this->css('tools.css');
?>
<div id="error-wrap">
	<div id="error-box" class="code-403">
		<h2><?php echo JText::_('COM_TOOLS_ACCESSDENIED'); ?></h2>
<?php if ($this->getError()) { ?>
		<p class="error-reasons"><?php echo $this->getError(); ?></p>
<?php } ?>

		<p><?php echo JText::_('COM_TOOLS_ACCESSDENIED_MESSAGE'); ?></p>
		<h3><?php echo JText::_('COM_TOOLS_ACCESSDENIED_HOW_TO_FIX'); ?></h3>
		<ul>
			<li><?php echo JText::sprintf('COM_TOOLS_ACCESSDENIED_OPT_CONTACT_SUPPORT', JRoute::_('index.php?option=com_support&controller=tickets&task=new')); ?></li>
			<li><?php echo JText::sprintf('COM_TOOLS_ACCESSDENIED_OPT_BROWSE', JRoute::_('index.php?option=' . $this->option)); ?></li>
		</ul>
	</div><!-- / #error-box -->
</div><!-- / #error-wrap -->
