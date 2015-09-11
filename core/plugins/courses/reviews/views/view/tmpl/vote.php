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

if (!strstr($this->url, 'index.php'))
{
	$this->url .= '?';
}
else
{
	$this->url .= '&';
}

if ($this->item->get('vote') !== null)
{
	switch ($this->item->get('vote'))
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
}
else
{
	$this->item->set('vote', null);
}

if (!User::isGuest())
{
	$like_title    = Lang::txt('PLG_COURSES_REVIEWS_VOTE_UP', $this->item->get('positive', 0));
	$dislike_title = Lang::txt('PLG_COURSES_REVIEWS_VOTE_DOWN', $this->item->get('negative', 0));
	$cls = ' tooltips';
}
else
{
	$like_title    = Lang::txt('PLG_COURSES_REVIEWS_VOTE_UP_LOGIN');
	$dislike_title = Lang::txt('PLG_COURSES_REVIEWS_VOTE_DOWN_LOGIN');
	$cls = ' tooltips';
}

$no_html = Request::getInt('no_html', 0);

if (!$no_html) { ?>
<p class="comment-voting voting">
<?php } ?>
	<span class="vote-like<?php echo $lcls; ?>">
	<?php if (User::isGuest() || $this->item->get('vote') || User::get('id') == $this->item->get('created_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('positive', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" title="<?php echo $like_title; ?>">
			<?php echo $this->item->get('positive', 0); ?><span> <?php echo Lang::txt('PLG_COURSES_REVIEWS_VOTE_LIKE'); ?></span>
		</span>
	<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->get('positive', 0) > 0) ? 'like' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url($this->url . 'action=vote&voteup=' . $this->item->get('id')); ?>" title="<?php echo $like_title; ?>">
			<?php echo $this->item->get('positive', 0); ?><span> <?php echo Lang::txt('PLG_COURSES_REVIEWS_VOTE_LIKE'); ?></span>
		</a>
	<?php } ?>
	</span>
	<span class="vote-dislike<?php echo $dcls; ?>">
	<?php if (User::isGuest() || $this->item->get('vote') || User::get('id') == $this->item->get('created_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('negative', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" title="<?php echo $dislike_title; ?>">
			<?php echo $this->item->get('negative', 0); ?><span> <?php echo Lang::txt('PLG_COURSES_REVIEWS_VOTE_DISLIKE'); ?></span>
		</span>
	<?php } else { ?>
		<a class="vote-button <?php echo ($this->item->get('negative', 0) > 0) ? 'dislike' : 'neutral'; echo $cls; ?>" href="<?php echo Route::url($this->url . 'action=vote&votedown=' . $this->item->get('id')); ?>" title="<?php echo $dislike_title; ?>">
			<?php echo $this->item->get('negative', 0); ?><span> <?php echo Lang::txt('PLG_COURSES_REVIEWS_VOTE_DISLIKE'); ?></span>
		</a>
	<?php } ?>
	</span>
<?php if (!$no_html) { ?>
</p>
<?php } ?>