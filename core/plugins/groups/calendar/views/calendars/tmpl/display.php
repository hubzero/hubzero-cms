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
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-prev btn back" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$this->year.'&month='.$this->month); ?>">
			<?php echo Lang::txt('Back to Events Calendar'); ?>
		</a>
		<a class="icon-add btn add" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=addcalendar'); ?>">
			<?php echo Lang::txt('Add Calendar'); ?>
		</a>

	</li>
</ul>
<!--
<div class="event-title-bar">
	<span class="event-title">
		<?php echo Lang::txt('Group Calendars'); ?>
	</span>
</div>
-->
<table class="group-calendars">
	<thead>
		<tr>
			<th><?php echo Lang::txt('Name'); ?></th>
			<th><?php echo Lang::txt('Color'); ?></th>
			<th><?php echo Lang::txt('Publish Events?'); ?></th>
			<th><?php echo Lang::txt('Actions'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->calendars) > 0) : ?>
			<?php foreach ($this->calendars as $calendar) : ?>
				<tr>
					<td><?php echo $calendar->get('title'); ?></td>
					<td>
						<?php
						$colors = array('red','orange','yellow','green','blue','purple','brown');
						if (!in_array($calendar->get('color'), $colors))
						{
							$calendar->set('color', '');
						}
						?>
						<?php if ($calendar->get('color')): ?>
							<img src="<?php echo Request::base(true); ?>/core/plugins/groups/calendar/assets/img/swatch-<?php echo $calendar->get('color'); ?>.png" alt="<?php echo $calendar->get('color'); ?>" />
						<?php else: ?>
							<img src="<?php echo Request::base(true); ?>/core/plugins/groups/calendar/assets/img/swatch-gray.png" alt="gray" />
						<?php endif; ?>
					</td>
					<td>
						<?php
							if ($calendar->get('published') == 1)
							{
								echo '<span class="yes">Yes</span>';
							}
							else
							{
								echo '<span class="no">No</span>';
							}
						?>
					</td>
					<td>
						<a class="edit" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=editcalendar&calendar_id=' . $calendar->get('id')); ?>">
							Edit
						</a> &nbsp;|
						<a class="delete" href="javascript:void(0);">
							Delete
						</a>
						<?php if ($calendar->get('url')) : ?>
							 &nbsp;|
							<a class="refresh" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=refreshcalendar&calendar_id=' . $calendar->get('id')); ?>">
								Refresh
							</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr class="delete-confirm">
					<td colspan="4">
						<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=deletecalendar&calendar_id=' . $calendar->get('id')); ?>" method="post">
							<h3>Delete Calendar</h3>
							<p>What do you want to do with the events associated with this calendar?</p>
							<select name="events">
								<option value="keep">Delete Calendar &amp; Set Events as Uncategorized</option>
								<option value="delete">Delete Calendar &amp; Delete Events</option>
							</select>
							<input class="btn btn-danger" type="submit" value="Delete" />
							<input class="btn btn-secondary delete-cancel" type="reset" value="Cancel" />
						</form>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<?php if ($calendar->get('url')) : ?>
							<span class="calendar-url">
								<span>Calendar URL:</span>
								<?php echo $calendar->get('url'); ?>
							</span>
							<br />
							<span class="calendar-url">
								<span>Last Fetched:</span>
								<?php
									if ($calendar->get('last_fetched') == '' || $calendar->get('last_fetched') == '0000-00-00 00:00:00')
									{
										echo 'Never';
									}
									else
									{
										echo Date::of($calendar->get('last_fetched'))->toLocal('m/d/Y @ g:ia');
									}
								?>
							</span>
						<?php endif; ?>
					</td>
				</tr>

			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="4">Currently there are no calendars for this group.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>