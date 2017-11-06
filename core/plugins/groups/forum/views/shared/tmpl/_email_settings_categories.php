<?php
	$base = $this->base;
	$preexistingSubscriptionIds = array();
	$currentUserId = User::get('id');
?>

<form method="post" id="email-settings">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS'); ?></legend>

		<span class="fieldset-subtitle">
			<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CATEGORIES'); ?>
		</span>

		<?php	foreach($this->categories as $category): ?>
			<?php
				$usersSubscription = $category->usersCategories()
					->whereEquals('user_id', $currentUserId)
					->row();

				$checked = '';

				if (!$usersSubscription->isNew())
			  {
					$checked = 'checked';
					$preexistingSubscriptionIds[] = $category->get('id');
			  }
			?>
			<span class="line-item">
				<input type="checkbox" name="<?php echo $category->get('id'); ?>"<?php echo $checked; ?>>
				<?php echo $category->get('title'); ?>
			</span>
		<?php endforeach; ?>

		<?php echo Html::input('token'); ?>
		<input type="hidden" id="user-id" value="<?php echo $currentUserId; ?>">
		<input type="hidden" id="preexisting-subscriptions" value="<?php echo implode($preexistingSubscriptionIds, ','); ?>">

		<input class="option" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />

	</fieldset>
</form>

<style>
	.fieldset-subtitle {
		display: block;
		margin: 0 0 5px 0;
	}
	.line-item {
		display: block;
		margin: 0 0 10px 0;
	}
</style>

<?php
	Html::behavior('core');
	$this->js('notify');
	$this->js('emailSettings');
?>
