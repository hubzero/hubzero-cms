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

?>
<style>
#map_canvas {
	width: 95%;
	min-height: 500px;
	margin: 0 0 2em 0;
	padding: 2em 2em 2em 2em;
}
.event-popup h1 {
	font-size: 14pt;
	margin-top: 5px;
	margin-bottom: 0;
}
.event-popup p.date {
	font-size: 10pt;
	margin-top:0;
	margin-bottom:0;
	font-style: normal;
}
.event-popup p.location {
	font-size: 10pt;
	margin-top: 0;
	font-style: normal;
}
</style>

<div id="content-header" class="full">
	<h2><?php echo Lang::txt('COM_GEOSEARCH_TITLE'); ?></h2>
</div>

<!--- .main .section -->
<div class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="get" id="frm_search">
	<!-- page errors -->
	<?php if ($this->getError()): ?>
		<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
	<?php endif; ?>

<div class="aside geosearch">
	<div class="container">
		<h3><?php echo Lang::txt('COM_GEOSEARCH_FILTER'); ?></h3>
	<fieldset>
		<legend><?php echo Lang::txt('COM_GEOSEARCH_LIM_RES'); ?></legend>

		<div class="key">
			<img src="<?php echo $this->img('icn_member2.png'); ?>">
			<input type="checkbox" name="resource[]" class="resck" value="member "<?php if (in_array("members",$this->resources)) { echo 'checked="checked"'; }?> /> Members
		</div>

		<div class="key">
			<img src="<?php echo $this->img('icn_job2.png'); ?>" />
			<input type="checkbox" name="resource[]" class="resck" value="job" <?php if (in_array("jobs",$this->resources)) { echo 'checked="checked"'; }?> /> Jobs
		</div>

		<div class="key">
			<img src="<?php echo $this->img('icn_event2.png'); ?>" />
			<input type="checkbox" name="resource[]" class="resck" value="event" <?php if (in_array("events",$this->resources)) { echo 'checked="checked"'; }?> /> Events
		</div>

		<div class="key">
			<img src="<?php echo $this->img('icn_org2.png'); ?>" />
			<input type="checkbox" name="resource[]" class="resck" value="organization" <?php if (in_array("organizations",$this->resources)) { echo 'checked="checked"'; }?> /> Organizations
		</div>

		<div class="clear-right"></div>
	</fieldset>

	<fieldset>
		<legend><?php echo Lang::txt('COM_GEOSEARCH_LIM_TAGS'); ?></legend>
		<?php
			if (isset($this->stags))
			{
				$stags = implode(",",$this->stags);
			}
			else
			{
				$stags = "";
			}

			// load tags plugin
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher = JDispatcher::getInstance();
			$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags','tags','actags','',$stags)) );
			if (count($tf) > 0)
			{
				echo $tf[0];
			}
			else
			{
				echo '<input type="text" name="tags" value="'. $stags .'" />';
			}
		?>
	</fieldset>

	<fieldset>
		<legend><?php echo Lang::txt('COM_GEOSEARCH_LIM_LOC'); ?></legend>
		<label class="fieldset">Within:</label>
		<input type="text" name="distance" id="idist" value="<?php //echo $this->distance; ?>" />
		<select name="dist_units">
			<option value="mi">Miles</option>
			<option value="km" <?php if ($this->unit == 'km') echo 'selected="selected"'; ?>>Kilometers</option>
		</select>
		<label class="fieldset">of:</label>
		<input type="text" name="location" id="iloc" value="<?php echo ($this->location != '' ? $this->location : ''); ?>" <?php echo ($this->location == '' ? 'placeholder="place, address, or zip"' : ''); ?> />
	</fieldset>

	<input type="submit" value="<?php echo Lang::txt('COM_GEOSEARCH_FILTER_BUTTON'); ?>" /> <input type="button" value="Clear" id="clears"/>

	<div class="clear"></div>
	</div><!-- / .container -->
</div><!-- / .aside -->

<div class="subject">
	<div class="container data-entry">
		<input class="entry-search-submit" type="submit" value="Search">
		<fieldset class="entry-search">
			<legend>Search by Keyword</legend>
			<label for="entry-search-field">Enter keyword or phrase</label>
			<input type="text" name="search" id="entry-search-field" value="<?php echo Request::getVar('search', ''); ?>" placeholder="Search by keyword or phrase">
		</fieldset>
	</div> <!-- / .container .data-entry -->

	<div id="map_container">
		<div id="map_canvas"></div>
	</div> <!-- / #map_container -->
</div> <!-- / .subject -->
