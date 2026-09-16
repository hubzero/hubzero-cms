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

$vote_action = Route::url($this->item->link('vote'));

if (isset($this->vote))
{
	switch ($this->vote)
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
	$this->vote = null;
}

if (!User::isGuest())
{
	$like_title    = Lang::txt('COM_KB_VOTE_UP', $this->item->get('helpful', 0));
	$dislike_title = Lang::txt('COM_KB_VOTE_DOWN', $this->item->get('nothelpful', 0));
}
else
{
	$like_title    = Lang::txt('COM_KB_VOTE_UP_LOGIN');
	$dislike_title = Lang::txt('COM_KB_VOTE_DOWN_LOGIN');
}
?>

<?php if ($this->id) : ?>
	<span class="vote-like">
		<span class="vote-button neutral disabled">
			<?php echo $this->item->get('helpful', 0); ?><span class="sr-only"> <?php echo $like_title; ?></span>
		</span>
	</span>
	<span class="vote-dislike">
		<span class="vote-button neutral disabled">
			<?php echo $this->item->get('nothelpful', 0); ?><span class="sr-only"> <?php echo $dislike_title; ?></span>
		</span>
	</span>
<?php else : ?>
	<?php if (!$this->vote) : ?>
		<?php if (User::isGuest()) : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<span class="vote-button like">
					<?php echo $this->item->get('helpful', 0); ?><span class="sr-only"> <?php echo $like_title; ?></span>
				</span>
			</span>
			<span class="vote-dislike<?php echo $lcls; ?>">
				<span class="vote-button dislike dislike-disabled">
					<?php echo $this->item->get('nothelpful', 0); ?><span class="sr-only"> <?php echo $dislike_title; ?></span>
				</span>
			</span>
		<?php else : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
					<input type="hidden" name="vote" value="like" />
					<?php echo Html::input('token'); ?>
					<button type="submit" class="vote-button like" aria-label="<?php echo $like_title; ?>">
						<?php echo $this->item->get('helpful', 0); ?><span class="sr-only"> <?php echo Lang::txt('COM_KB_VOTE_LIKE'); ?></span>
					</button>
				</form>
			</span>
			<span class="vote-dislike<?php echo $lcls; ?>">
				<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
					<input type="hidden" name="vote" value="dislike" />
					<?php echo Html::input('token'); ?>
					<button type="submit" class="vote-button dislike" aria-label="<?php echo $dislike_title; ?>">
						<?php echo $this->item->get('nothelpful', 0); ?><span class="sr-only"> <?php echo Lang::txt('COM_KB_VOTE_DISLIKE'); ?></span>
					</button>
				</form>
			</span>
		<?php endif; ?>
	<?php else : ?>
		<?php if (trim($lcls) == 'chosen') : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?>">
					<?php echo $this->item->get('helpful', 0); ?><span class="sr-only"> <?php echo $like_title; ?></span>
				</span>
			</span>
			<span class="vote-dislike<?php echo $dcls; ?>">
				<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
					<input type="hidden" name="vote" value="dislike" />
					<?php echo Html::input('token'); ?>
					<button type="submit" class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; ?>" aria-label="<?php echo $dislike_title; ?>">
						<?php echo $this->item->get('nothelpful', 0); ?><span class="sr-only"> <?php echo Lang::txt('COM_KB_VOTE_DISLIKE'); ?></span>
					</button>
				</form>
			</span>
		<?php else : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<form class="inline-form" method="post" action="<?php echo $vote_action; ?>">
					<input type="hidden" name="vote" value="like" />
					<?php echo Html::input('token'); ?>
					<button type="submit" class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?>" aria-label="<?php echo $like_title; ?>">
						<?php echo $this->item->get('helpful', 0); ?><span class="sr-only"> <?php echo Lang::txt('COM_KB_VOTE_LIKE'); ?></span>
					</button>
				</form>
			</span>
			<span class="vote-dislike<?php echo $dcls; ?>">
				<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; ?>">
					<?php echo $this->item->get('nothelpful', 0); ?><span class="sr-only"> <?php echo $dislike_title; ?></span>
				</span>
			</span>
		<?php endif; ?>
	<?php endif; ?>
<?php endif;
