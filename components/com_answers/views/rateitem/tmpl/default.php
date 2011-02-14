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


$this->item->helpful = ($this->item->helpful) ? $this->item->helpful : 0;
$this->item->nothelpful = ($this->item->nothelpful) ? $this->item->nothelpful : 0;

$dcls = '';
$lcls = '';
//if ($this->id == $this->item->id) {
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
//}
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


/*$juser =& JFactory::getUser();

$pclass = (isset($this->item->vote) && $this->item->vote=="yes") ? 'yes' : 'zero';
$nclass = (isset($this->item->vote) && $this->item->vote=="no") ? 'no' : 'zero';
$this->item->helpful = ($this->item->helpful > 0) ? '+'.$this->item->helpful: '&nbsp;&nbsp;'.$this->item->helpful;
$this->item->nothelpful = ($this->item->nothelpful > 0) ? '-'.$this->item->nothelpful: '&nbsp;&nbsp;'.$this->item->nothelpful;
?>
<span class="thumbsvote">
	<span class="<?php echo $pclass; ?>"><?php echo $this->item->helpful; ?></span>
<?php if ($juser->get('guest')) { ?>
		<span class="gooditem r_disabled"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=yes'); ?>" >&nbsp;</a></span>
		<span class="<?php echo $nclass; ?>"><?php echo $this->item->nothelpful; ?></span>
		<span class="baditem r_disabled"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->id.'&vote=no'); ?>" >&nbsp;</a></span>
		<span class="votinghints"><span><?php echo JText::_('COM_ANSWERS_LOGIN_TO_VOTE'); ?></span></span>
<?php } else { ?>
		<span class="gooditem">
<?php if ($this->item->vote && $this->item->vote=="no" || $juser->get('username') == $this->item->created_by) { ?>
			<span class="dis">&nbsp;</span>
<?php } else if ($this->item->vote) { ?>
			<span>&nbsp;</span>
<?php } else { ?>
			<a href="javascript:void(0);" class="revvote" title="<?php echo JText::_('COM_ANSWERS_THIS_HELPFUL'); ?>">&nbsp;</a>
<?php } ?>
		</span>
		<span class="<?php echo $nclass; ?>"><?php echo $this->item->nothelpful; ?></span>
		<span class="baditem">
<?php if ($this->item->vote && $this->item->vote == 'yes' or $juser->get('username') == $this->item->created_by) { ?>
			<span class="dis">&nbsp;</span>
<?php } else if ($this->item->vote) { ?>
			<span>&nbsp;</span>
<?php } else { ?>
			<a href="javascript:void(0);" class="revvote" title="<?php echo JText::_('COM_ANSWERS_THIS_NOT_HELPFUL'); ?>">&nbsp;</a>
<?php } ?>
		</span>
		<span class="votinghints"><span></span></span>
<?php } ?>
	</span>
</span>
*/
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