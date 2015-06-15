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
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<header id="content-header">
	<h2>Calendar</h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add btn" href="#">Add</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

	<section class="main section">
		<div class="subject">
			<div class="container">
				<div class="clearfix"></div>
				<div id="calendar"
					data-base="calendar"
					data-month="4"
					data-year="2015"
					data-event-quickcreate="1">
				</div>
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

