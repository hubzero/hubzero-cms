<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER_SEND') . ': ' . $this->newsletter->name, 'newsletter');

//add buttons to toolbar
Toolbar::custom('dosendnewsletter', 'send', '', 'COM_NEWSLETTER_TOOLBAR_SEND', false);
Toolbar::cancel();

// add jquery ui
Html::behavior('framework', true);

// add newsletter js
$this->js();
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<div class="col span12">
		<?php if ($this->newsletter->id != null) : ?>
			<a name="distribution"></a>
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_NEWSLETTER_SEND'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER'); ?>:</th>
							<td>
								<?php echo $this->newsletter->name; ?>
								<input type="hidden" name="newsletter-name" id="newsletter-name" value="<?php echo $this->escape($this->newsletter->name); ?>" />
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SENT_PREVIOUSLY'); ?>:</th>
							<td>
								<?php if (count($this->mailings) > 0) : ?>
									<?php foreach ($this->mailings as $mailing) : ?>
										<?php
											$status = 'In Progress';
											$color = 'DarkGoldenRod';
											$sent = Date::of($mailing->date);
											$now  = Date::of('now');

											// is mailing scheduled?
											if ($sent > $now)
											{
												$status = 'Scheduled';
												$color  = 'DodgerBlue';
											}
											// is mailing fully sent?
											else if ($mailing->recipients()->whereEquals('status', 'queued')->total() == 0)
											{
												$status = 'Sent';
												$color  = 'ForestGreen';
											}
										?>
										<strong>
											<font color="<?php echo $color; ?>"><?php echo $status; ?></font> - 
										</strong>
										<?php echo $sent->toLocal("l, F d, Y @ g:ia"); ?>
										<br />
									<?php endforeach; ?>
								<?php else : ?>
									<strong>
										<font color="red"><?php echo Lang::txt('JNO'); ?></font>
									</strong>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE'); ?>:</th>
							<td>
								<div id="scheduler">
									<input type="radio" name="scheduler" value="1" checked="checked" /> <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_NOW'); ?> <br />
									<input type="radio" name="scheduler" value="0" /> <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER'); ?> <br />

									<div id="scheduler-alt">
										<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_DATE'); ?>
										<input type="text" name="scheduler_date" id="scheduler_date" class="width-auto" />

										<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_TIME'); ?>
										<select name="scheduler_date_hour" id="scheduler_date_hour" class="width-auto">
											<option value=""><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_NULL'); ?></option>
											<?php for ($i = 1, $n = 13; $i < $n; $i++) : ?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
											<?php endfor; ?>
										</select>

										<select name="scheduler_date_minute" id="scheduler_date_minute" class="width-auto">
											<option value=""><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_NULL'); ?></option>
											<?php for ($i = 0, $n = 60; $i < $n; $i+=5) : ?>
												<?php
													if ($i == '0')
													{
														$i = '00';
													}
													else if ($i == '5')
													{
														$i = '05';
													}

												?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
											<?php endfor; ?>
										</select>

										<select name="scheduler_date_meridian" id="scheduler_date_meridian" class="width-auto">
											<option value=""><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_NULL'); ?></option>
											<option value="am"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_AM'); ?></option>
											<option value="pm"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_OPTION_PM'); ?></option>
										</select>
										<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_SCHEDULE_LATER_EST'); ?>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST'); ?></th>
							<td>
								<select name="mailinglist" id="mailinglist">
									<option value=""><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST_OPTION_NULL'); ?></option>
									<option value="-1"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST_OPTION_DEFAULT'); ?></option>
									<?php foreach ($this->mailinglists as $list) : ?>
										<option value="<?php echo $list->id; ?>"><?php echo $this->escape($list->name); ?></option>
									<?php endforeach; ?>
								</select>
								<br /><br />
								<p id="mailinglist-count">
									<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SEND_MAILINGLIST_RECIEVE'); ?><span id="mailinglist-count-count"></span>
									<span id="mailinglist-emails"></span>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="dosendnewsletter" />
	<input type="hidden" name="nid" value="<?php echo $this->newsletter->id; ?>" />

	<?php echo Html::input('token'); ?>
</form>