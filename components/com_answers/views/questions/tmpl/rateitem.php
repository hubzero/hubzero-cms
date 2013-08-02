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

$dcls = '';
$lcls = '';

if ($this->item->get('vote', null)) 
{
	switch ($this->item->get('vote'))
	{
		case 'yes':
		case 'positive':
		case 'like':
			$lcls = ' chosen';
		break;

		case 'no':
		case 'negative':
		case 'dislike':
			$dcls = ' chosen';
		break;
	}
}

$juser = JFactory::getUser();
if (!$juser->get('guest')) 
{
	$like_title    = JText::sprintf('COM_ANSWERS_VOTE_LIKE_TITLE', $this->item->get('helpful', 0));
	$dislike_title = JText::sprintf('COM_ANSWERS_VOTE_DISLIKE_TITLE', $this->item->get('nothelpful', 0));
	$cls = ' tooltips';
} 
else 
{
	$like_title    = JText::_('COM_ANSWERS_VOTE_LIKE_LOGIN');;
	$dislike_title = JText::_('COM_ANSWERS_VOTE_DISLIKE_LOGIN');;
	$cls = ' tooltips';
}
?>
<?php if (!$this->item->get('vote')) { ?>
	<?php if ($juser->get('guest')) { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</span>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</a>
		</span>
	<?php } ?>
<?php } else { ?>
	<?php if (trim($lcls) == 'chosen') { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</a>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo JText::_('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</span>
		</span>
	<?php } ?>
<?php } ?>