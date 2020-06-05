<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$userLocalizer = new UserLocalizer();
$timezone = $userLocalizer->getTimezone();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->group->published == 1 && in_array(User::get('id'), $this->members)) : ?>
	<ul id="page_options">
		<li>
			<a class="icon-add btn add" title="<?php echo Lang::txt('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT'); ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=add'); ?>">
				<?php echo Lang::txt('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT'); ?>
			</a>
			<?php if ($this->authorized == 'manager') : ?>
				<a class="icon-date btn date" title="<?php echo Lang::txt('Manage Calendars'); ?>" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=calendars'); ?>">
					<?php echo Lang::txt('Manage Calendars'); ?>
				</a>
			<?php endif; ?>
		</li>
	</ul>
<?php endif; ?>

<?php $quickCreate = ($this->params->get('allow_quick_create', 1) && in_array(User::get('id'), $this->group->get('members'))) ? true : 0; ?>
<div id="calendar"
	data-base="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=calendar'); ?>"
	data-month="<?php echo $this->month; ?>"
	data-year="<?php echo $this->year; ?>"
	data-event-quickcreate="<?php echo $quickCreate; ?>"></div>

<select name="calendar" id="calendar-picker">
	<option value="0"><?php echo Lang::txt('All Calendars'); ?></option>
	<?php foreach ($this->calendars as $calendar) : ?>
		<?php $sel = ($calendar->get('id') == $this->calendar) ? 'selected="selected"' : ''; ?>
		<option <?php echo $sel; ?> data-img="<?php echo Request::base(true); ?>/core/plugins/groups/calendar/assets/img/swatch-<?php echo ($calendar->get('color')) ? strtolower($calendar->get('color')) : 'gray'; ?>.png" value="<?php echo $calendar->get('id'); ?>" class="calendar-picker-option"><?php echo $calendar->get('title'); ?></option>
	<?php endforeach; ?>
</select>


<div class="subject group-calendar-subject event-list">
	<div class="container">
		<h3><?php echo Lang::txt('Events List'); ?></h3>
		<?php if ($this->eventsCount > 0) : ?>
			<ol class="calendar-entries">
				<?php foreach ($this->events as $event) : ?>
					<?php
						$params = new \Hubzero\Config\Registry($event->get('params'));
						$ignoreDst = false;
						$ignoreDst = $params->get('ignore_dst') == 1 ? true : false;
					?>
					<li>
						<h4 class="entry-title">
							<a href="<?php echo $event->link(); ?>">
								<?php echo $event->get('title'); ?>
							</a>
						</h4>
						<dl class="entry-meta">
							<dd class="calendar">
								in <?php echo ($event->calendar()->get('id')) ? $event->calendar()->get('title') : 'Uncategorized'; ?>
							</dd>
							<?php if ($event->get('publish_down') && $event->get('publish_down') != '0000-00-00 00:00:00') : ?>
								<dd class="start-and-end">
									<?php
										echo Date::of($event->get('publish_up'))->toTimezone($timezone, 'l, F d, Y @ g:i a', $ignoreDst);
									?>
									&mdash;
									<?php
										echo Date::of($event->get('publish_down'))->toTimezone($timezone, 'l, F d, Y @ g:i a', $ignoreDst);
									?>
								</dd>
							<?php else : ?>
								<dd class="date">
									<?php
										echo Date::of($event->get('publish_up'))->toTimezone($timezone, 'l, F d, Y @ g:i a', $ignoreDst);
									?>
								<dd>
								<dd class="time">
									<?php
										echo Date::of($event->get('publish_up'))->toTimezone($timezone, 'l, F d, Y @ g:i a', $ignoreDst);
									?>
								<dd>
							<?php endif; ?>
						</dl>
						<div class="entry-content">
							<p>
								<?php
									$content = strip_tags($event->get('content'));
									echo ($content) ? Hubzero\Utility\Str::truncate($content, 500) : '<em>no content</em>';
								?>
							</p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>

			<?php
				$pageNav = $this->pagination(
					$this->eventsCount,
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
				$pageNav->setAdditionalUrlParam('active', 'calendar');
				echo $pageNav->render();
			?>
		<?php else : ?>
			<p class="warning"><?php echo Lang::txt('PLG_GROUPS_CALENDAR_NO_ENTRIES_FOUND'); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php
	if ($this->params->get('allow_subscriptions', 1))
	{
		$this->view('subscribe')
			->set('calendar', $this->calendar)
			->set('calendars', $this->calendars)
			->set('group', $this->group)
			->display();
	}
