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

$juser =& JFactory::getUser();

$pclass = (isset($this->item->vote) && $this->item->vote=="yes") ? 'yes' : 'zero';
$nclass = (isset($this->item->vote) && $this->item->vote=="no") ? 'no' : 'zero';
$this->item->helpful = ($this->item->helpful > 0) ? '+'.$this->item->helpful: '&nbsp;&nbsp;'.$this->item->helpful;
$this->item->nothelpful = ($this->item->nothelpful > 0) ? '-'.$this->item->nothelpful: '&nbsp;&nbsp;'.$this->item->nothelpful;
?>
<span class="thumbsvote" id="rev<?php echo $this->item->id; ?>_<?php echo $this->rid; ?>">
	<span class="<?php echo $pclass; ?>"><?php echo $this->item->helpful; ?></span>
<?php if ($juser->get('guest')) { ?>
		<span class="gooditem r_disabled"><a href="<?php echo JRoute::_('index.php?option=com_login&return='.base64_encode(JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews'))); //JRoute::_('index.php?option='.$this->option.'&task=plugin&trigger=onPublicationRateItem&action=rateitem&no_html=1&rid='.$this->rid.'&refid='.$this->item->id.'&vote=yes'); ?>" >&nbsp;</a></span>
		<span class="<?php echo $nclass; ?>"><?php echo $this->item->nothelpful; ?></span>
		<span class="baditem r_disabled"><a href="<?php echo JRoute::_('index.php?option=com_login&return='.base64_encode(JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews'))); //JRoute::_('index.php?option='.$this->option.'&task=plugin&trigger=onPublicationRateItem&action=rateitem&no_html=1&rid='.$this->rid.'refid='.$this->item->id.'&vote=no'); ?>" >&nbsp;</a></span>
		<span class="votinghints"><span><?php echo JText::_('PLG_PUBLICATION_REVIEWS_LOGIN_TO_VOTE'); ?></span></span>
<?php } else { ?>
		<span class="gooditem">
<?php if ($this->item->vote && $this->item->vote=="no" || $juser->get('username') == $this->item->created_by) { ?>
			<span class="dis">&nbsp;</span>
<?php } else if ($this->item->vote) { ?>
			<span>&nbsp;</span>
<?php } else { ?>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews').'?task=plugin&trigger=onPublicationRateItem&action=rateitem&vote=yes&&refid=' . $this->item->id; ?>" class="revvote" title="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_THIS_HELPFUL'); ?>">&nbsp;</a>
<?php } ?>
		</span>
		<span class="<?php echo $nclass; ?>"><?php echo $this->item->nothelpful; ?></span>
		<span class="baditem">
<?php if ($this->item->vote && $this->item->vote == 'yes' or $juser->get('username') == $this->item->created_by) { ?>
			<span class="dis">&nbsp;</span>
<?php } else if ($this->item->vote) { ?>
			<span>&nbsp;</span>
<?php } else { ?>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews').'?task=plugin&trigger=onPublicationRateItem&action=rateitem&vote=no&&refid=' . $this->item->id; ?>" class="revvote" title="<?php echo JText::_('PLG_PUBLICATION_REVIEWS_THIS_NOT_HELPFUL'); ?>">&nbsp;</a>
<?php } ?>
		</span>
		<span class="votinghints"><span></span></span>
<?php } ?>
</span>