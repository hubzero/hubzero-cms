<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$mylistIds = array();

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul>
			<li>
				<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_newsletter'); ?>">
					<?php echo Lang::txt('COM_NEWSLETTER_BROWSE'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section class="main section">
	<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
	?>
	<div class="subscribe">
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
			<?php if (count($this->mylists) > 0) : ?>
				<fieldset>
					<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_MYLISTS'); ?></legend>
					<?php foreach ($this->mylists as $mylist) : ?>
						<?php $mylistIds[] = $mylist->id; ?>
						<?php if ($mylist->status != 'removed') : ?>
							<label for="newsletterlist<?php echo $mylist->id; ?>">
								<input type="checkbox" name="lists[]" id="newsletterlist<?php echo $mylist->id; ?>" value="<?php echo $mylist->id; ?>" <?php echo ($mylist->status == 'active' || $mylist->status == 'inactive') ? 'checked="checked"' : ''; ?> />
								<strong><?php echo $this->escape($mylist->name); ?></strong>
								<?php
									if ($mylist->status == 'active' || $mylist->status == 'inactive')
									{
										if (!$mylist->confirmed)
										{
											echo ' - <span title="' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED_TOOLTIP') . '" class="unconfirmed tooltips">' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NOTCONFIRMED') . '</span> <span class="unconfirmed-link">(<a href="'.Route::url('index.php?option=com_newsletter&task=resendconfirmation&mid='.$mylist->id.'&e='.urlencode($this->email)).'" class="">' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_CONFIRMLINK_TEXT') . '</a>)</span>';
										}
									}
									else if ($mylist->status == 'unsubscribed')
									{
										echo ' - <span class="unsubscribed">' . Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBED') . '</span>';
									}
								?>
								<span class="desc">
									<?php echo $mylist->description ? nl2br($mylist->description) : Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION'); ?>
								</span>
							</label>
						<?php endif; ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>

			<?php if (count($this->alllists) > 0) : ?>
				<fieldset>
					<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_PUBLICLISTS'); ?></legend>
					<?php foreach ($this->alllists as $list) : ?>
						<?php
						if (in_array($list->id, $mylistIds))
						{
							continue;
						}
						?>
						<label for="newsletterlist<?php echo $list->id; ?>">
							<input type="checkbox" name="lists[]" id="newsletterlist<?php echo $list->id; ?>" value="<?php echo $list->id; ?>" />
							<strong><?php echo $this->escape($list->name); ?></strong>
							<span class="desc"><?php echo ($list->description) ? nl2br($list->description) : Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LIST_NODESCRIPTION'); ?></span>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
			<?php if (count($this->mylists) > 0 || count($this->alllists) > 0) : ?>
				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_SAVE'); ?>">
				</p>
			<?php else: ?>
				<p class="info">
					<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_NONE'); ?>
				</p>
			<?php endif; ?>
			<input type="hidden" name="e" value="<?php echo urlencode($this->email); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="domultisubscribe" />
			<?php echo Html::input('token'); ?>
		</form>
	</div>
</section>
