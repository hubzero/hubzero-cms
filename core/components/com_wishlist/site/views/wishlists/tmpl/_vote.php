<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// import filters
$filterln = '&' . Session::getFormToken() . '=1';
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
		<span class="vote-button <?php echo ($this->item->get('positive') > 0) ? 'like' : 'neutral';
echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->get('positive'); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_LIKE'); ?></span></span>
	<?php } else { ?>
		<form class="inline-form" method="post" action="<?php echo Route::url('index.php?option='.$this->option.'&task=rateitem'); ?>">
			<input type="hidden" name="refid" value="<?php echo $this->item->get('id'); ?>" />
			<input type="hidden" name="vote" value="yes" />
			<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
			<?php foreach ($this->filters as $key => $val) { if ($val) { ?>
				<input type="hidden" name="<?php echo $this->escape($key); ?>" value="<?php echo $this->escape($val); ?>" />
			<?php } } ?>
			<?php echo Html::input('token'); ?>
			<button type="submit" class="vote-button <?php echo ($this->item->get('positive') > 0) ? 'like' : 'like';
echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->get('positive', 0); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_LIKE'); ?></span></button>
		</form>
	<?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
	<?php if (User::isGuest() || User::get('id') == $this->item->get('proposed_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('negative') > 0) ? 'dislike' : 'neutral';
echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->get('negative'); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_DISLIKE'); ?></span></span>
	<?php } else { ?>
		<form class="inline-form" method="post" action="<?php echo Route::url('index.php?option='.$this->option.'&task=rateitem'); ?>">
			<input type="hidden" name="refid" value="<?php echo $this->item->get('id'); ?>" />
			<input type="hidden" name="vote" value="no" />
			<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
			<?php foreach ($this->filters as $key => $val) { if ($val) { ?>
				<input type="hidden" name="<?php echo $this->escape($key); ?>" value="<?php echo $this->escape($val); ?>" />
			<?php } } ?>
			<?php echo Html::input('token'); ?>
			<button type="submit" class="vote-button <?php echo ($this->item->get('negative') > 0) ? 'dislike' : 'dislike';
echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->get('negative', 0); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_DISLIKE'); ?></span></button>
		</form>
	<?php } ?>
</span>