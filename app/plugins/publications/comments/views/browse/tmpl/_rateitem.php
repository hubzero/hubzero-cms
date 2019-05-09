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
	$like_title = 'Vote this up :: ' . $this->item->get('helpful', 0) . ' people liked this';
	$dislike_title = 'Vote this down :: ' . $this->item->get('nothelpful', 0) . ' people did not like this';
	$cls = ' tooltips';
}
else
{
	$like_title = 'Vote this up :: Please login to vote.';
	$dislike_title = 'Vote this down :: Please login to vote.';
	$cls = ' tooltips';
}
?>
<?php if (!$this->item->get('vote')) { ?>
	<?php if (User::isGuest()) { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') .  $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> Like</span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') .  $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
			</span>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->item->get('publication_id') . '&active=reviews&action=rateitem&refid=' . $this->item->get('id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> Like</span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->item->get('publication_id') . '&active=reviews&action=rateitem&refid=' . $this->item->get('id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
			</a>
		</span>
	<?php } ?>
<?php } else { ?>
	<?php if (trim($lcls) == 'chosen') { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> Like</span>
			</span>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->item->get('publication_id') . '&active=reviews&action=rateitem&refid=' . $this->item->get('id') . '&vote=no'); ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
			</a>
		</span>
	<?php } else { ?>
		<span class="vote-like<?php echo $lcls; ?>">
			<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0 ? 'like' : 'neutral') . $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->item->get('publication_id') . '&active=reviews&action=rateitem&refid=' . $this->item->get('id') . '&vote=yes'); ?>" title="<?php echo $like_title; ?>">
				<?php echo $this->item->get('helpful', 0); ?><span> Like</span>
			</a>
		</span>
		<span class="vote-dislike<?php echo $dcls; ?>">
			<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0 ? 'dislike' : 'neutral') . $cls; ?>" title="<?php echo $dislike_title; ?>">
				<?php echo $this->item->get('nothelpful', 0); ?><span> Dislike</span>
			</span>
		</span>
	<?php } ?>
<?php }
