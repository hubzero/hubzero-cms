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

$this->item->positive = ($this->item->positive) ? $this->item->positive : 0;
$this->item->negative = ($this->item->negative) ? $this->item->negative : 0;

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

if (isset($this->item->vote)) {
	switch ($this->item->vote)
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
} else {
	$this->item->vote = null;
}

$juser = JFactory::getUser();
if (!$juser->get('guest')) {
	$like_title = 'Vote this up :: ' . $this->item->positive . ' people liked this';
	$dislike_title = 'Vote this down :: ' . $this->item->negative . ' people did not like this';
	$cls = ' tooltips';
} else {
	$like_title = 'Vote this up :: Please login to vote.';
	$dislike_title = 'Vote this down :: Please login to vote.';
	$cls = ' tooltips';
}

$no_html = JRequest::getInt('no_html', 0);

if (!$no_html) { ?>
<p class="comment-voting">
<?php } ?>
	<span class="vote-like<?php echo $lcls; ?>">
<?php if ($this->item->vote || $juser->get('id') == $this->item->created_by) { ?>
		<span class="vote-button <?php echo ($this->item->positive > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
			<?php echo $this->item->positive; ?><span> Like</span>
		</span>
<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->positive > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_($this->url . 'action=vote&voteup=' . $this->item->id); ?>" title="<?php echo $like_title; ?>">
			<?php echo $this->item->positive; ?><span> Like</span>
		</a>
<?php } ?>
	</span>
	<span class="vote-dislike<?php echo $dcls; ?>">
<?php if ($this->item->vote || $juser->get('id') == $this->item->created_by) { ?>
		<span class="vote-button <?php echo ($this->item->negative > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
			<?php echo $this->item->negative; ?><span> Dislike</span>
		</span>
<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->negative > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo JRoute::_($this->url . 'action=vote&votedown=' . $this->item->id); ?>" title="<?php echo $dislike_title; ?>">
			<?php echo $this->item->negative; ?><span> Dislike</span>
		</a>
<?php } ?>
	</span>
<?php if (!$no_html) { ?>
</p>
<?php } ?>