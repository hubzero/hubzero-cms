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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field  = strtolower(Request::getWord('field', ''));
$action = strtolower(Request::getWord('action', ''));

if ($this->course->isManager())
{
	$filters = array(
		'available' => false,
		'state'     => array(0, 1, 3),
		'sort'      => 'publish_up',
		'sort_Dir'  => 'DESC'
	);
}
else
{
	$filters = array(
		'available' => true,
		'state'     => 1,
		'sort'      => 'publish_up',
		'sort_Dir'  => 'DESC'/*,
		'limit'     => ($this->course->isStudent() ? 0 : 1)*/
	);
}
$offerings = $this->course->offerings($filters, true);

$this->css('course.css')
     ->js()
     ->js('courses.overview.js');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_COURSES'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-browse browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
				<?php echo Lang::txt('COM_COURSES_CATALOG'); ?>
			</a>
		</p>
	</div>
</header>

<section class="course section intro<?php echo ($this->course->get('logo')) ? ' with-identity' : ''; ?>">
	<div class="section-inner">
		<div class="subject">
			<?php if (($field == 'blurb' || $field == 'tags') && $this->course->access('edit', 'course')) { ?>
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" class="form-inplace" method="post">
					<label for="field_title">
						<?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
						<input type="text" name="course[title]" id="field_title" value="<?php echo $this->escape($this->course->get('title')); ?>" />
					</label>

					<label for="field_blurb">
						<?php echo Lang::txt('COM_COURSES_FIELD_BLURB'); ?>
						<textarea name="course[blurb]" id="field_blurb" cols="50" rows="5"><?php echo $this->escape($this->course->get('blurb')); ?></textarea>
					</label>

					<label for="actags">
						<?php echo Lang::txt('COM_COURSES_FIELD_TAGS'); ?>
						<?php echo $this->autocompleter('tags', 'tags', $this->escape($this->course->tags('string')), 'actags'); ?>
						<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_TAGS_HINT'); ?></span>
					</label>

					<p class="submit">
						<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
						<a class="btn btn-secondary" href="<?php echo Route::url($this->course->link()); ?>">
							<?php echo Lang::txt('COM_COURSES_CANCEL'); ?>
						</a>
					</p>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="course" />
					<input type="hidden" name="task" value="save" />

					<?php echo Html::input('token'); ?>

					<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
					<input type="hidden" name="course[id]" value="<?php echo $this->escape($this->course->get('id')); ?>" />
					<input type="hidden" name="course[alias]" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
				</form>
			<?php } else { ?>
				<?php if ($this->course->access('edit', 'course')) { ?>
					<div class="manager-options">
						<a class="icon-edit btn btn-secondary" href="<?php echo Route::url($this->course->link() . '&task=edit&field=blurb'); ?>">
							<?php echo Lang::txt('COM_COURSES_EDIT'); ?>
						</a>
						<span><strong><?php echo Lang::txt('COM_COURSES_FIELDS_TITLE_BLURB'); ?></strong></span>
					</div>
				<?php } ?>

				<div id="course-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
					<h2>
						<?php echo $this->escape($this->course->get('title')); ?>
					</h2>
				</div>
				<p>
					<?php echo $this->escape($this->course->get('blurb')); ?>
				</p>

				<?php echo $this->course->tags('cloud'); ?>

				<?php if ($this->course->get('group_id')) { ?>
					<div class="course-group">
						<?php
						$group = \Hubzero\User\Group::getInstance($this->course->get('group_id'));

						list($width, $height) = $group->getLogo('size');
						$atts = ($width > $height ? 'height="50"' : 'width="50"');
						?>
						<p class="course-group-img">
							<a href="<?php echo Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
								<img src="<?php echo $group->getLogo(); ?>" <?php echo $atts; ?> alt="<?php echo $this->escape(stripslashes($group->get('description'))); ?>" />
							</a>
						</p>
						<p class="course-group-description">
							<?php echo Lang::txt('COM_COURSES_BROUGHT_BY_GROUP'); ?>
						</p>
						<h3 class="course-group-title">
							<a href="<?php echo Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
								<?php echo $this->escape(stripslashes($group->get('description'))); ?>
							</a>
						</h3>
					</div>
				<?php } ?>
			<?php } ?>
		</div><!-- / .subject -->
		<aside class="aside">
			<p class="course-identity">
				<?php if ($logo = $this->course->logo('url')) { ?>
					<img src="<?php 
						$size = $this->course->logo('size');
						echo Route::url($logo);
						?>" class="<?php echo ($size['width'] >= $size['height'] ? 'landscape' : 'portrait'); ?>" alt="<?php echo $this->escape($this->course->get('title')); ?>" />
				<?php } else { ?>
					<span></span>
				<?php } ?>
			</p>
		</aside><!-- / .aside -->
	</div>
</section><!-- / .course section intro -->

<?php if ($this->course->access('edit', 'course') && !$offerings->total()) { ?>
	<section class="course section intro offering-help">
		<div class="section-inner">
			<div class="subject">
				<p>
					<strong><?php echo Lang::txt('COM_COURSES_COURSE_NEEDS_AN_OFFERING'); ?></strong></p>
					<?php echo Lang::txt('COM_COURSES_COURSE_NEEDS_AN_OFFERING_EXPLANATION'); ?>
				</p>
			</div><!-- / .subject -->
			<aside class="aside">
				<p>
					<a class="icon-add btn" id="add-offering" href="<?php echo Route::url($this->course->link() . '&task=newoffering'); ?>">
						<?php echo Lang::txt('COM_COURSES_CREATE_OFFERING'); ?>
					</a>
				</p>
			</aside><!-- / .aside -->
		</div>
	</section><!-- / .course section intro offering-help -->
<?php } ?>

<section class="course section">
	<div class="subject">
		<ul class="sub-menu">
			<?php
			if ($action == 'addpage')
			{
				$this->active = '';
			}

			if ($this->plugins)
			{
				foreach ($this->plugins as $i => $plugin)
				{
					$url = Route::url($this->course->link() . '&active=' . $plugin->get('name'));

					if ($plugin->get('name') == $this->active)
					{
						Pathway::append($plugin->get('title'), $url);

						if ($this->active != 'overview')
						{
							Document::setTitle(Document::getTitle() . ': ' . $plugin->get('title'));
						}
					}
					?>
					<li id="sm-<?php echo $i; ?>"<?php echo ($plugin->get('name') == $this->active) ? ' class="active"' : ''; ?>>
						<a class="tab" data-rel="<?php echo $plugin->get('name'); ?>" href="<?php echo $url; ?>">
							<span><?php echo $this->escape($plugin->get('title')); ?></span>
						</a>
					</li>
					<?php
				}
			}
			?>
			<?php if ($this->course->access('edit', 'course')) { ?>
				<li class="add-page">
					<a class="icon-add tab" href="<?php echo Route::url($this->course->link() . '&action=addpage'); ?>">
						<?php echo Lang::txt('PLG_COURSES_PAGES_ADD_PAGE'); ?>
					</a>
				</li>
			<?php } ?>
		</ul>

		<?php
		foreach ($this->notifications as $notification)
		{
			echo '<p class="' . $notification['type'] . '">' . $notification['message'] . '</p>';
		}

		if (($action == 'addpage' || $action == 'editpage') && $this->course->access('edit', 'course'))
		{
			$page = $this->course->page($this->active);
			?>
			<div class="inner-section" id="addpage-section">
				<form action="<?php echo Route::url($this->course->link()); ?>" class="form-inplace" method="post">
					<fieldset>
						<div class="grid">
							<div class="col span-half">
								<label for="field-title">
									<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_PAGES_REQUIRED'); ?></span>
									<input type="text" name="page[title]" id="field-title" value="<?php echo $this->escape(stripslashes($page->get('title'))); ?>" />
									<span class="hint"><?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE_HINT'); ?></span>
								</label>
							</div>
							<div class="col span-half omega">
								<label for="field-url">
									<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS'); ?> <span class="optional"><?php echo Lang::txt('PLG_COURSES_PAGES_OPTINAL'); ?></span>
									<input type="text" name="page[url]" id="field-url" value="<?php echo $this->escape(stripslashes($page->get('url'))); ?>" />
									<span class="hint"><?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS_HINT'); ?></span>
								</label>
							</div>
						</div>

						<label for="field_description">
							<?php echo $this->editor('page[content]', $this->escape(stripslashes($page->get('content'))), 35, 50, 'field_content'); ?>
						</label>

						<p class="submit">
							<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
							<a class="btn btn-secondary" href="<?php echo Route::url($this->course->link()); ?>">
								<?php echo Lang::txt('COM_COURSES_CANCEL'); ?>
							</a>
						</p>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="course" />
						<input type="hidden" name="task" value="savepage" />

						<?php echo Html::input('token'); ?>

						<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
						<input type="hidden" name="page[id]" value="<?php echo $this->escape($page->get('id')); ?>" />
						<input type="hidden" name="page[alias]" value="<?php echo $this->escape($page->get('alias')); ?>" />
						<input type="hidden" name="page[course_id]" value="<?php echo $this->course->get('id'); ?>" />
						<input type="hidden" name="page[section_id]" value="0" />
						<input type="hidden" name="page[offering_id]" value="0" />
					</fieldset>
				</form>
			</div>
			<?php
		}
		elseif ($this->plugins)
		{
			foreach ($this->plugins as $plugin)
			{
				if ($html = $plugin->get('html'))
				{
					?>
					<div class="inner-section" id="<?php echo $plugin->get('name'); ?>-section">
						<?php if ($this->course->access('edit', 'course') && $plugin->get('isPage')) { ?>
							<div class="manager-options">
								<a class="icon-error btn btn-secondary btn-danger" href="<?php echo Route::url($this->course->link() . '&active=' . $plugin->get('name') . '&task=deletepage'); ?>">
									<?php echo Lang::txt('COM_COURSES_DELETE'); ?>
								</a>
								<a class="icon-edit btn btn-secondary" href="<?php echo Route::url($this->course->link() . '&active=' . $plugin->get('name') . '&action=editpage'); ?>">
									<?php echo Lang::txt('COM_COURSES_EDIT'); ?>
								</a>
								<span><strong><?php echo Lang::txt('COM_COURSES_PAGE_CONTENTS'); ?></strong></span>
							</div>
						<?php } ?>
						<?php echo $html; ?>
					</div><!-- / .inner-section -->
					<?php
				}
			}
		}
		?>
	</div><!-- / .subject -->
	<aside class="aside">
		<?php if ($field == 'summary' && $this->course->access('edit', 'course')) { ?>
			<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" class="form-inplace course-summary" method="post">
				<label for="field_length">
					<?php echo Lang::txt('COM_COURSES_COURSE_LENGTH'); ?><br />
					<input type="text" name="course[length]" id="field_length" value="<?php echo $this->escape($this->course->get('length')); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_COURSE_LENGTH_HINT'); ?>" />
				</label>

				<label for="field_effort">
					<?php echo Lang::txt('COM_COURSES_COURSE_EFFORT'); ?><br />
					<input type="text" name="course[effort]" id="field_effort" value="<?php echo $this->escape($this->course->get('effort')); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_COURSE_EFFORT_HINT'); ?>" />
				</label>

				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
					<a class="btn btn-secondary" href="<?php echo Route::url($this->course->link()); ?>">
						<?php echo Lang::txt('COM_COURSES_CANCEL'); ?>
					</a>
				</p>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="course" />
				<input type="hidden" name="task" value="save" />

				<?php echo Html::input('token'); ?>

				<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
				<input type="hidden" name="course[id]" value="<?php echo $this->escape($this->course->get('id')); ?>" />
				<input type="hidden" name="course[alias]" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
			</form>
		<?php } else { ?>
			<?php
			if ($this->course->access('edit', 'course'))
			{
				?>
				<div class="manager-options">
					<a class="icon-edit btn btn-secondary" href="<?php echo Route::url($this->course->link() . '&task=edit&field=summary'); ?>">
						<?php echo Lang::txt('COM_COURSES_EDIT'); ?>
					</a>
					<span><strong><?php echo Lang::txt('COM_COURSES_SUMMARY'); ?></strong></span>
				</div>
				<?php
			}
			?>
			<div class="course-summary">
				<table>
					<tbody>
						<?php if ($this->course->config('show_stats')) { ?>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_COURSES_COURSE_ENROLLED'); ?>:
								</th>
								<td>
									<?php echo number_format((int) $this->course->students(array('count' => true))); ?>
								</td>
							</tr>
						<?php } ?>
						<?php if ($length = $this->course->get('length')) { ?>
							<tr>
								<th scope="row">
									<?php echo Lang::txt('COM_COURSES_COURSE_LENGTH'); ?>:
								</th>
								<td>
									<?php echo $this->escape($length); ?>
								</td>
							</tr>
						<?php } ?>
						<?php if ($effort = $this->course->get('effort')) { ?>
							<tr>
								<th scope="row">
									<?php echo Lang::txt('COM_COURSES_COURSE_EFFORT'); ?>:
								</th>
								<td>
									<?php echo $this->escape($effort); ?>
								</td>
							</tr>
						<?php } ?>
						<?php
						$cert = false;
						if ($this->course->certificate()->exists())
						{
							foreach ($offerings as $offering)
							{
								$sections = $offering->sections(array(
									'state'      => 1,
									'available'  => true
								));
								foreach ($sections as $section)
								{
									if ($section->params('certificate') && $section->get('enrollment') != 2)
									{
										$cert = true;
										break;
									}
								}
							}
						}
						if ($cert) {
						?>
							<tr>
								<th scope="row">
									<?php echo Lang::txt('COM_COURSES_COURSE_CERTIFICATE'); ?>:
								</th>
								<td>
									<?php echo Lang::txt('COM_COURSES_COURSE_CERTIFICATE_AVAILABLE'); ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

				<?php
				$c = 0;
				if ($offerings->total())
				{
					$found = false;

					// If a student...
					if ($this->course->isStudent())
					{
						// Filters for getting all available sections
						// includng the default
						$filters = array(
							'state'      => 1,
							'available'  => true
						);

						foreach ($offerings as $offering)
						{
							$s = array();

							// Get all the sections for this offering
							$sections = $offering->sections($filters);
							if ($sections->total() > 0)
							{
								// Loop through all sections and collect ones 
								// the user is a student in
								foreach ($sections as $section)
								{
									// If not a student in *this* section
									if (!$section->isMember())
									{
										continue;
									}

									$s[] = $section;
								}
							}

							// If there's more than one section...
							if (count($s) > 1)
							{
								$offering->section($s[0]);

								$this->view('_button')
								     ->set('course', $this->course)
								     ->set('offering', $offering)
								     ->set('section', $s[0])
								     ->set('sections', $s)
								     ->display();

								$found = true;
							}
							// If only one section...
							else if (count($s) == 1)
							{
								$offering->section($s[0]);
								?>
								<p>
									<a class="enroll btn" href="<?php echo Route::url($offering->link('enter')); ?>">
										<?php echo Lang::txt('COM_COURSES_ACCESS_COURSE'); ?>
									</a>
								</p>
								<?php

								$found = true;
							}

							$c++;
						}
					}

					if (!$found)
					{
						// If a course manager,
						// show all sections regardless of published state
						if ($this->course->isManager())
						{
							$filters = array(
								'available'  => false
							);
						}
						else
						{
							$filters = array(
								'state'      => 1,
								'available'  => true,
								'enrollment' => array(0, 1),
								'started'    => true,
								'ended'      => false,
								'is_default' => 0
							);
						}

						foreach ($offerings as $offering)
						{
							// Try to load the default section
							$dflt = $offering->section('!!default!!');
							if (!$dflt->exists())
							{
								// No default? Get the first in the list
								if (!$offering->sections($filters, true)->total())
								{
									$offering->makeSection();
								}
								$dflt = $offering->sections()->fetch('first');
								$offering->section($dflt);
							}

							$sections = $offering->sections($filters, true);
							if ($this->course->isManager() && $sections->total() > 0)
							{
								$this->view('_button')
								     ->set('course', $this->course)
								     ->set('offering', $offering)
								     ->set('section', $dflt)
								     ->set('sections', $sections)
								     ->display();

								$c++;
							}
							else
							{
								// If enrollment is closed on the default section
								if ($dflt->get('enrollment') == 2 && !$dflt->isMember())
								{
									continue;
								}
								?>
								<p>
									<a class="enroll btn" href="<?php echo Route::url($offering->link('enter')); ?>">
										<?php echo Lang::txt('COM_COURSES_ACCESS_COURSE'); ?>
									</a>
								</p>
								<?php
								$c++;

								if (!$this->course->isManager())
								{
									break;
								}
							}
						}
					}
				}

				if (!$c)
				{
					?>
					<p>
						<?php echo Lang::txt('COM_COURSES_NO_OFFERINGS_AVAILABLE'); ?>
					</p>
					<?php
				}
				?>
			</div>
		<?php } ?>

		<?php
		if ($this->course->access('edit', 'course'))
		{
			?>
			<div class="manager-options">
				<a class="icon-edit btn btn-secondary" id="manage-instructors" href="<?php echo Route::url($this->course->link() . '&task=instructors'); ?>">
					<?php echo Lang::txt('COM_COURSES_MANAGE'); ?>
				</a>
				<span><strong><?php echo Lang::txt('COM_COURSES_MANAGE_INSTRUCTORS'); ?></strong></span>
			</div>
			<?php
		}

		$instructors = $this->course->instructors();
		if (count($instructors) > 0)
		{
			require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';
			?>
			<div class="course-instructors" data-bio-length="200">
				<h3>
					<?php echo (count($instructors) > 1) ? Lang::txt('COM_COURSES_ABOUT_THE_INSTRUCTORS') : Lang::txt('COM_COURSES_ABOUT_THE_INSTRUCTOR'); ?>
				</h3>
				<?php
				foreach ($instructors as $i)
				{
					$this->view('_instructor')
					     ->set('biolength', 200)
					     ->set('instructor', Components\Members\Models\Member::oneOrNew($i->get('user_id')))
					     ->display();
				}
				?>
			</div>
			<?php
		}
		else
		{
			?>
			<div class="course-instructors-none">
				<?php echo Lang::txt('COM_COURSES_NO_INSTRUCTORS_FOUND'); ?>
			</div>
			<?php
		}

		if ($this->plugins)
		{
			foreach ($this->plugins as $plugin)
			{
				if ($meta = $plugin->get('metadata'))
				{
					echo $meta;
				}
			}
		}
		?>
	</aside><!-- / .aside -->
</section><!-- / .course section -->

<?php
$after = Event::trigger('courses.onCourseViewAfter', array($this->course));
if ($after && count($after) > 0) { ?>
<section class="below course section">
	<?php echo implode("\n", $after); ?>
</section><!-- / .course section -->
<?php } ?>