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
<?php
// A vote is a small POST form. When this is rendered inside another form
// (the wish's priority table, the list's search form) set 'detached': the
// buttons then point at their form with form="..." and the forms are held
// back, to be printed after the enclosing form by printing
// $GLOBALS['com_wishlist.vote_forms'] (forms can't nest).
$detached = !empty($this->detached);

$voteForm = function ($vote, $id) {
	ob_start();
	?>
	<form<?php echo $id ? ' id="' . $id . '"' : ''; ?> class="inline-form" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&task=rateitem'); ?>">
		<input type="hidden" name="refid" value="<?php echo $this->item->get('id'); ?>" />
		<input type="hidden" name="vote" value="<?php echo $vote; ?>" />
		<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
		<?php foreach ($this->filters as $key => $val) { if ($val) { ?>
			<input type="hidden" name="<?php echo $this->escape($key); ?>" value="<?php echo $this->escape($val); ?>" />
		<?php } } ?>
		<?php echo Html::input('token'); ?>
	<?php
	return ob_get_clean();
};

$voteButton = function ($vote, $label, $count, $title) use ($detached, $voteForm, $cls) {
	$type = ($vote == 'yes') ? 'like' : 'dislike';
	$id   = '';
	if ($detached)
	{
		$n  = isset($GLOBALS['com_wishlist.vote_n']) ? $GLOBALS['com_wishlist.vote_n'] + 1 : 1;
		$GLOBALS['com_wishlist.vote_n'] = $n;
		$id = 'wishvote-' . $this->item->get('id') . '-' . $vote . '-' . $n;
		$GLOBALS['com_wishlist.vote_forms'][] = $voteForm($vote, $id) . '</form>';
	}
	else
	{
		echo $voteForm($vote, '');
	}
	?>
	<button type="submit"<?php echo $id ? ' form="' . $id . '"' : ''; ?> class="vote-button <?php echo $type . $cls; ?>" title="<?php echo Lang::txt($label); ?> :: <?php echo $title; ?>"><?php echo $count; ?><span> <?php echo Lang::txt($vote == 'yes' ? 'COM_WISHLIST_VOTING_LIKE' : 'COM_WISHLIST_VOTING_DISLIKE'); ?></span></button>
	<?php
	if (!$detached)
	{
		echo '</form>';
	}
};
?>
<span class="vote-like<?php echo $lcls; ?>">
	<?php if (User::isGuest() || User::get('id') == $this->item->get('proposed_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('positive') > 0) ? 'like' : 'neutral';
echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_UP'); ?> :: <?php echo $like_title; ?>"><?php echo $this->item->get('positive'); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_LIKE'); ?></span></span>
	<?php } else { ?>
		<?php $voteButton('yes', 'COM_WISHLIST_VOTING_VOTE_UP', $this->item->get('positive', 0), $like_title); ?>
	<?php } ?>
</span>
<span class="vote-dislike<?php echo $dcls; ?>">
	<?php if (User::isGuest() || User::get('id') == $this->item->get('proposed_by')) { ?>
		<span class="vote-button <?php echo ($this->item->get('negative') > 0) ? 'dislike' : 'neutral';
echo $cls; ?>" title="<?php echo Lang::txt('COM_WISHLIST_VOTING_VOTE_DOWN'); ?> :: <?php echo $dislike_title; ?>"><?php echo $this->item->get('negative'); ?><span> <?php echo Lang::txt('COM_WISHLIST_VOTING_DISLIKE'); ?></span></span>
	<?php } else { ?>
		<?php $voteButton('no', 'COM_WISHLIST_VOTING_VOTE_DOWN', $this->item->get('negative', 0), $dislike_title); ?>
	<?php } ?>
</span>