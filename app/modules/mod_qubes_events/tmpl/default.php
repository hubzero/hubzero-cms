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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="mod_qubes_events">

	<?php foreach ($this->events as $event) {

		//print_r($event); die;
		?>

		<div class="item cf">
			<div class="meta <?php echo $event->type; ?>">
				<div class="in cf">
					<div class="type"><?php echo $event->type_name; ?></div>
					<div class="status">
						<?php
						if (!empty($event->status) && $event->status == 'open')
						{
							if (isset($event->applyUrl)) {
								echo '<a href="' . $event->applyUrl . '">Apply now</a>';
							}
							else {
								echo '<a href="/events/details/' . $event->id . '">Apply now</a>';
							}
						}
						elseif (!empty($event->status) && $event->status == 'now')
						{
							echo 'Happening now';
						}
						?>
					</div>
				</div>
			</div>

			<div class="cf">
				<div class="thumb">
					<?php
					if (!empty($event->group))
					{
						//echo '<a href="/events/details/' . $event->id . '">';
						echo '<img src="' . $event->group->logo . '"/>';
					}
					else {
						echo '&nbsp;';
					}
					?>
				</div>
				<div class="content">

					<h3>
						<?php
						if (!empty($event->group))
						{
							echo '<a href="/groups/' . $event->group->cn . '">';
						}
						else {
							echo '<a href="/events/details/' . $event->id . '">';
						}
						?>

						<?php echo $event->title; ?>

						<?php
						if (1)
						{
							echo '</a>';
						}
						?>
					</h3>
				</div>
			</div>

			<div class="main">

				<?php
				echo '<p class="when">';
				if (date('d F Y', strtotime($event->publish_up)) == date('d F Y', strtotime($event->publish_down)))
				{
					// display time
					echo Date::of($event->publish_up, date('T', strtotime($event->publish_up)))->format('F d');

					if (date('Y', strtotime($event->publish_up)) != date('Y'))
					{
						echo ', ';
						echo Date::of($event->publish_down, date('T', strtotime($event->publish_down)))->format('Y');
					}

					echo ', ';

					echo Date::of($event->publish_up, date('T', strtotime($event->publish_up)))->format('g:i a');
					echo '&ndash;';
					echo Date::of($event->publish_down, date('T', strtotime($event->publish_down)))->format('g:i a');
					echo ' ';
					echo Date::of($event->publish_up, date('T', strtotime($event->publish_up)))->format('T');
				}
				else
				{
					echo Date::of($event->publish_up, date('T', strtotime($event->publish_up)))->format('F d');
					if (date('Y', strtotime($event->publish_up)) != date('Y', strtotime($event->publish_down)))
					{
						echo ', ';
						echo Date::of($event->publish_up, date('T', strtotime($event->publish_up)))->format('Y');
						echo '&ndash;';
						echo Date::of($event->publish_down, date('T', strtotime($event->publish_down)))->format('F d, Y');
					}
					else
					{
						echo '&ndash;';

						if (date('F', strtotime($event->publish_up)) != date('F', strtotime($event->publish_down)))
						{
							echo Date::of($event->publish_down, date('T', strtotime($event->publish_down)))->format('F');
							echo ' ';
						}
						echo Date::of($event->publish_down, date('T', strtotime($event->publish_down)))->format('d');

						if (date('Y', strtotime($event->publish_up)) != date('Y'))
						{
							echo ', ';
							echo Date::of($event->publish_down, date('T', strtotime($event->publish_down)))->format('Y');
						}
					}
				}
				echo '</p>';

				echo $event->ftext;

				//echo Date::of($event->publish_up, date('T', strtotime($event->publish_up)))->format('F, d F, Y g:i a T');
				?>

			</div>
		</div>
	<?php } ?>

</div>