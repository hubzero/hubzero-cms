<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dcls = '';
$lcls = '';
if ($this->id == $this->item->id) {
	switch ($this->vote)
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
<?php if ($this->vote) { ?>
	<span class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>"><?php echo $this->item->helpful; ?><span> Like</span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=vote&category='.$this->type.'&id='.$this->item->id.'&vote=like'); ?>" title="<?php echo $like_title; ?>"><?php echo $this->item->helpful; ?><span> Like</span></a>
<?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
<?php if ($this->vote) { ?>
	<span class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>"><?php echo $this->item->nothelpful; ?><span> Dislike</span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=vote&category='.$this->type.'&id='.$this->item->id.'&vote=dislike'); ?>" title="<?php echo $dislike_title; ?>"><?php echo $this->item->nothelpful; ?><span> Dislike</span></a>
<?php } ?>
</span>