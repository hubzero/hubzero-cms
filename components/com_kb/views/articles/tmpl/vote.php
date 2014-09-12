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

$this->item->helpful = ($this->item->helpful) ? $this->item->helpful : 0;
$this->item->nothelpful = ($this->item->nothelpful) ? $this->item->nothelpful : 0;

$dcls = '';
$lcls = '';

$like_link = JRoute::_('index.php?option='.$this->option.'&task=vote&category='.$this->type.'&id='.$this->item->id.'&vote=like');
$dislike_link = JRoute::_('index.php?option='.$this->option.'&task=vote&category='.$this->type.'&id='.$this->item->id.'&vote=dislike');

if (isset($this->vote)) {
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

<?php if (!$this->vote) : ?>
	<?php if ($juser->get('guest')) : ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button like tooltips" title="Vote Up :: Please login to vote">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $lcls; ?>">
			<span class="vote-button dislike dislike-disabled tooltips" title="Vote down :: Please login to vote">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</span>
		</span>
	<?php else : ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button like tooltips" href="<?php echo $like_link; ?>" title="Vote Up :: I like this">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $lcls; ?>">
			<a class="vote-button dislike tooltips" href="<?php echo $dislike_link; ?>" title="Vote Down :: I dislike this">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</a>
		</span>
	<?php endif; ?>
<?php else : ?>
	<?php if (trim($lcls) == 'chosen') : ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo $like_title; ?>">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; ?> tooltips" href="<?php echo $dislike_link; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</a>
		</span>
	<?php else : ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->helpful > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo $like_link; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->helpful; ?><span> Like</span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->nothelpful > 0) ? 'dislike' : 'neutral'; ?> tooltips" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->nothelpful; ?><span> Dislike</span>
			</span>
		</span>
	<?php endif; ?>
<?php endif; ?>
