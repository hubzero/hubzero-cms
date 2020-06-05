<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('reports')
	->css('jquery.ui', 'system')
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
	<?php if ($this->getError() || $this->msg): ?>
		<div class="status-msg">
			<?php
			// Display error or success message
			if ($this->getError()):
				echo '<p class="error">' . $this->getError() . '</p>';
			elseif ($this->msg):
				echo '<p>' . $this->msg . '</p>';
			endif;
			?>
		</div>
	<?php endif; ?>

	<div class="report-block">
		<form id="hubForm" class="full" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
			<fieldset>
				<legend><?php echo Lang::txt('Download publication data:'); ?></legend>

				<input type="hidden"  name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden"  name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden"  name="task" value="generate" />
				<input type="hidden"  name="no_html" value="1" />

				<div class="grid">
					<div class="col span3 form-group">
						<label for="from-date">
							<?php echo Lang::txt('From'); ?>:
							<input type="text" class="form-input datepicker" value="<?php echo $this->escape($from); ?>" id="from-date" name="fromdate" placeholder="<?php echo $this->escape(Date::of('-1 month')->toLocal('Y-m')); ?>" maxlength="7" />
						</label>
					</div>
					<div class="col span3 form-group">
						<label for="to-date">
							<?php echo Lang::txt('To'); ?>:
							<input type="text" class="form-input datepicker" value="<?php echo $this->escape($to); ?>" id="to-date" name="todate" placeholder="<?php echo $this->escape(Date::of('now')->toLocal('Y-m')); ?>" maxlength="7" />
						</label>
					</div>
					<div class="col span6 omega form-group">
						<label for="searchterm">
							<?php echo Lang::txt('Filter by tag'); ?>:
							<?php
							$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'searchterm', 'searchterm', '', $filter)));
							$tf = implode('', $tf);

							if (empty($tf)):
								$tf = '<textarea name="searchterm" id="searchterm" class="form-input" rows="6" cols="35">' . $this->escape($this->tags) . '</textarea>'."\n";
							endif;

							echo $tf;
							?>
						</label>
					</div>
				</div>
				<fieldset class="element-choice">
					<legend><?php echo Lang::txt('Include the following information:'); ?></legend>

					<div class="grid">
						<div class="col span6">
							<div class="form-group form-check">
								<label for="choice-id" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="id" id="choice-id" checked="checked" />
									<?php echo Lang::txt('Publication ID'); ?>
								</label>
							</div>
							<div class="form-group form-check">
								<label for="choice-title" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="title" id="choice-title" checked="checked" />
									<?php echo Lang::txt('Publication title'); ?>
								</label>
							</div>
							<div class="form-group form-check">
								<label for="choice-author" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="author" id="choice-author" checked="checked" />
									<?php echo Lang::txt('First author'); ?>
								</label>
							</div>
							<div class="form-group form-check">
								<label for="choice-version" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="version" id="choice-version" checked="checked" />
									<?php echo Lang::txt('Version label'); ?>
								</label>
							</div>
							<div class="form-group form-check">
								<label for="choice-doi" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="doi" id="choice-doi" checked="checked" />
									<?php echo Lang::txt('DOI url'); ?>
								</label>
							</div>
						</div>
						<div class="col span6 omega">
							<div class="form-group form-check">
								<label for="choice-downloads" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="downloads" id="choice-downloads" checked="checked" />
									<?php echo Lang::txt('Number of downloads'); ?>
								</label>
							</div>
							<div class="form-group form-check">
								<label for="choice-views" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="views" id="choice-views" checked="checked" />
									<?php echo Lang::txt('Number of page views'); ?>
								</label>
							</div>
							<div class="form-group form-check">
								<label for="choice-citations" class="form-check-label">
									<input type="checkbox" class="form-check-input" name="data[]" value="citations" id="choice-citations" checked="checked" />
									<?php echo Lang::txt('Number of citations'); ?>
								</label>
							</div>
						</div>
					</div>
				</fieldset>
			</fieldset>

			<p class="submit">
				<span class="button-wrapper icon-download-alt">
					<input type="submit" class="btn btn-primary icon-download-alt" value="<?php echo Lang::txt('Download report (CSV)'); ?>"  />
				</span>
			</p>
		</form>
	</div>
</section>