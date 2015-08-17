<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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

$this->js('https://maps.googleapis.com/maps/api/js?v=3.exp');
$this->js("geosearch.jquery.js");
$this->js('oms.min.js');
$this->css('geosearch.css');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_GEOSEARCH_TITLE'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="get" id="frm_search" class="section-inner">
		<div class="subject">
			<?php if ($this->getError()): ?>
				<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
			<?php endif; ?>

			<div id="map_container">
				<div id="map_canvas"></div>
			</div> <!-- / #map_container -->
		</div> <!-- / .subject -->

		<aside class="aside geosearch">
			<div class="container">
				<h3><?php echo Lang::txt('COM_GEOSEARCH_FILTER'); ?></h3>
				<fieldset>
					<legend><?php echo Lang::txt('COM_GEOSEARCH_LIM_RES'); ?></legend>

					<div class="key">
						<label for="resck-member">
							<img src="<?php echo $this->img('icn_member2.png'); ?>" alt="" />
							<input type="checkbox" name="resource[]" class="resck option" id="resck-member" value="member" checked="checked" /> <?php echo Lang::txt('COM_GEOSEARCH_MEMBERS'); ?>
						</label>
					</div>

					<div class="key">
						<label for="resck-job">
							<img src="<?php echo $this->img('icn_job2.png'); ?>" alt="" />
							<input type="checkbox" name="resource[]" class="resck option" value="job" id="resck-job" checked="checked" /> <?php echo Lang::txt('COM_GEOSEARCH_JOBS'); ?>
						</label>
					</div>

					<div class="key">
						<label for="resck-event">
							<img src="<?php echo $this->img('icn_event2.png'); ?>" alt="" />
							<input type="checkbox" name="resource[]" class="resck option" value="event" id="resck-event" checked="checked" /> <?php echo Lang::txt('COM_GEOSEARCH_EVENTS'); ?>
						</label>
					</div>

					<div class="key">
						<label for="resck-organization">
							<img src="<?php echo $this->img('icn_org2.png'); ?>" alt="" />
							<input type="checkbox" name="resource[]" class="resck option" value="organization" id="resck-organization" checked="checked" /> <?php echo Lang::txt('COM_GEOSEARCH_ORGANIZATIONS'); ?>
						</label>
					</div>

					<div class="clear-right"></div>
				</fieldset>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</form>
</section>
