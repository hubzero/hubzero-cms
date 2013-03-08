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
defined('_JEXEC') or die('Restricted access');

$juser =& JFactory::getUser();

$this->item->helpful    = ($this->item->helpful)    ? $this->item->helpful    : 0;
$this->item->nothelpful = ($this->item->nothelpful) ? $this->item->nothelpful : 0;

$dcls = '';
$lcls = '';

if (isset($this->item->vote)) 
{
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
} 
else 
{
	$this->item->vote = null;
}

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
<?php if (!$this->item->vote) { ?>
	<?php if ($juser->get('guest')) { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</span>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews&action=rateitem&refid='.$this->item->id.'&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews&action=rateitem&refid='.$this->item->id.'&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</a>
		</span>
	<?php } ?>
<?php } else { ?>
	<?php if (trim($lcls) == 'chosen') { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews&task=rateitem&refid='.$this->item->id.'&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</a>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews&task=rateitem&refid='.$this->item->id.'&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</span>
		</span>
	<?php } ?>
<?php } ?>