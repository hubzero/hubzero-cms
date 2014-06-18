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

if (!strstr($this->url, 'index.php'))
{
	$this->url .= '?';
}
else
{
	$this->url .= '&';
}

if ($vote = $this->item->get('vote'))
{
	switch ($vote)
	{
		case 'yes':
		case 'positive':
		case 'up':
		case 'like':
		case '1':
		case '+':
			$lcls = ' chosen';
		break;

		case 'no':
		case 'negative':
		case 'down':
		case 'dislike':
		case '-1':
		case '-':
			$dcls = ' chosen';
		break;
	}
}
else
{
	$this->item->set('vote', null);
}

$juser = JFactory::getUser();
if (!$juser->get('guest'))
{
	$like_title    = JText::sprintf('PLG_HUBZERO_COMMENTS_VOTE_UP', $this->item->get('positive', 0));
	$dislike_title = JText::sprintf('PLG_HUBZERO_COMMENTS_VOTE_DOWN', $this->item->get('negative', 0));
	$cls = ' tooltips';
}
else
{
	$like_title    = JText::_('PLG_HUBZERO_COMMENTS_VOTE_UP_LOGIN');
	$dislike_title = JText::_('PLG_HUBZERO_COMMENTS_VOTE_DOWN_LOGIN');
	$cls = ' tooltips';
}

$no_html = JRequest::getInt('no_html', 0);

if (!$no_html) { ?>
<p class="comment-voting voting">
<?php } ?>
	<span class="vote-like<?php echo $lcls; ?>">
	<?php if ($this->item->get('vote') || $juser->get('id') == $this->item->get('created_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('positive', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
			<?php echo $this->item->get('positive', 0); ?><span> <?php echo JText::_('PLG_HUBZERO_COMMENTS_VOTE_LIKE'); ?></span>
		</span>
	<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->get('positive', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_($this->url . 'action=vote&voteup=' . $this->item->get('id')); ?>" title="<?php echo $like_title; ?>">
			<?php echo $this->item->get('positive', 0); ?><span> <?php echo JText::_('PLG_HUBZERO_COMMENTS_VOTE_LIKE'); ?></span>
		</a>
	<?php } ?>
	</span>
	<span class="vote-dislike<?php echo $dcls; ?>">
	<?php if ($this->item->get('vote') || $juser->get('id') == $this->item->get('created_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('negative', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
			<?php echo $this->item->get('negative', 0); ?><span> <?php echo JText::_('PLG_HUBZERO_COMMENTS_VOTE_DISLIKE'); ?></span>
		</span>
	<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->get('negative', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_($this->url . 'action=vote&votedown=' . $this->item->get('id')); ?>" title="<?php echo $dislike_title; ?>">
			<?php echo $this->item->get('negative', 0); ?><span> <?php echo JText::_('PLG_HUBZERO_COMMENTS_VOTE_DISLIKE'); ?></span>
		</a>
	<?php } ?>
	</span>
<?php if (!$no_html) { ?>
</p>
<?php } ?>