<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		if ($this->getError())
		{
			echo '<p class="witherror">' . $this->getError() . '</p>';
		}
		else if ($this->msg)
		{
			echo '<p>' . $this->msg . '</p>';
		}
		?>
	</div>
	<div class="report-block">
		<form id="reportForm" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
			<fieldset>
				<input type="hidden"  name="option" value="com_projects" />
				<input type="hidden"  name="controller" value="reports" />
				<input type="hidden"  name="task" value="generate" />
				<input type="hidden"  name="no_html" value="1" />
			</fieldset>
			<div class="report-content">
				<div class="groupblock">
					<h6><?php echo Lang::txt('Download publication data:'); ?></h6>
					<label for="from-date">
						<?php $ph = Date::of('-1 month')->toLocal('Y-m'); ?>
						<?php echo Lang::txt('From'); ?>: <input type="text" value="<?php echo $this->escape($from); ?>" id="from-date" name="fromdate" placeholder="<?php echo $this->escape($ph); ?>" maxlength="7" />
					</label>
					<label for="to-date">
						<?php $ph = Date::of('now')->toLocal('Y-m'); ?>
						<?php echo Lang::txt('To'); ?>: <input type="text" value="<?php echo $this->escape($to); ?>" id="to-date" name="todate" placeholder="<?php echo $this->escape($ph); ?>" maxlength="7" />
					</label>
				</div>
				<div class="groupblock">
					<div class="block">
						<label for="searchterm">
							<?php echo Lang::txt('Filter by tag'); ?>:
							<?php

							$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'searchterm', 'searchterm', '', $filter)));

							if (count($tf) > 0) {
								echo $tf[0];
							} else {
								echo '<textarea name="searchterm" id="searchterm" rows="6" cols="35">' . $this->escape($this->tags) . '</textarea>'."\n";
							}
							?>
						</label>
					</div>
				</div>
				<h6><?php echo Lang::txt('Include the following information:'); ?></h6>
				<div class="groupblock element-choice grid">
					<div class="col span4">
						<label for="choice-id" class="block">
							<input type="checkbox" name="data[]" value="id" id="choice-id" checked="checked" /> <?php echo Lang::txt('Publication ID'); ?>
						</label>
						<label for="choice-title" class="block">
							<input type="checkbox" name="data[]" value="title" id="choice-title" checked="checked" /> <?php echo Lang::txt('Publication title'); ?>
						</label>
						<label for="choice-author" class="block">
							<input type="checkbox" name="data[]" value="author" id="choice-author" checked="checked" /> <?php echo Lang::txt('First author'); ?>
						</label>
						<label for="choice-version" class="block">
							<input type="checkbox" name="data[]" value="version" id="choice-version" checked="checked" /> <?php echo Lang::txt('Version label'); ?>
						</label>
						<label for="choice-doi" class="block">
							<input type="checkbox" name="data[]" value="doi" id="choice-doi" checked="checked" /> <?php echo Lang::txt('DOI url'); ?>
						</label>
					</div>
					<div class="col span4">
						<label for="choice-downloads" class="block">
							<input type="checkbox" name="data[]" value="downloads" id="choice-downloads" checked="checked" /> <?php echo Lang::txt('Number of downloads'); ?>
						</label>
						<label for="choice-views" class="block">
							<input type="checkbox" name="data[]" value="views" id="choice-views" checked="checked" /> <?php echo Lang::txt('Number of page views'); ?>
						</label>
						<label for="choice-citations" class="block">
							<input type="checkbox" name="data[]" value="citations" id="choice-citations" checked="checked" /> <?php echo Lang::txt('Number of citations'); ?>
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