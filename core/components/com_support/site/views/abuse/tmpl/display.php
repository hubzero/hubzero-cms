<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use \Hubzero\Utility\Sanitize;

// No direct access.
defined('_HZEXEC_') or die();

$no_html = Request::getInt('no_html', 0);

if (!$no_html)
{
	$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
<?php } ?>
	<?php if ($this->report) { ?>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=reportabuse'); ?>" method="post" id="hubForm<?php if ($no_html) { echo '-ajax'; } ?>">
			<?php if (!$no_html) { ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_EXPLANATION'); ?></p>
				<p><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_DESCRIPTION_HINT'); ?></p>
			</div>
			<?php } ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE'); ?></legend>

				<?php if (!$no_html) { ?>
				<div class="field-wrap">
					<div class="abuseitem">
						<p><?php
							$name = Lang::txt('JANONYMOUS');

							if ($this->report->anon == 0)
							{
								$user = User::getInstance($this->report->author);
								$name = Lang::txt('COM_SUPPORT_UNKNOWN');
								if (is_object($user))
								{
									$name = $user->get('name');
								}
							}

							echo ($this->report->href) ? '<a href="' . $this->report->href . '">': '';
							echo ucfirst($this->cat) . ' by ';
							echo ($this->report->anon != 0) ? Lang::txt('JANONYMOUS') : $name;
							echo ($this->report->href) ? '</a>': '';
						?></p>
						<?php echo ($this->report->subject) ? '<p><strong>'.stripslashes($this->report->subject).'</strong></p>' : ''; ?>
						<blockquote cite="<?php echo ($this->report->anon != 0) ? Lang::txt('COM_SUPPORT_ANONYMOUS') : $name; ?>">
							<p><?php echo Sanitize::html($this->report->text); ?></p>
						</blockquote>
					</div>
				</div>
				<?php } ?>

				<fieldset class="multiple-option">
					<legend><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_REASON'); ?></legend>

					<div class="form-group form-check">
						<label class="option form-check-label" for="subject1">
							<input type="radio" class="option form-check-input" name="subject" id="subject1" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OFFENSIVE'); ?>" checked="checked" />
							<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OFFENSIVE'); ?>
						</label>
						<label class="option form-check-label" for="subject2">
							<input type="radio" class="option form-check-input" name="subject" id="subject2" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_STUPID'); ?>" />
							<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_STUPID'); ?>
						</label>
						<label class="option form-check-label" for="subject3">
							<input type="radio" class="option form-check-input" name="subject" id="subject3" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_SPAM'); ?>" />
							<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_SPAM'); ?>
						</label>
						<label class="option form-check-label" for="subject4">
							<input type="radio" class="option form-check-input" name="subject" id="subject4" value="<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OTHER'); ?>" />
							<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_OTHER'); ?>
						</label>
					</div>
				</fieldset>

				<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="category" value="<?php echo $this->escape($this->cat); ?>" />
				<input type="hidden" name="referenceid" value="<?php echo $this->escape($this->refid); ?>" />
				<input type="hidden" name="link" value="<?php echo $this->escape($this->report->href); ?>" />
				<input type="hidden" name="no_html" value="<?php echo $no_html; ?>" />

				<?php echo Html::input('token'); ?>

				<div class="form-group">
					<label for="field-report">
						<?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_DESCRIPTION'); ?>
						<textarea name="report" id="field-report" class="form-control" rows="10" cols="50"></textarea>
					</label>
				</div>
			</fieldset>
			<p class="submit">
				<input type="submit" class="btn btn-danger" value="<?php echo Lang::txt('COM_SUPPORT_SUBMIT'); ?>" />

				<a class="btn btn-secondary" href="<?php echo $this->report->href; ?>">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			</p>
		</form>
		<div class="clear"></div>
	<?php } else { ?>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p class="warning"><?php echo Lang::txt('COM_SUPPORT_ERROR_NO_INFO_ON_REPORTED_ITEM'); ?></p>
		<?php } ?>
	<?php } ?>
<?php if (!$no_html) { ?>
</section><!-- / .main section -->
<?php }
