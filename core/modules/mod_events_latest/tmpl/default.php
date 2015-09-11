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

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } else { ?>
	<table class="latest_events_tbl">
		<tbody>
		<?php if (count($this->events) > 0) { ?>
			<?php
			$cls = 'even';
			foreach ($this->events as $event)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
			?>
			<tr class="<?php echo $cls; ?>">
				<td class="event-date">
					<span class="month"><?php echo Date::of($event->publish_up)->toLocal('M'); ?></span>
					<span class="day"><?php echo Date::of($event->publish_up)->toLocal('d'); ?></span>
				</td>
				<td class="event-title">
					<a href="<?php echo Route::url('index.php?option=com_events&task=details&id=' . $event->id); ?>"><?php echo $this->escape(html_entity_decode(stripslashes($event->title))); ?></a>
				</td>
			</tr>
			<?php
			}
			?>
		<?php } else { ?>
			<tr class="odd">
				<td class="mod_events_latest_noevents"><?php echo Lang::txt('MOD_EVENTS_LATEST_NONE_FOUND'); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<p class="more">
		<a href="<?php echo Route::url('index.php?option=com_events&year=' . strftime("%Y", time()) . '&month=' . strftime("%m", time())); ?>"><?php echo Lang::txt('MOD_EVENTS_LATEST_MORE'); ?></a>
	</p>
<?php } ?>