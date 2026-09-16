<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dcls = '';
$lcls = '';
$this->url = preg_replace('/\#[^\#]*$/', '', $this->url);
if (!strstr($this->url, '?'))
{
	$this->url .= '?';
}
else
{
	$this->url .= '&';
}

if ($vote = $this->item->get('vote'))
{
	switch ($vote)
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
	$like_title    = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_UP', $this->item->get('positive', 0));
	$dislike_title = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DOWN', $this->item->get('negative', 0));
	$cls = ' tooltips';
}
else
{
	$like_title    = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_UP_LOGIN');
	$dislike_title = Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DOWN_LOGIN');
	$cls = ' tooltips';
}

$no_html = Request::getInt('no_html', 0);

if (!$no_html) { ?>
<p class="comment-voting voting">
<?php } ?>
	<span class="vote-like<?php echo $lcls; ?>">
		<?php if ($this->item->get('vote') || User::get('id') == $this->item->get('created_by')) { // || !$this->params->get('access-vote-comment')) { ?>
			<span class="vote-button <?php echo ($this->item->get('positive', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('positive', 0); ?><span> <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_LIKE'); ?></span>
			</span>
		<?php } else { ?>
			<form class="inline-form" method="post" action="<?php echo Route::url($this->url . 'action=commentvote'); ?>">
				<input type="hidden" name="voteup" value="<?php echo $this->item->get('id'); ?>" />
				<?php echo Html::input('token'); ?>
				<button type="submit" class="vote-button <?php echo ($this->item->get('positive', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
					<?php echo $this->item->get('positive', 0); ?><span> <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_LIKE'); ?></span>
				</button>
			</form>
		<?php } ?>
	</span>
	<span class="vote-dislike<?php echo $dcls; ?>">
		<?php if ($this->item->get('vote') || User::get('id') == $this->item->get('created_by')) { ?>
			<span class="vote-button <?php echo ($this->item->get('negative', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('negative', 0); ?><span> <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DISLIKE'); ?></span>
			</span>
		<?php } else { ?>
			<form class="inline-form" method="post" action="<?php echo Route::url($this->url . 'action=commentvote'); ?>">
				<input type="hidden" name="votedown" value="<?php echo $this->item->get('id'); ?>" />
				<?php echo Html::input('token'); ?>
				<button type="submit" class="vote-button <?php echo ($this->item->get('negative', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
					<?php echo $this->item->get('negative', 0); ?><span> <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_VOTE_DISLIKE'); ?></span>
				</button>
			</form>
		<?php } ?>
	</span>
<?php if (!$no_html) { ?>
</p>
<?php }
