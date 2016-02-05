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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
					<?php if (!User::isGuest()): ?>
					<div class="key">
					 <p><a id="reportMarker">Report the current marker as being in an incorrect location.</a></p>
					</div>
					<?php endif; ?>

					<div class="clear-right"></div>
				</fieldset>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</form>
</section>
