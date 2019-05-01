<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dcls = '';
$lcls = '';

if ($ballot = $this->vote->get('vote', null))
{
	switch ($ballot)
	{
		case 1:
			$lcls = ' chosen';
		break;

		case -1:
			$dcls = ' chosen';
		break;
	}
}

if ($pos = $this->item->get('positive'))
{
	$this->item->set('helpful', $pos);
}
if ($neg = $this->item->get('negative'))
{
	$this->item->set('nothelpful', $neg);
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
<?php if (!$this->vote->get('id')) { ?>
	<?php if (User::isGuest() || User::get('id') == $this->item->get('created_by')) { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral';
echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral';
echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</span>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral';
echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=vote&category=' . $this->vote->get('item_type') . '&id=' . $this->vote->get('item_id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral';
echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=vote&category=' . $this->vote->get('item_type') . '&id=' . $this->vote->get('item_id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</a>
		</span>
	<?php } ?>
<?php } else { ?>
	<?php if (trim($lcls) == 'chosen') { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral';
echo $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral';
echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=vote&category=' . $this->vote->get('item_type') . '&id=' . $this->vote->get('item_id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</a>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral';
echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=vote&category=' . $this->vote->get('item_type') . '&id=' . $this->vote->get('item_id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_LIKE'); ?></span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral';
echo $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_ANSWERS_VOTE_DISLIKE'); ?></span>
			</span>
		</span>
	<?php } ?>
<?php } 