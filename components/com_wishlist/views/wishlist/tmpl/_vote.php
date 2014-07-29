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

// import filters
$filterln = '';
foreach ($this->filters as $key => $val)
{
	if ($val)
	{
		$filterln .= '&' . $key . '=' . $val;
	}
}

$dcls = '';
$lcls = '';
$cls  = ' tooltips';

$juser = JFactory::getUser();
if (!$juser->get('guest'))
{
	// Logged in
	$like_title    = JText::_('COM_WISHLIST_VOTING_I_LIKE_THIS');
	$dislike_title = JText::_('COM_WISHLIST_VOTING_I_DISLIKE_THIS');

	if ($this->item->get('vote'))
	{
		$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_ALREADY_VOTED');
		if ($this->item->get('vote') == $this->item->get('positive'))
		{
			$lcls = ' chosen';
		}
		if ($this->item->get('vote') == $this->item->get('negative'))
		{
			$dcls = ' chosen';
		}
	}
	if ($juser->get('id') == $this->item->get('proposed_by'))
	{
		$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_CANNOT_VOTE_FOR_OWN');
	}
	if ($this->item->get('status') == 1
	 || $this->item->get('status') == 3
	 || $this->item->get('status') == 4)
	{
		$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_CLOED');
	}
}
else
{
	// Not logged in
	$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_LOGIN_TO_VOTE');
}
?>
<span class="vote-like<?php echo $lcls; ?>">
<?php if ($juser->get('guest') || $juser->get('id') == $this->item->get('proposed_by')) { //if ($this->item->get('vote') || $juser->get('guest') || $juser->get('id') == $this->item->get('proposed_by')) { ?>
	<span class="vote-button <?php echo ($this->item->get('positive') > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo JText::_('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->get('positive'); ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_LIKE'); ?></span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->get('positive') > 0) ? 'like' : 'like'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->get('id').'&vote=yes&page='.$this->page.$filterln); ?>" title="<?php echo JText::_('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->get('positive', 0); ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_LIKE'); ?></span></a>
<?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
<?php if ($juser->get('guest') || $juser->get('id') == $this->item->get('proposed_by')) { //if ($this->item->get('vote') || $juser->get('guest') || $juser->get('id') == $this->item->get('proposed_by')) { ?>
	<span class="vote-button <?php echo ($this->item->get('negative') > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo JText::_('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->get('negative'); ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_DISLIKE'); ?></span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->get('negative') > 0) ? 'dislike' : 'dislike'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->get('id').'&vote=no&page='.$this->page.$filterln); ?>" title="<?php echo JText::_('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->get('negative', 0); ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_DISLIKE'); ?></span></a>
<?php } ?>
</span>