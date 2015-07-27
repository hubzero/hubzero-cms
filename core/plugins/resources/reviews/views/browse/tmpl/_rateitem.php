<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$dcls = '';
$lcls = '';

if ($this->item->get('user_id') == User::get('id'))
{
	$this->item->set('vote', null);
}

if ($vote = $this->item->get('vote'))
{
	switch ($vote)
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
	$this->item->set('vote', null);
}

if (!User::isGuest())
{
	$like_title    = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_UP', $this->item->get('helpful', 0));
	$dislike_title = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DOWN', $this->item->get('nothelpful', 0));
	$cls = ' tooltips';
}
else
{
	$like_title    = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_UP_LOGIN');
	$dislike_title = Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DOWN_LOGIN');
	$cls = ' tooltips';
}
?>
<?php if (!$this->item->get('vote')) { ?>
	<?php if (User::isGuest() || $this->item->get('user_id') == User::get('id')) { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
			</span>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&id='.$this->item->get('resource_id').'&active=reviews&action=rateitem&refid='.$this->item->get('id').'&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&id='.$this->item->get('resource_id').'&active=reviews&action=rateitem&refid='.$this->item->get('id').'&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
			</a>
		</span>
	<?php } ?>
<?php } else { ?>
	<?php if (trim($lcls) == 'chosen') { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&id='.$this->item->get('resource_id').'&active=reviews&action=rateitem&refid='.$this->item->get('id').'&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
			</a>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&id='.$this->item->get('resource_id').'&active=reviews&action=rateitem&refid='.$this->item->get('id').'&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
			</span>
		</span>
	<?php } ?>
<?php } ?>