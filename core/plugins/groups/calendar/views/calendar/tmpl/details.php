<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$year  = date("Y", strtotime($this->event->get('publish_up')));
$month = date("m", strtotime($this->event->get('publish_up')));
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-prev btn back" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$year.'&month='.$month); ?>">
			<?php echo Lang::txt('Back to Events Calendar'); ?>
		</a>
	</li>
</ul>

<div class="event-title-bar">
	<span class="event-title">
		<?php echo $this->event->get('title'); ?>
		<?php if (isset($this->calendar)) : ?>
			<span>&ndash;&nbsp;<?php echo $this->calendar->get('title'); ?></span>
		<?php endif; ?>
	</span>
	<?php if ($this->group->published == 1 && ($this->user->get('id') == $this->event->get('created_by') || $this->authorized == 'manager')) : ?>
		<?php if (!isset($this->calendar) || !$this->calendar->get('readonly')) : ?>
			<a class="delete" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=delete&event_id='.$this->event->get('id')); ?>">
				Delete
			</a>
			<a class="edit" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=edit&event_id='.$this->event->get('id')); ?>">
				Edit
			</a>
		<?php endif; ?>
	<?php endif; ?>
</div>

<div class="event-sub-menu">
	<ul>
		<li class="active">
			<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=details&event_id='.$this->event->get('id')); ?>">
				<span><?php echo Lang::txt('Details'); ?></span>
			</a>
		</li>

		<?php if ($this->event->get('registerby') != '' && $this->event->get('registerby') != '0000-00-00 00:00:00') : ?>
			<li>
				<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=register&event_id='.$this->event->get('id')); ?>">
					<span><?php echo Lang::txt('Register'); ?></span>
				</a>
			</li>
			<?php if ($this->user->get('id') == $this->event->get('created_by') || $this->authorized == 'manager') : ?>
				<li>
					<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=registrants&event_id='.$this->event->get('id')); ?>">
						<span><?php echo Lang::txt('Registrants ('.$this->registrants.')'); ?></span>
					</a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<table class="group-event-details">
	<tbody>
		<?php
			$timezone     = timezone_name_from_abbr('', $this->event->get('time_zone')*3600, null);
			$publish_up   = $this->event->get('publish_up');
			$publish_down = $this->event->get('publish_down');
			$allday_event = $this->event->get('allday');

			// show alternative event start/ends
			// used for repeating events
			$start = Request::getInt('start', null, 'get');
			$end   = Request::getInt('end', null, 'get');

			if ($start || ($start && $end))
			{
				$publish_up   = Date::of($start)->toSql();
				$publish_down = Date::of($end)->toSql();
			}
		?>
		<?php if ($allday_event) : ?>

			<tr>
				<th class="date"></th>
				<td width="50%">
					<?php
						// check to see if its a single date all day event
						$d1 = Date::of($publish_up);
						$d2 = Date::of($publish_down)->subtract('24 hours');
						if ($d1 == $d2 || $publish_down == '0000-00-00 00:00:00')
						{
							echo $d1->format('l, F d, Y', true);
						}
						else
						{
							echo $d1->format('l, F d, Y', true) . ' - ' . $d2->format('l, F d, Y', true);
						}
					?>
				</td>
				<th class="time"></th>
				<td>
					<?php echo Lang::txt('All Day Event'); ?>
				</td>
			</tr>
		<?php elseif ($publish_down != '0000-00-00 00:00:00') : ?>
			<tr>
				<th class="date"></th>
				<td colspan="3">
					<?php echo Components\Events\Models\EventDate::of($publish_up)->toTimezone($this->event->get('time_zone'), 'l, F d, Y @ h:i a T', true); ?>
					&mdash;
					<?php echo Components\Events\Models\EventDate::of($publish_down)->toTimezone($this->event->get('time_zone'), 'l, F d, Y @ h:i a T', true); ?>
				</td>
			</tr>
		<?php else : ?>
			<tr>
				<th class="date"></th>
				<td width="50%">
					<?php echo Date::of($publish_up, $this->event->get('time_zone'))->format('l, F d, Y', true); ?>
				</td>
				<th class="time"></th>
				<td>
					<?php echo Date::of($publish_up, $this->event->get('time_zone'))->format('g:i a T', true); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('repeating_rule') != '') : ?>
			<tr>
				<th class="repeatig"></th>
				<td colspan="3"><?php echo $this->event->humanReadableRepeatingRule(); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('adresse_info') != '') : ?>
			<tr>
				<th class="location"></th>
				<td colspan="3"><?php echo $this->event->get('adresse_info'); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('contact_info') != '') : ?>
			<tr>
				<th class="author"></th>
				<td colspan="3"><?php echo plgGroupsCalendarHelper::autoLinkText($this->event->get('contact_info')); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('extra_info') != '') : ?>
			<tr>
				<th class="url"></th>
				<td colspan="3">
					<a href="<?php echo $this->event->get('extra_info'); ?>" rel="external">
						<?php echo $this->event->get('extra_info'); ?>
					</a>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($this->event->get('content') != '') : ?>
			<tr>
				<th class="details"></th>
				<td colspan="3"><?php echo plgGroupsCalendarHelper::autoLinkText(nl2br($this->event->get('content'))); ?></td>
			</tr>
		<?php endif; ?>

		<tr>
			<td colspan="4"></td>
		</tr>
		<tr>
			<th class="download"></th>
			<td colspan="4">
				<a class="btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=export&event_id='.$this->event->get('id')); ?>"><?php echo Lang::txt('Export to My Calendar (ics)'); ?></a>
			</td>
		</tr>
	</tbody>
</table>
