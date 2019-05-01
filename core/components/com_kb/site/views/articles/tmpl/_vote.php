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

$like_link    = Route::url($this->item->link('vote') . '&vote=like&' . Session::getFormToken() . '=1');
$dislike_link = Route::url($this->item->link('vote') . '&vote=dislike&' . Session::getFormToken() . '=1');

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
	$cls = ' tooltips';
}
else
{
	$like_title    = Lang::txt('COM_KB_VOTE_UP_LOGIN');
	$dislike_title = Lang::txt('COM_KB_VOTE_DOWN_LOGIN');
	$cls = ' tooltips';
}
?>

<?php if ($this->id) : ?>
	<span class="vote-like">
		<span class="vote-button neutral disabled tooltips" title="<?php echo $like_title; ?>">
			<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_LIKE'); ?></span>
		</span>
	</span>
	<span class="vote-dislike">
		<span class="vote-button neutral disabled tooltips" title="<?php echo $dislike_title; ?>">
			<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_DISLIKE'); ?></span>
		</span>
	</span>
<?php else : ?>
	<?php if (!$this->vote) : ?>
		<?php if (User::isGuest()) : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<span class="vote-button like tooltips" title="<?php echo $like_title; ?>">
					<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_LIKE'); ?></span>
				</span>
			</span>
			<span class="vote-dislike<?php echo $lcls; ?>">
				<span class="vote-button dislike dislike-disabled tooltips" title="<?php echo $dislike_title; ?>">
					<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_DISLIKE'); ?></span>
				</span>
			</span>
		<?php else : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<a class="vote-button like tooltips" href="<?php echo $like_link; ?>" title="<?php echo $like_title; ?>">
					<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_LIKE'); ?></span>
				</a>
			</span>
			<span class="vote-dislike<?php echo $lcls; ?>">
				<a class="vote-button dislike tooltips" href="<?php echo $dislike_link; ?>" title="<?php echo $dislike_title; ?>">
					<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_DISLIKE'); ?></span>
				</a>
			</span>
		<?php endif; ?>
	<?php else : ?>
		<?php if (trim($lcls) == 'chosen') : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<span class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo $like_title; ?>">
					<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_LIKE'); ?></span>
				</span>
			</span>
			<span class="vote-dislike<?php echo $dcls; ?>">
				<a class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; ?> tooltips" href="<?php echo $dislike_link; ?>" title="<?php echo $dislike_title; ?>">
					<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_DISLIKE'); ?></span>
				</a>
			</span>
		<?php else : ?>
			<span class="vote-like<?php echo $lcls; ?>">
				<a class="vote-button <?php echo ($this->item->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo $like_link; ?>" title="<?php echo $like_title; ?>">
					<?php echo $this->item->get('helpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_LIKE'); ?></span>
				</a>
			</span>
			<span class="vote-dislike<?php echo $dcls; ?>">
				<span class="vote-button <?php echo ($this->item->get('nothelpful', 0) > 0) ? 'dislike' : 'neutral'; ?> tooltips" title="<?php echo $dislike_title; ?>">
					<?php echo $this->item->get('nothelpful', 0); ?><span> <?php echo Lang::txt('COM_KB_VOTE_DISLIKE'); ?></span>
				</span>
			</span>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; 