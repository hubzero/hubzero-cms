<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//
$thisCalendar = new stdClass;
$thisCalendar->id        = 0;
$thisCalendar->published = 1;
$thisCalendar->title     = "All Calendars";
foreach ($this->calendars as $calendar)
{
	if ($calendar->get('id') == $this->calendar)
	{
		$thisCalendar = $calendar;
	}
}
?>

<div class="subject group-calendar-subject subscribe">
	<div class="container">
		<h3>
			<?php echo Lang::txt('Subscribe'); ?>
			<a class="popup subscribe-help" href="<?php echo Route::url('index.php?option=com_help&component=groups&extension=calendar&page=subscriptions'); ?>">
				<?php echo Lang::txt('Need Help?'); ?>
			</a>
		</h3>

		<div class="subscribe-content">
			<p class="info">
				<?php echo Lang::txt('If you are prompted to enter a username & password when subscribing to a calendar, enter your HUB credentials.'); ?>
			</p>

			<p>
				<strong><?php echo Lang::txt('Select the calendars you wish to subscribe to:'); ?></strong>
			</p>

			<label>
				<input type="checkbox" value="0" checked="checked" />
				<img src="<?php echo Request::base(true); ?>/core/plugins/groups/calendar/assets/img/swatch-gray.png" />
				<?php echo Lang::txt('Uncategorized Events'); ?>
			</label>

			<?php $cals = array(0); ?>
			<?php foreach ($this->calendars as $calendar) : ?>
				<?php
					$enabled = false;
					if ($calendar->get('published') == 1)
					{
						$enabled = true;
						$cals[] = $calendar->get('id');
					}
				?>
				<label <?php echo (!$enabled) ? 'class="disabled"' : '' ?>>
					<input <?php echo (!$enabled) ? 'disabled="disabled"' : 'checked="checked"'; ?> name="subscribe[]"  type="checkbox" value="<?php echo $calendar->get('id'); ?>" />
					<?php if ($calendar->get('color')) : ?>
						<img src="<?php echo Request::base(true); ?>/core/plugins/groups/calendar/assets/img/swatch-<?php echo $calendar->get('color'); ?>.png" alt="<?php echo $calendar->get('color'); ?>" />
					<?php else : ?>
						<img src="<?php echo Request::base(true); ?>/core/plugins/groups/calendar/assets/img/swatch-gray.png" alt="gray" />
					<?php endif; ?>
					<?php echo $calendar->get('title'); ?>
					<?php
						if (!$enabled)
						{
							echo Lang::txt('(Calendar is not publishing events.)');
						}
					?>
				</label>
			<?php endforeach; ?>

			<?php
				$link = $_SERVER['HTTP_HOST'] . DS . 'groups' . DS . $this->group->get('cn') . DS . 'calendar' . DS . 'subscribe' . DS . implode(',', $cals) . '.ics';
				$httpsLink = 'https://' . $link;
				$webcalLink = 'webcal://' . $link;
			?>
			<br />
			<label id="subscribe-link"><strong><?php echo Lang::txt('Click the subscribe button to the right or add the link below to add as a calendar subscription:'); ?></strong>
				<input type="text" value="<?php echo $httpsLink; ?>" />
				<a class="btn feed download https" href="<?php echo $httpsLink; ?>">Download</a>
				<a class="btn feed subscribe-webcal webcal" href="<?php echo $webcalLink; ?>">Subscribe</a>
			</label>

		</div>
	</div>
</div>