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

$juser =& JFactory::getUser();

$pclass = (isset($this->item->vote) && $this->item->vote=="yes") ? 'yes' : 'zero';
$nclass = (isset($this->item->vote) && $this->item->vote=="no") ? 'no' : 'zero';
$this->item->helpful = ($this->item->helpful > 0) ? '+'.$this->item->helpful: '&nbsp;&nbsp;'.$this->item->helpful;
$this->item->nothelpful = ($this->item->nothelpful > 0) ? '-'.$this->item->nothelpful: '&nbsp;&nbsp;'.$this->item->nothelpful;
?>
<span class="thumbsvote" id="rev<?php echo $this->item->id; ?>_<?php echo $this->rid; ?>">
	<span class="<?php echo $pclass; ?>"><?php echo $this->item->helpful; ?></span>
<?php if ($juser->get('guest')) { ?>
		<span class="gooditem r_disabled"><a href="<?php echo JRoute::_('index.php?option=com_login&return='.base64_encode(JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews'))); //JRoute::_('index.php?option='.$this->option.'&task=plugin&trigger=onResourcesRateItem&action=rateitem&no_html=1&rid='.$this->rid.'&refid='.$this->item->id.'&vote=yes'); ?>" >&nbsp;</a></span>
		<span class="<?php echo $nclass; ?>"><?php echo $this->item->nothelpful; ?></span>
		<span class="baditem r_disabled"><a href="<?php echo JRoute::_('index.php?option=com_login&return='.base64_encode(JRoute::_('index.php?option='.$this->option.'&id='.$this->rid.'&active=reviews'))); //JRoute::_('index.php?option='.$this->option.'&task=plugin&trigger=onResourcesRateItem&action=rateitem&no_html=1&rid='.$this->rid.'refid='.$this->item->id.'&vote=no'); ?>" >&nbsp;</a></span>
		<span class="votinghints"><span><?php echo JText::_('PLG_RESOURCES_REVIEWS_LOGIN_TO_VOTE'); ?></span></span>
<?php } else { ?>
		<span class="gooditem">
<?php if ($this->item->vote && $this->item->vote=="no" || $juser->get('username') == $this->item->user_id) { ?>
			<span class="dis">&nbsp;</span>
<?php } else if ($this->item->vote) { ?>
			<span>&nbsp;</span>
<?php } else { ?>
			<a href="javascript:void(0);" class="revvote" title="<?php echo JText::_('PLG_RESOURCES_REVIEWS_THIS_HELPFUL'); ?>">&nbsp;</a>
<?php } ?>
		</span>
		<span class="<?php echo $nclass; ?>"><?php echo $this->item->nothelpful; ?></span>
		<span class="baditem">
<?php if ($this->item->vote && $this->item->vote == 'yes' or $juser->get('username') == $this->item->user_id) { ?>
			<span class="dis">&nbsp;</span>
<?php } else if ($this->item->vote) { ?>
			<span>&nbsp;</span>
<?php } else { ?>
			<a href="javascript:void(0);" class="revvote" title="<?php echo JText::_('PLG_RESOURCES_REVIEWS_THIS_NOT_HELPFUL'); ?>">&nbsp;</a>
<?php } ?>
		</span>
		<span class="votinghints"><span></span></span>
<?php } ?>
			
	</span>
</span>
