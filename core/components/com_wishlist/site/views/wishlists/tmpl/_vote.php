<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// import filters
$filterln = '';
foreach ($this->filters as $key => $val)
{
	if ($val)
	{
		$filterln .= '&' . $key . '=' . $val;
	}
}

$dcls = '';
$lcls = '';
$cls  = ' tooltips';

if (!User::isGuest())
{
	// Logged in
	$like_title    = Lang::txt('COM_WISHLIST_VOTING_I_LIKE_THIS');
	$dislike_title = Lang::txt('COM_WISHLIST_VOTING_I_DISLIKE_THIS');

	if ($this->item->get('vote'))
	{
		$like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_ALREADY_VOTED');
		if ($this->item->get('vote') == $this->item->get('positive'))
		{
			$lcls = ' chosen';
		}
		if ($this->item->get('vote') == $this->item->get('negative'))
		{
			$dcls = ' chosen';
		}
	}
	if (User::get('id') == $this->item->get('proposed_by'))
	{
		$like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_CANNOT_VOTE_FOR_OWN');
	}
	if ($this->item->get('status') == 1
	 || $this->item->get('status') == 3
	 || $this->item->get('status') == 4)
	{
		$like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_CLOED');
	}
}
else
{
	// Not logged in
	$like_title = $dislike_title = Lang::txt('COM_WISHLIST_VOTING_LOGIN_TO_VOTE');
}
?>
<span class="vote-like<?php echo $lcls; ?>">
	<?php if (User::isGuest() || User::get('id') == $this->item->get('proposed_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('positive') > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->get('positive'); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_LIKE'); ?></span></span>
	<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->get('positive') > 0) ? 'like' : 'like'; echo $cls; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->get('id').'&vote=yes&page='.$this->page.$filterln); ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->get('positive', 0); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_LIKE'); ?></span></a>
	<?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
	<?php if (User::isGuest() || User::get('id') == $this->item->get('proposed_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('negative') > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->get('negative'); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_DISLIKE'); ?></span></span>
	<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->get('negative') > 0) ? 'dislike' : 'dislike'; echo $cls; ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&task=rateitem&refid='.$this->item->get('id').'&vote=no&page='.$this->page.$filterln); ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->get('negative', 0); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_DISLIKE'); ?></span></a>
	<?php } ?>
</span>