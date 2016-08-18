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

// No direct access
defined('_HZEXEC_') or die();

$this->js('jquery.ui', 'system');
$this->css('jquery.ui.css', 'system');

$base = $this->course->offering()->link() . '&active=progress';
?>

<script id="progress-template-main" type="text/x-handlebars-template">
	<div class="grade-policy">
		<div class="grade-policy-inner">
			{{#if gradepolicy.editable}}
				<div class="label-input-pair">
					<label for="exam-weight">Exam Weight:</label>
					<input type="text" name="exam-weight" value="{{gradepolicy.exam_weight}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="quiz-weight">Quiz Weight:</label>
					<input type="text" name="quiz-weight" value="{{gradepolicy.quiz_weight}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="homework-weight">Homework Weight:</label>
					<input type="text" name="homework-weight" value="{{gradepolicy.homework_weight}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="threshold">Passing Threshold:</label>
					<input type="text" name="threshold" value="{{gradepolicy.threshold}}" class="slider" size="4" />
				</div>
				<div class="label-input-pair">
					<label for="description">Policy Description:</label>
					<textarea name="description" cols="50" rows="2">{{gradepolicy.description}}</textarea>
				</div>
				<button type="submit">Submit</button>
				<a class="restore-defaults" href="<?php echo Route::url($base. '&action=restoredefaults') ?>">Restore Defaults</a>
			{{else}}
				<p class="warning">Sorry, you do not have permission to edit the grade policy for this course</p>
			{{/if}}
		</div>
	</div>
	<div class="headers main-headers">
		<div class="cell header-student-name">
			<div class="sorter" data-sort-val="name" data-sort-dir="asc"></div>
			Name
		</div>
		<div class="header-sub">
			<div class="cell header-progress">
				Unit Progress
				<div class="details" title="This reflects what students have viewed, not the actual scores that they may have received."></div>
			</div>
			<div class="cell header-score">
				<div class="sorter" data-sort-val="score" data-sort-dir="asc"></div>
				Current Score
				<div class="details" title="{{gradepolicy.description}}"></div>
			</div>
			<div class="cell header-recognition">
				Awards
			</div>
		</div>
	</div>
	<div class="students"></div>
</script>

<script id="progress-template-row" type="text/x-handlebars-template">
	{{#each members}}
		<div class="student">
			<div class="student-clickable">
				<div class="cell student-name">
					<div class="picture-thumb">
						<img src="<?php /*echo Request::base();*/ ?>{{this.thumb}}" />
					</div>
					<div class="name-value">
						{{this.name}}
					</div>
				</div>
				<div class="student-progress-container">
					<div class="student-progress-timeline">
						<div class="student-progress-timeline-inner length_{{countUnits ../units}}">
							{{#each ../units}}
								<div class="unit">
									<div class="unit-inner">
										<div class="unit-title">{{this.title}}</div>
										{{getFill ../../progress ../id}}
									</div>
								</div>
							{{/each}}
						</div>
					</div>
					<div class="progress-bar-container">
						<div class="progress-bar-container-inner">
							<div class="progress-bar-inner">
								{{getBar ../grades ../passing ../course_id}}
							</div>
						</div>
					</div>
					<div class="student-recognitions{{hasEarned ../recognitions this.id}}">
						<div class="student-recognition-icon student-recognitions-badge"></div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="student-details grades">
				<div class="extended-info">
					<div class="picture">
						<img src="<?php /*echo Request::base();*/ ?>{{this.full}}" />
						<a class="more-details" href="<?php echo Route::url($base.'&id=') ?>{{this.user_id}}">More details</a>
					</div>
					<div class="extended-info-extra">
						<h6>Joined Course</h6>
						<p>{{enrolled}}</p>
						<h6>Last Visit</h6>
						<p>{{lastvisit}}</p>
					</div>
				</div>
				<div class="units">
					<div class="headers">
						<div class="header-units">Unit Scores</div>
					</div>
					{{#each ../units}}
						<div class="unit-entry">
							<div class="unit-overview">
								<div class="unit-title">{{this.title}}</div>
								<div class="unit-score">
									{{getScore "units" ../../grades ../id this.id}}
								</div>
							</div>
						</div>
					{{/each}}
					<div class="unit-entry">
						<div class="unit-overview">
							<div class="unit-title">Course Average</div>
							<div class="unit-score">
								{{getScore "course" ../grades this.id ../course_id}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	{{/each}}
</script>

<script id="gradebook-template-main" type="text/x-handlebars-template">
	<div class="gradebook-container-inner">
		<div class="gradebook-column gradebook-students">
			<div class="cell search-box"><input type="text" placeholder="Search students" /></div>
			{{#each members}}
					<div class="cell cell-title cell-row{{@index}}" title="{{this.name}}" data-rownum="cell-row{{@index}}">
						{{shorten name 25}}
					</div>
				</tr>
			{{/each}}
		</div>
		<div class="slidable-outer">
			<div class="slidable">
				<div class="slidable-inner">
				</div>
			</div>
		</div>
	</div>
</script>

<script id="gradebook-template-asset" type="text/x-handlebars-template">
	{{#each assets}}
		<div class="gradebook-column" data-colnum="{{@index}}" data-asset-id="{{this.id}}">
			<div class="cell form-name" title="{{this.title}}">
				<div class="form-name-inner">
					<div class="form-title">
						{{shorten title 10}}
					</div>
					<div class="form-type">
						{{#if ../canManage}}
							<select name="type">
								<option value="exam"{{ifAreEqual grade_weight "exam"}}>Exam</option>
								<option value="quiz"{{ifAreEqual grade_weight "quiz"}}>Quiz</option>
								<option value="homework"{{ifAreEqual grade_weight "homework"}}>Homework</option>
							</select>
						{{else}}
							<small>*{{grade_weight}}</small>
						{{/if}}
					</div>
					{{#if ../canManage}}
						<div class="form-delete"></div>
					{{/if}}
				</div>
			</div>
			{{#each ../members}}
				<div class="cell cell-entry cell-row{{@index}}" data-asset-id="{{../id}}" data-student-id="{{this.id}}" data-rownum="cell-row{{@index}}">
					<div class="cell-score">{{getGrade ../../grades this.id ../id}}</div>
					<div class="override{{ifIsOverride ../../grades this.id ../id}}"></div>
				</div>
			{{/each}}
		</div>
	{{/each}}
</script>

<script id="reports-template-main" type="text/x-handlebars-template">
	<div class="reports-container-inner">
		<table>
			<thead>
				<tr>
					<td class="centered"><input type="checkbox" class="checkall" /></td>
					<td>Name</td>
					<td class="numeric">Responses</td>
					<td class="numeric">Average</td>
					<td class="numeric">Min</td>
					<td class="numeric">Max</td>
				</tr>
			</thead>
			<tbody>
				{{#each assets}}
					<tr>
						<td class="centered checkbox">
							{{ifIsForm this.type 'form'}}
						</td>
						<td>
							{{resultDetails this.type 'form'}}
							<span class="form-title">{{this.title}}</span>
						</td>
						<td class="numeric">
							{{getStat ../stats this.id 'responses'}}
						</td>
						<td class="numeric">
							{{getStat ../stats this.id 'average'}}
						</td>
						<td class="numeric">
							{{getStat ../stats this.id 'min'}}
						</td>
						<td class="numeric">
							{{getStat ../stats this.id 'max'}}
						</td>
					</tr>
				{{/each}}
			</tbody>
		</table>
	</div>
</script>

<div class="main-container">
	<div id="message-container"></div>
	<div class="loading">
		<img src="<?php echo Request::base(true); ?>/core/components/com_courses/site/assets/img/loading-light.gif" />
	</div>

	<div class="controls-wrap">
		<div class="controls clear">
			<div title="progress view" class="progress-button button active"></div>
			<div title="gradebook view" class="gradebook-button button"></div>
			<div title="reports view" class="reports-button button"></div>
			<?php echo (!$this->course->config()->get('section_grade_policy', true) && !$this->course->offering()->access('manage'))
				? ''
				: '<div title="edit grade policy" class="progress_button policy button"></div>'; ?>
			<?php echo (!$this->course->access('manage'))
				? ''
				: '<div title="add a new entry" class="gradebook_button addrow button"></div>'; ?>
			<div title="export to csv" class="gradebook_button export button"></div>
			<div title="refresh gradebook view" class="gradebook_button refresh button"></div>
			<div title="download selected detailed results" class="reports_button download button"></div>
		</div>
		<div class="fetching-rows">
			<div class="fetching-rows-inner">
				<div class="fetching-message">loading students...</div>
				<div class="fetching-rows-bar"></div>
			</div>
		</div>
	</div>

	<div class="clear"></div>

	<form action="<?php echo Route::url($base); ?>" class="progress-form"></form>

	<div class="clear"></div>

	<div class="navigation">
		<div class="search-box"><input type="text" placeholder="Search students" /></div>
		<div class="nav-wrap">
			<div class="prv"></div>
			<div class="nxt"></div>
			<div class="slider-container"><div class="slider"></div></div>
		</div>
	</div>
</div>