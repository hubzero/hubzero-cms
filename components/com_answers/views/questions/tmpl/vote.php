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

$votes = $this->question->get('helpful', 0);

$juser = JFactory::getUser();
?>
<span class="vote-like">
	<?php if ($juser->get("guest")) { ?>
		<span class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo JText::_('COM_ANSWERS_VOTE_LIKE_LOGIN'); ?>">
			<?php echo $votes; ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
		</span>
	<?php } else { ?>
		<?php if ($this->question->get('created_by') == $juser->get('username')) { ?>
			<span class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo JText::_('COM_ANSWERS_VOTE_CANT_VOTE_FOR_SELF'); ?>">
				<?php echo $votes; ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		<?php } elseif ($this->voted) { ?>
			<span class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo JText::_('COM_ANSWERS_VOTE_ALREADY'); ?>">
				<?php echo $votes; ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		<?php } else { ?>
			<a class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=vote&id=' . $this->question->get('id') . '&vote=1'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_VOTE_LIKE_TITLE', $votes); ?>">
				<?php echo $votes; ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		<?php } ?>
	<?php } ?>
</span>
