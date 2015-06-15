<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
	 ->css('jquery.webui-popover.min.css')
     ->js()
     ->js('jquery.webui-popover.min');

$event = $this->row;

?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php
	$serverTZ = date_default_timezone_get(); //get the default timezone

	// convert UTC to local time zone
	date_default_timezone_set('UTC'); // set up DateTime to use UTC
	$start = new DateTime($event->publish_up); // make the DateTime object
	$end = new DateTime($event->publish_down); // make the DateTime object
	$registerby = new DateTime($event->registerby); //make the DateTime object

	$local = new DateTimeZone($serverTZ); // create the local timezone
	$start->setTimezone($local); // convert the time to the local timezone
	$end->setTimezone($local);
	$registerby->setTimezone($local);
?>

<header id="content-header">
	<h2>Calendar</h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev btn" href="<?php echo Route::url('index.php?option=com_calendar'); ?>">Back</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

	<section class="main section">
		<div class="subject">
			<div class="container data-entry">
						<?php
							echo '<h2 class="eventTitle">' . $event->title . '</h2>';

							// add editable capability
							if ($event->created_by == User::get('id'))
							{
								echo '<a class="editLink" href="#">Edit</a>';
							}
						?>
			</div><!-- / .container -->

			<div class="container" style="padding: 10px 20px 10px 20px;">
				<?php if ($event): ?>
					<!-- Description -->
					<p><b>Description: </b></p>
					<p><?php echo $event->content; ?></p>

					<!-- Category -->
					<p><b>Category: </b><?php echo $event->category; ?></p>

					<!-- When -->
					<p><b>When: </b>
						<?php
							// if user_timezone is not set
								// use default timezone
								echo date_format($start, 'F d, Y g:ia T');
								echo ' - ';
								echo date_format($end, 'F d, Y g:ia T');
							//else
								// use user's timezone
						?>
						</p>

					<!-- Where -->
					<p>
						<b>Where: </b>
						<a href="https://google.com/maps/search/<?php echo $event->adresse_info; ?>">
							<?php echo $event->adresse_info; ?>
						</a>
					</p>

					<!-- Register By -->
					<?php if ($event->registerby == 0): ?>
						<p>
							<b>Registration Deadline: </b>
							<?php echo date_format($registerby, 'F d, Y g:ia T'); ?>
						</p>
					<?php endif;?>

					<p>
						<b>Website: </b>
						<a href="<?php echo '. htmlentities($this->row->extra_info) .' ?>"><?php echo htmlentities($this->row->extra_info); ?></a>
					</p>

					<!-- Tags -->
					<p>
						<b>Tags: </b>
						<?php
							if ($event->tags)
							{
								echo $event->tags;
							}
						?>
					</p>

				<?php endif;?>
				<?php if ((isset($error))): ?>
					<div class="container-block">
							<div class="clear"></div>
							<p class="warning">Some warning here</p>
					</div>
				<?php endif; ?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3>Upcoming Events</h3>
				<p>Somethign</p>
			</div><!-- / .container -->
			<div class="container">
				<h3>Options</h3>
				<p>So the timeliest events</p>
			</div>
		</aside><!-- / .aside -->
	</section><!-- / .main section -->
</form>

