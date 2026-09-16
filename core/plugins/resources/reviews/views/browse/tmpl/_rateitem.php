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

if ($this->item->get('user_id') == User::get('id'))
{
	$this->item->set('vote', null);
}

if ($vote = $this->item->get('vote'))
{
	switch ($vote)
	{
		case 1:
		case 'yes':
		case 'positive':
		case 'like':
			$lcls = ' chosen';
		break;

		case -1:
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

$vote_action = Route::url('index.php?option='.$this->option.'&id='.$this->item->get('resource_id').'&active=reviews&action=rateitem&refid='.$this->item->get('id'));
?>
<?php if (!$this->item->get('vote')) { ?>
	<?php if (User::isGuest() || $this->item->get('user_id') == User::get('id')) { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
			</span>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
				<input type="hidden" name="vote" value="yes" />
				<?php echo Html::input('token'); ?>
				<button type="submit" class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
				</button>
			</form>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
				<input type="hidden" name="vote" value="no" />
				<?php echo Html::input('token'); ?>
				<button type="submit" class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
				</button>
			</form>
		</span>
	<?php } ?>
<?php } else { ?>
	<?php if (trim($lcls) == 'chosen') { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
				<input type="hidden" name="vote" value="no" />
				<?php echo Html::input('token'); ?>
				<button type="submit" class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
				</button>
			</form>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
				<input type="hidden" name="vote" value="yes" />
				<?php echo Html::input('token'); ?>
				<button type="submit" class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_LIKE', $this->item->get('helpful', 0)); ?>
				</button>
			</form>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_VOTE_DISLIKE', $this->item->get('nothelpful', 0)); ?>
			</span>
		</span>
	<?php } ?>
<?php }
