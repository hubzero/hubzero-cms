<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
			<fieldset>
				<legend><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE'); ?></legend>
				<p><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE_DESC'); ?></p>

				<p>
					<strong><?php echo $this->escape($this->mailinglist->name); ?></strong><br />
					<span><?php echo $this->escape($this->mailinglist->description); ?></span>
					<input type="hidden" name="t" value="<?php echo $this->escape(Request::getString('t', '')); ?>" />
					<input type="hidden" name="e" value="<?php echo $this->escape(Request::getString('e', '')); ?>" />
				</p>

				<?php if ($this->mailinglist->id == '-1' && User::get('guest') == 1) : ?>
					<ol>
						<li>
							<?php if (User::isGuest()) : ?>
								<a href="login?return=<?php echo base64_encode( $_SERVER['REQUEST_URI'] ); ?>">
									<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LOGIN_TO_UNSUBSCRIBE'); ?>
								</a>
							<?php else : ?>
								<span class="complete">
									<?php echo Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LOGGEDIN_AS', User::get('username')); ?>
								</span>
							<?php endif; ?>
						</li>
					</ol>
				<?php else : ?>
					<label><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON'); ?>
						<select name="reason" id="reason">
							<option value=""><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_DEFAULT'); ?></option>
							<option value="Too many emails"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_TOOMANY'); ?></option>
							<option value="Content isn't relevant to me"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTRELEVANT'); ?></option>
							<option value="I don't remember signing up"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTSIGNEDUP'); ?></option>
							<option value="Privacy concerns"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_PRIVACY'); ?></option>
							<option value="Other"><?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER'); ?></option>
						</select>
					</label>

					<label>
						<textarea rows="4" name="reason-alt" id="reason-alt" placeholder="<?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER_OTHER'); ?>"></textarea>
					</label>
				<?php endif; ?>
			</fieldset>
			<?php if (!User::isGuest() || $this->mailinglist->id != '-1') : ?>
				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE'); ?>">
				</p>
			<?php endif; ?>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="dounsubscribe" />
		</form>
	</div>
</section>