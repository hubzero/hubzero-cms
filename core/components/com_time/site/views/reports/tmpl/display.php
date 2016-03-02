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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('reports')
     ->js('reports');

$base    = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
$options = array();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_reports">
		<?php if (count($this->reports) > 0) : ?>
			<div class="report-select-type">
				<form action="<?php echo Route::url($base); ?>">
					<label for="report-type"><?php echo Lang::txt('COM_TIME_REPORTS_SELECT_REPORT_TYPE'); ?>: </label>
					<?php foreach ($this->reports as $report) : ?>
						<?php $element = 'plg_time_' . $report->name; ?>
						<?php Lang::load(strtolower($element), PATH_CORE . DS . 'plugins' . DS . 'time' . DS . $report->name); ?>
						<?php $options[] = Html::select('option', $report->name, Lang::txt(strtoupper($element . '_display_name')), 'value', 'text'); ?>
					<?php endforeach; ?>
					<?php echo Html::select('genericlist', $options, 'report_type', null, 'value', 'text', $this->report_type); ?>
					<button class="btn btn-success"><?php echo Lang::txt('COM_TIME_REPORTS_BEGIN'); ?></button>
				</form>
			</div>
			<div class="report-content">
				<?php if (isset($this->content)) : ?>
					<?php echo (isset($this->content)) ? $this->content : ''; ?>
				<?php else : ?>
					<div class="make-selection">
						<?php echo Lang::txt('COM_TIME_REPORTS_PLEASE_SELECT_REPORT_TYPE'); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="no_reports">
				<?php echo Lang::txt('COM_TIME_REPORTS_NO_REPORT_TYPES'); ?>
			</div>
		<?php endif; ?>
	</section>
</div>