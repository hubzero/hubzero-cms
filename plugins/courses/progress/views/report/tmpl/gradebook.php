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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = '/index.php?option='.$this->option.'&controller=offering&active=progress&gid='.$this->course->get('alias');
$base .= '&offering='.$this->course->offering()->get('alias').'&section='.$this->course->offering()->section()->get('alias');

?>

<script id="gradebook-template" type="text/x-handlebars-template">
	<div class="gradebook-container">
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
				{{#each assets}}
					<div class="gradebook-column" data-colnum="{{@index}}">
						<div class="cell form-name" title="{{this.title}}">
							<div class="form-name-inner">
								<div class="form-title">
									{{shorten title 10}}
								</div>
								<div class="form-type">
									<select name="type" disabled="disabled">
										<option value="exam"{{ifAreEqual subtype "exam"}}>Exam</option>
										<option value="quiz"{{ifAreEqual subtype "quiz"}}>Quiz</option>
										<option value="homework"{{ifAreEqual subtype "homework"}}>Homework</option>
									</select>
								</div>
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
			</div>
		</div>
		</div>
	</div>
</script>

<div class="gradebook">
	<div class="loading">
		<img src="/components/com_courses/assets/img/loading-light.gif" />
	</div>

	<form action="<?php echo JRoute::_($base); ?>" class="gradebook-form"></form>

	<div class="clear"></div>

	<div class="navigation">
		<div class="prv"></div>
		<div class="nxt"></div>
		<div class="slider-container"><div class="slider"></div></div>
	</div>
</div>