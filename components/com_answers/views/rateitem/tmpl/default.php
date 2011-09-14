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

$this->item->helpful = ($this->item->helpful) ? $this->item->helpful : 0;
$this->item->nothelpful = ($this->item->nothelpful) ? $this->item->nothelpful : 0;

$dcls = '';
$lcls = '';

if (isset($this->item->vote)) {
	switch ($this->item->vote)
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
} else {
	$this->item->vote = null;
}

$juser = JFactory::getUser();
if (!$juser->get('guest')) {
	$like_title = 'Vote this up :: '.$this->item->helpful.' people liked this';
	$dislike_title = 'Vote this down :: '.$this->item->nothelpful.' people did not like this';
	$cls = ' tooltips';
} else {
	$like_title = 'Vote this up :: Please login to vote.';
	$dislike_title = 'Vote this down :: Please login to vote.';
	$cls = ' tooltips';
}
?>
<span class="vote-like<?php echo $lcls; ?>">
<?php if ($this->item->vote || $juser->get('username') == $this->item->created_by) { ?>
	<span class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>"><?php echo $this->item->helpful; ?><span> Like</span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=yes'); ?>" title="<?php echo $like_title; ?>"><?php echo $this->item->helpful; ?><span> Like</span></a>
<?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
<?php if ($this->item->vote || $juser->get('username') == $this->item->created_by) { ?>
	<span class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>"><?php echo $this->item->nothelpful; ?><span> Dislike</span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=no'); ?>" title="<?php echo $dislike_title; ?>"><?php echo $this->item->nothelpful; ?><span> Dislike</span></a>
<?php } ?>
</span>
