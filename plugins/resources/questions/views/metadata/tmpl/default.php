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
?>
<p class="answer">
	<?php if ($this->resource->alias) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_resources&alias=' . $this->resource->alias . '&active=questions'); ?>">
	<?php else : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->resource->id . '&active=questions'); ?>">
	<?php endif; ?>
		<?php
			if ($this->count == 1)
			{
				echo JText::sprintf('PLG_RESOURCES_QUESTIONS_NUM_QUESTION', $this->count);
			}
			else
			{
				echo JText::sprintf('PLG_RESOURCES_QUESTIONS_NUM_QUESTIONS', $this->count);
			}
		?>
	</a>

	<?php if ($this->resource->alias) : ?>
		(<a href="<?php echo JRoute::_('index.php?option=com_answers&task=new&tag=tool:' . $this->resource->alias); ?>"><?php echo JText::_('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION'); ?></a>)
	<?php else : ?>
		(<a href="<?php echo JRoute::_('index.php?option=com_answers&task=new&tag=tool:' . $this->resource->id); ?>"><?php echo JText::_('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION'); ?></a>)
	<?php endif; ?>
</p>
