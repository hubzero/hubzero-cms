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

$year  = date("Y", strtotime($this->event->publish_up));
$month = date("m", strtotime($this->event->publish_up));
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-date btn date" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&year='.$year.'&month='.$month); ?>">
			<?php echo Lang::txt('Back to Calendar'); ?>
		</a>
	</li>
</ul>

<div class="event-title-bar">
	<span class="event-title">
		<?php echo $this->event->title; ?>
	</span>
	<?php if ($this->user->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
		<a class="delete" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=delete&event_id='.$this->event->id); ?>">
			Delete
		</a>
		<a class="edit" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=edit&event_id='.$this->event->id); ?>">
			Edit
		</a>
	<?php endif; ?>
</div>

<div class="event-sub-menu">
	<ul>
		<li>
			<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=details&event_id='.$this->event->id); ?>">
				<span><?php echo Lang::txt('Details'); ?></span>
			</a>
		</li>
		<?php if (isset($this->event->registerby) && $this->event->registerby != '' && $this->event->registerby != '0000-00-00 00:00:00') : ?>
			<li>
				<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=register&event_id='.$this->event->id); ?>">
					<span><?php echo Lang::txt('Register'); ?></span>
				</a>
			</li>
		<?php endif; ?>

		<?php if ($this->user->get('id') == $this->event->created_by || $this->authorized == 'manager') : ?>
			<li class="active">
				<a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=calendar&action=registrants&event_id='.$this->event->id); ?>">
					<span><?php echo Lang::txt('Registrants ('.count($this->registrants).')'); ?></span>
				</a>
			</li>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>

<table class="group-registrants">
	<thead>
		<tr>
			<th colspan="4">
				<a href="<?php echo Route::url('index.php?option=' . $this->option.'&cn=' . $this->group->get('cn') . '&active=calendar&action=download&event_id=' . $this->event->id); ?>">Download Registrants (.csv)</a>
			</th>
		</tr>
		<tr>
			<th><?php echo Lang::txt('PLG_GROUPS_CALENDAR_REGISTRANTS_NAME'); ?></th>
			<th><?php echo Lang::txt('PLG_GROUPS_CALENDAR_REGISTRANTS_EMAIL'); ?></th>
			<th><?php echo Lang::txt('PLG_GROUPS_CALENDAR_REGISTRANTS_DATE'); ?></th>
			<th><?php echo Lang::txt('PLG_GROUPS_CALENDAR_REGISTRANTS_UNREGISTER'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($this->registrants) > 0) : ?>
			<?php foreach ($this->registrants as $registrant) : ?>
				<tr>
					<td><?php echo $registrant->last_name . ', ' . $registrant->first_name; ?></td>
					<td><?php echo $registrant->email; ?></td>
					<td><?php echo Date::of($registrant->registered)->toLocal('l, F d, Y @ g:i a'); ?></td>
					<?php if($registrant->email == $this->user->email || $this->authorized == 'manager') { ?>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->cn . '&active=calendar&action=unregister&email=' . $registrant->email  . '&event_id=' . $this->event->id . '&' . Session::getFormToken() . '=1');  ?>"`>Unregister</a></td>
					<?php } else { ?>
					<td></td>
					<?php } ?>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="4">Currently there are no event registrants.</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
