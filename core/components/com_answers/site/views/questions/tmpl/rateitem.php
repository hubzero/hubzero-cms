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

// No direct access
defined('_HZEXEC_') or die();

$dcls = '';
$lcls = '';

if ($this->item->get('vote', null))
{
	switch ($this->item->get('vote'))
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

if (!User::isGuest())
{
	$like_title    = Lang::txt('COM_ANSWERS_VOTE_LIKE_TITLE', $this->item->get('helpful', 0));
	$dislike_title = Lang::txt('COM_ANSWERS_VOTE_DISLIKE_TITLE', $this->item->get('nothelpful', 0));
	$cls = ' tooltips';
}
else
{
	$like_title    = Lang::txt('COM_ANSWERS_VOTE_LIKE_LOGIN');
	$dislike_title = Lang::txt('COM_ANSWERS_VOTE_DISLIKE_LOGIN');
	$cls = ' tooltips';
}
?>
<?php if (!$this->item->get('vote')) { ?>
	<?php if (User::isGuest()) { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</span>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</a>
		</span>
	<?php } ?>
<?php } else { ?>
	<?php if (trim($lcls) == 'chosen') { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</a>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=rateitem&refid=' . $this->item->get('id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</span>
		</span>
	<?php } ?>
<?php } ?>