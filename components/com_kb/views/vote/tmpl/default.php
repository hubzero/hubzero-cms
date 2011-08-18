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

$juser = JFactory::getUser();
$num_likes = 0;
$num_dislikes = 0;

$like_link = JRoute::_('index.php?option='.$this->option.'&task=vote&category='.$this->type.'&id='.$this->item->id.'&vote=like');
$dislike_link = JRoute::_('index.php?option='.$this->option.'&task=vote&category='.$this->type.'&id='.$this->item->id.'&vote=dislike');

if($this->vote == 'like' || $this->vote == 'yes' || $this->vote == 'positive') { 
	$this->vote = 'like';
} elseif($this->vote == 'dislike' || $this->vote == 'no' || $this->vote == 'negative') { 
	$this->vote = 'dislike';
}

if($this->type != 'comment') {
	$num_likes = $this->item->helpful;
	$num_dislikes = $this->item->nothelpful;
}
?>

<span class="voting-links">
<?php if(!$this->vote) : ?>
	<?php if($juser->get('guest')) : ?>
		<span class="like like-disabled tooltips" title="Vote Up :: Please login to vote">
			<span><?php echo $num_likes; ?></span>
		</span>
		<span class="dislike dislike-disabled tooltips" title="Vote down :: Please login to vote">
			<span><?php echo $num_dislikes; ?></span>
		</span>
	<?php else : ?>
		<a class="vote-link like tooltips" href="<?php echo $like_link; ?>" title="Vote Up :: I like this">
			<span><?php echo $num_likes; ?></span>
		</a>
		<a class="vote-link dislike tooltips" href="<?php echo $dislike_link; ?>" title="Vote Down :: I dislike this">
			<span><?php echo $num_dislikes; ?></span>
		</a>
	<?php endif; ?>
<?php else : ?>
	<?php if($this->vote == 'like') : ?>
		<span class="like like-chosen tooltips" title="Already Voted :: You already liked this">
			<span><?php echo $num_likes; ?></span>
		</span>
		<a class="vote-link dislike tooltips" href="<?php echo $dislike_link; ?>" title="Change Your Vote :: Dislike this">
			<span><?php echo $num_dislikes; ?></span>
		</a>
	<?php else : ?>
		<a class="vote-link like tooltips" href="<?php echo $like_link; ?>" title="Change Your Vote :: Like this">
			<span><?php echo $num_likes; ?></span>
		</a>
		<span class="dislike dislike-chosen tooltips" title="Already Voted :: You already disliked this">
			<span><?php echo $num_dislikes; ?></span>
		</span>
	<?php endif; ?>
<?php endif; ?>
</span>
