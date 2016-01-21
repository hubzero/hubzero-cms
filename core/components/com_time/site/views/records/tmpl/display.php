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

use Components\Time\Helpers\Filters;
use Hubzero\Utility\String;

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('records')
     ->css('jquery.ui.css', 'system')
     ->js('records');

HTML::behavior('core');

// Set some ordering variables
$sortcol = $this->records->orderBy;
$dir     = $this->records->orderDir;
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-add btn" href="<?php echo Route::url($this->base . '&task=new'); ?>">
					<?php echo Lang::txt('COM_TIME_RECORDS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_records">
		<div class="container">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
				<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<form method="get" action="<?php echo Route::url($this->base); ?>">
				<div class="search-box">
					<a href="<?php echo Route::url($this->base . '&search='); ?>">
						<button type="button" class="clear-button btn btn-warning"><?php echo Lang::txt('COM_TIME_RECORDS_CLEAR'); ?></button>
					</a>
					<input class="search-submit btn btn-success" type="submit" value="<?php echo Lang::txt('COM_TIME_RECORDS_SEARCH'); ?>" />
					<fieldset class="search-text">
						<input id="search-input" type="text" name="search" placeholder="<?php echo Lang::txt('COM_TIME_RECORDS_SEARCH_EXPLANATION'); ?>" value="<?php
								echo (is_array($this->filters['search']) && !empty($this->filters['search'][0])) ? implode(" ", $this->filters['search']) : ''; ?>" />
					</fieldset>
				</div><!-- / .search-box -->
			</form>
			<form method="get" action="<?php echo Route::url($this->base); ?>">
				<div id="add-filters">
					<p>Filter results:
						<select name="q[column]" id="filter-column">
							<?php foreach (Filters::getColumnNames('time_records', array("id", "description", "end")) as $c) : ?>
								<option value="<?php echo $c['raw']; ?>"><?php echo $c['human']; ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo Filters::buildSelectOperators(); ?>
						<select name="q[value]" id="filter-value">
						</select>
						<input id="filter-submit" class="btn btn-success" type="submit" value="<?php echo Lang::txt('+ Add filter'); ?>" />
						<input type="hidden" value="time_records" id="filter-table" />
					</p>
				</div><!-- / .filters -->
			</form>
			<?php if (!empty($this->filters['q']) || (is_array($this->filters['search']) && !empty($this->filters['search'][0]))) : ?>
				<div id="applied-filters">
					<p>Applied filters:</p>
					<ul class="filters-list">
						<?php if (!empty($this->filters['q'])) : ?>
							<?php foreach ($this->filters['q'] as $q) : ?>
								<li>
									<a href="<?php echo Route::url($this->base . '&q[column]=' . $q['column'] .
										'&q[operator]=' . $q['operator'] . '&q[value]=' . $q['value'] . '&q[delete]'); ?>"
										class="filters-x">x
									</a>
									<i><?php echo $q['human_column'] . ' ' . $q['human_operator']; ?></i>: <?php echo $q['human_value']; ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (is_array($this->filters['search']) && !empty($this->filters['search'][0])) : ?>
							<li>
								<a href="<?php echo Route::url($this->base . '&search='); ?>" class="filters-x">x</a>
								<i>Search</i>: <?php echo implode(" ", $this->filters['search']); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="entries table">
				<div class="caption"><?php echo Lang::txt('COM_TIME_RECORDS_CAPTION'); ?></div>
				<div class="thead">
					<div class="tr">
						<div class="th">
							<a <?php if ($sortcol == 'id') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=id&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_RECORDS_ID'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'user.name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=user.name&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_RECORDS_USER'); ?>
							</a>
						</div>
						<div class="th col-time">
							<a <?php if ($sortcol == 'time') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=time&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_RECORDS_TIME'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'date') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=date&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_RECORDS_DATE'); ?>
							</a>
						</div>
						<div class="th">
							<a <?php if ($sortcol == 'task.name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo Route::url($this->base . '&orderby=task.name&orderdir=' . $newdir); ?>">
									<?php echo Lang::txt('COM_TIME_RECORDS_TASK'); ?>
							</a>
						</div>
						<div class="th"><?php echo Lang::txt('COM_TIME_RECORDS_DESCRIPTION'); ?></div>
					</div>
				</div>
				<div class="tbody">
					<?php foreach ($this->records as $record) : ?>
						<div class="tr">
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_RECORDS_ID'); ?>:</div>
								<div class="small-content">
									<a href="<?php echo Route::url($this->base . '&task=readonly&id=' . $record->id); ?>">
										<?php echo $record->id; ?>
									</a>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_RECORDS_USER'); ?>:</div>
								<div class="small-content">
									<?php echo $record->user->name; ?>
								</div>
							</div>
							<div class="td col-time">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_RECORDS_TIME'); ?>:</div>
								<div class="small-content">
									<?php echo $record->time; ?>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_RECORDS_DATE'); ?>:</div>
								<div class="small-content">
									<?php echo Date::of($record->date)->toLocal('m/d/y'); ?>
								</div>
							</div>
							<div class="td">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_RECORDS_TASK'); ?>:</div>
								<div class="small-content">
									<?php echo String::highlight(
										$record->task->name,
										$this->filters['search'],
										array('html' => true)
									); ?>
									[<?php echo $record->task->hub->name; ?>]
								</div>
							</div>
							<div class="td last" title="<?php echo $record->description; ?>">
								<div class="small-label"><?php echo Lang::txt('COM_TIME_RECORDS_DESCRIPTION'); ?>:</div>
								<div class="small-content">
									<?php echo String::highlight(
										String::truncate(
											$record->description,
											50),
										$this->filters['search'],
										array('html' => true)
									); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if (!$this->records->count()) : ?>
						<div class="tr">
							<div colspan="7" class="td no_records"><?php echo Lang::txt('COM_TIME_RECORDS_NONE_TO_DISPLAY'); ?></div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<form action="<?php echo Route::url($this->base); ?>">
				<?php echo $this->records->pagination; ?>
			</form>
		</div>
	</section>
</div>