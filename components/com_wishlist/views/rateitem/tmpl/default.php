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

/*$juser =& JFactory::getUser();

$title = ($juser->get('id') == $this->item->proposed_by) ? JText::_('You cannot vote for your own wish') : '';
if ($juser->get('guest')) { 
	$title = JText::_('Please login to vote'); 
}
if ($this->item->vote) { 
	$title = JText::_('You have already voted for this wish'); 
}
if ($this->item->status==1 or $this->item->status==3 or $this->item->status==4) { 
	$title = JText::_('Voting is closed for this wish'); 
}*/
			
// are we voting yes or no
//$pclass = (isset($this->item->vote) && $this->item->vote=="yes" && $this->item->status!=1) ? 'yes' : 'zero';
//$nclass = (isset($this->item->vote) && $this->item->vote=="no" && $this->item->status!=1) ? 'no' : 'zero';

//$this->item->positive = ($this->item->positive > 0) ? '+'.$this->item->positive: '&nbsp;&nbsp;'.$this->item->positive;
//$this->item->negative = ($this->item->negative > 0) ? '-'.$this->item->negative: '&nbsp;&nbsp;'.$this->item->negative;
			
// import filters
$filterln  = isset($this->filters['filterby']) ? '&filterby='.$this->filters['filterby'] : '';
$filterln .= isset($this->filters['sortby'])   ? '&sortby='.$this->filters['sortby']     : '';
$filterln .= isset($this->filters['tag'])      ? '&tags='.$this->filters['tag']          : '';
$filterln .= isset($this->filters['limit'])    ? '&limit='.$this->filters['limit']       : '';
$filterln .= isset($this->filters['start'])    ? '&limitstart='.$this->filters['start']  : '';

// Begin HTML output

/*<span class="thumbsvote" title="'.$title.'">
if ($juser->get('guest')) {
	<span class="'.$pclass.'">'.$this->item->positive.'</span>
	$html .= t.t.t.t.'<span class="gooditem r_disabled"><a href="index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=yes&page='.$this->page.$filterln.'" >&nbsp;</a></span>'.n;
	$html .= t.t.t.t.'<span class="'.$nclass.'">'.$this->item->negative.'</span>'.n;
	$html .= t.t.t.t.'<span class="baditem r_disabled"><a href="index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=no&page='.$this->page.$filterln.'" >&nbsp;</a></span>'.n;				
}else {
	<span class="'.$pclass.'">'.$this->item->positive.'</span>		
	$html .= t.t.t.t.'<span class="gooditem">'.n;
	if($this->item->vote && $this->item->vote=="no" or  $juser->get('id') == $this->item->proposed_by or $this->item->status==1) {
		$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
	}
	else if($this->item->vote) {
		$html .= t.t.t.t.'<span>&nbsp;</span>'.n;
	}
	else {
		$html .= t.t.t.t.t.'<a href="index.php?option='.$this->option.a.'task=rateitem'.a.'refid='.$this->item->id.a.'vote=yes'.a.'page='.$this->page.$filterln.'"  title="'.JText::_('THIS_IS_GOOD').'">&nbsp;</a>'.n;			
	}
	$html .= t.t.t.t.'</span>'.n;
	$html .= t.t.t.t.'<span class="'.$nclass.'">'.$this->item->negative.'</span>'.n;
	$html .= t.t.t.t.'<span class="baditem">'.n;
	if($this->item->vote && $this->item->vote=="yes" or $juser->get('id') == $this->item->proposed_by or $this->item->status==1) {
		$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
	}
	else if($this->item->vote) {
		$html .= t.t.t.'<span>&nbsp;</span>'.n;
	}
	else {
		$html .= t.t.t.t.t.'<a href="index.php?option='.$this->option.a.'task=rateitem'.a.'refid='.$this->item->id.a.'vote=no'.a.'page='.$this->page.$filterln.'"  title="'.JText::_('THIS_IS_NOT_GOOD').'">&nbsp;</a>'.n;	
	}
	$html .= t.t.t.t.'</span>'.n;		
}
				
	if($this->plugin) {
		$html .= t.t.t.t.'<span class="votinghints"><span>&nbsp;</span></span>'.n;
	}
				
</span>*/

$dcls = '';
$lcls = '';
$cls = ' tooltips';

$juser = JFactory::getUser();
if (!$juser->get('guest')) {
	// Logged in
	$like_title = JText::_('COM_WISHLIST_VOTING_I_LIKE_THIS');
	$dislike_title = JText::_('COM_WISHLIST_VOTING_I_DISLIKE_THIS');
	
	if ($this->item->vote) {
		$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_ALREADY_VOTED');
		if ($this->item->vote == $this->item->positive) {
			$lcls = ' chosen';
		}
		if ($this->item->vote == $this->item->negative) {
			$dcls = ' chosen';
		}
	}
	if ($juser->get('id') == $this->item->proposed_by) {
		$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_CANNOT_VOTE_FOR_OWN');
		//$this->item->positive = 0;
	}
	if ($this->item->status == 1 || $this->item->status == 3 || $this->item->status == 4) { 
		$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_CLOED');
	}
} else {
	// Not logged in
	$like_title = $dislike_title = JText::_('COM_WISHLIST_VOTING_LOGIN_TO_VOTE');
	//$this->item->positive = 0;
}
?>
<span class="vote-like<?php echo $lcls; ?>">
<?php if ($this->item->vote || $juser->get('guest') || $juser->get('id') == $this->item->proposed_by) { ?>
	<span class="vote-button <?php echo ($this->item->positive > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo JText::_('Vote up'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->positive; ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_LIKE'); ?></span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->positive > 0) ? 'like' : 'like'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=yes&page='.$this->page.$filterln); ?>" title="<?php echo JText::_('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->positive; ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_LIKE'); ?></span></a>
<?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
<?php if ($this->item->vote || $juser->get('guest') || $juser->get('id') == $this->item->proposed_by) { ?>
	<span class="vote-button <?php echo ($this->item->negative > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo JText::_('Vote down'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->negative; ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_DISLIKE'); ?></span></span>
<?php } else { ?>
	<a class="vote-button <?php echo ($this->item->negative > 0) ? 'dislike' : 'dislike'; echo $cls; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=no&page='.$this->page.$filterln); ?>" title="<?php echo JText::_('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->negative; ?><span> <?php echo JText::_('COM_WISHLIST_VOTING_DISLIKE'); ?></span></a>
<?php } ?>
</span>