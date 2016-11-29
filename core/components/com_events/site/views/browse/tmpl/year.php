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

$this->css()
	->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->authorized) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last"><a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=add'); ?>"><?php echo Lang::txt('EVENTS_ADD_EVENT'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header><!-- / #content-header -->

<nav>
	<ul class="sub-menu">
		<li<?php if ($this->task == 'year') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_YEAR'); ?></span></a></li>
		<li<?php if ($this->task == 'month') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_MONTH'); ?></span></a></li>
		<li<?php if ($this->task == 'week') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day.'&task=week'); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_WEEK'); ?></span></a></li>
		<li<?php if ($this->task == 'day') { echo ' class="active"'; } ?>><a href="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day); ?>"><span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_DAY'); ?></span></a></li>
	</ul>
</nav>

<section class="main section">
	<div class="subject">
	<p id="toggle-prior"><strong><a id="toggle-prior-anchor" href="#" onClick="return false;">Show Past Events</a></strong></p>
	<?php if (count($this->rows) > 0) { ?>
		<ul class="events">
		<?php
			foreach ($this->rows as $row)
			{
				$this->view('item')
				     ->set('option', $this->option)
				     ->set('task', $this->task)
				     ->set('row', $row)
				     ->set('fields', $this->fields)
				     ->set('categories', $this->categories)
				     ->set('showdate', 1)
				     ->display();
			}
		?>
		</ul>
	<?php } else { ?>
		<p class="warning"><?php echo Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR').' <strong>'.$this->year.'</strong>'; ?></p>
	<?php } ?>
	</div><!-- / .subject -->
	<div class="aside">
		<form action="<?php echo Route::url('index.php?option='.$this->option.'&year='.$this->year); ?>" method="get" id="event-categories">
			<fieldset>
				<select name="category">
					<option value=""><?php echo Lang::txt('EVENTS_ALL_CATEGORIES'); ?></option>
				<?php
				if ($this->categories)
				{
					foreach ($this->categories as $id=>$title)
					{
					?>
						<option value="<?php echo $id; ?>"<?php if ($this->category == $id) { echo ' selected="selected"'; } ?>><?php echo stripslashes($title); ?></option>
					<?php
					}
				}
				?>
				</select>
				<input type="submit" value="<?php echo Lang::txt('EVENTS_GO'); ?>" />
			</fieldset>
		</form>

		<div class="calendarwrap">
			<p class="datenav">
				<?php
				$this_date = new \Components\Events\Helpers\EventsDate();
				$this_date->setDate( $this->year, 0, 0 );

				$prev_year = clone($this_date);
				$prev_year->addMonths( -12 );
				$next_year = clone($this_date);
				$next_year->addMonths( +12 );
				$database = App::get('db');
				$sql = "SELECT MIN(publish_up) min, MAX(publish_down) max FROM `#__events` as e
								WHERE `scope`='event'
								AND `state`=1
								AND `approved`=1";
				$database->setQuery($sql);
				$rows = $database->loadObjectList();
				$first_event_time = new DateTime($rows[0]->min);
				$last_event_time = new DateTime($rows[0]->max);
				$this_datetime = new DateTime($this->year . '-01-01');
				//get a DateTime for the first day of the year and check if there's an event earlier
				if ($this_datetime > $first_event_time) {
					$prev = Route::url('index.php?option='.$this->option.'&'.$prev_year->toDateURL($this->task));
					$prev_text = Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
				} else {
					$prev = "javascript:void(0);";
					$prev_text = Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') . ' ' . Lang::txt('EVENTS_CAL_LANG_PREVIOUSYEAR');
				}
				//get a DateTime for the first day of the next year and see if there's an event after
				$this_datetime->add(new DateInterval("P1Y"));
				if ($this_datetime <= $last_event_time) {
					$next = Route::url('index.php?option='.$this->option.'&'.$next_year->toDateURL($this->task));
					$next_text = Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
				} else {
					$next = "javascript:void(0);";
					$next_text = Lang::txt('EVENTS_CAL_LANG_NO_EVENTFOR') . ' ' . Lang::txt('EVENTS_CAL_LANG_NEXTYEAR');
				}

				?>
				<a class="prv" href="<?php echo $prev;?>" title="<?php echo $prev_text; ?>">&lsaquo;</a>
				<a class="nxt" href="<?php echo $next;?>" title="<?php echo $next_text; ?>">&rsaquo;</a>
				<?php echo $this->year; ?>
			</p>
		</div><!-- / .calendarwrap -->
	</div><!-- / .aside -->
</section><!-- / .main section -->
