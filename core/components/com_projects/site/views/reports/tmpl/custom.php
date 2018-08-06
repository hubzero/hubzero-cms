<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('reports')
	->css('calendar')
	->js('reports');

// Incoming
$data   = Request::getArray('data', array(), 'post');
$from   = Request::getString('fromdate', Date::of('-1 month')->toLocal('Y-m'));
$to     = Request::getString('todate', Date::of('now')->toLocal('Y-m'));
$filter = Request::getString('searchterm', '');

?>
<header id="content-header" class="reports">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section custom-reports" id="custom-reports">
	<div class="status-msg">
	<?php
		// Display error or success message
		if ($this->getError()) {
			echo ('<p class="witherror">' . $this->getError().'</p>');
		}
		else if ($this->msg) {
			echo ('<p>' . $this->msg . '</p>');
		} ?>
	</div>
	<div class="report-block">
		<form id="reportForm" method="post" action="index.php">
			<fieldset>
				<input type="hidden"  name="option" value="com_projects" />
				<input type="hidden"  name="controller" value="reports" />
				<input type="hidden"  name="task" value="generate" />
				<input type="hidden"  name="no_html" value="1" />
			</fieldset>
			<div class="report-content">
				<div class="groupblock">
					<h6><?php echo Lang::txt('Download publication data:'); ?></h6>
					<label>
						<?php $ph = Date::of('-1 month')->toLocal('Y-m'); ?>
						<?php echo Lang::txt('From'); ?>: <input type="text" value="<?php echo $from; ?>" id="from-date" name="fromdate" placeholder="<?php echo $ph; ?>" maxlength="7" />
					</label>
					<label>
						<?php $ph = Date::of('now')->toLocal('Y-m'); ?>
						<?php echo Lang::txt('To'); ?>: <input type="text" value="<?php echo $to; ?>" id="to-date"  name="todate" placeholder="<?php echo $ph; ?>" maxlength="7" />
					</label>
				</div>
				<div class="groupblock">
					<div class="block">
						<label><?php echo Lang::txt('Filter by tag'); ?>:
						<?php

						$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'searchterm', 'searchterm','', $filter)) );

						if (count($tf) > 0) {
							echo $tf[0];
						} else {
							echo '<textarea name="searchterm" id="searchterm" rows="6" cols="35">'. $this->tags .'</textarea>'."\n";
						}
						?>
						</label>
					</div>
				</div>
				<h6><?php echo Lang::txt('Include the following information:'); ?></h6>
				<div class="groupblock element-choice grid">
					<div class="col span4">
						<label class="block">
							<input type="checkbox" name="data[]" value="id" checked="checked" /> <?php echo Lang::txt('Publication ID'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="title" checked="checked" /> <?php echo Lang::txt('Publication title'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="author" checked="checked" /> <?php echo Lang::txt('First author'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="version" checked="checked" /> <?php echo Lang::txt('Version label'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="doi" checked="checked" /> <?php echo Lang::txt('DOI url'); ?>
						</label>
					</div>
					<div class="col span4">
						<label class="block">
							<input type="checkbox" name="data[]" value="downloads" checked="checked" /> <?php echo Lang::txt('Number of downloads'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="views" checked="checked" /> <?php echo Lang::txt('Number of page views'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="citations" checked="checked" /> <?php echo Lang::txt('Number of citations'); ?>
						</label>
					</div>
					<div class="clear"></div>
				</div>
				<p class="submitarea">
					<span class="button-wrapper icon-download">
						<input type="submit" class="btn active btn-primary icon-download" value="<?php echo Lang::txt('Download report (CSV)'); ?>"  />
					</span>
				</p>
			</div>
		</form>
	</div>
</section>