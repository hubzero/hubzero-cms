<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

if ($this->course->access('edit', 'course'))
{
	$this->js('jquery.fileuploader.js', 'system');
}
$manager = $this->course->manager(User::get('id'));

$this->css('course.css')
     ->js('courses.overview.js');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_COURSES'); ?></h2>

	<div id="content-header-extra">
		<p>
			<?php if ($this->course->access('edit', 'course') && $this->course->access('create', 'course')) { ?>
				<?php if ($manager && $manager->get('id')) { ?>
					<a class="btn icon-copy copy" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&task=copy'); ?>">
						<?php echo Lang::txt('COM_COURSES_COPY'); ?>
					</a>
				<?php } elseif ($this->course->config('allow_forks')) { ?>
					<a class="btn icon-fork fork" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&task=fork'); ?>">
						<?php echo Lang::txt('COM_COURSES_FORK'); ?>
					</a>
				<?php } ?>
			<?php } ?>
			<a class="btn icon-browse browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
				<?php echo Lang::txt('COM_COURSES_CATALOG'); ?>
			</a>
		</p>
	</div>
</header>

<?php if ($this->course->access('edit', 'course') && $this->course->get('state') != 1) { ?>
	<div class="manager-options draft">
		<a class="icon-edit btn btn-secondary btn-success" href="<?php echo Route::url($this->course->link() . '&task=publish'); ?>">
			<?php echo Lang::txt('COM_COURSES_PUBLISH'); ?>
		</a>
		<span><strong><?php echo Lang::txt('COM_COURSES_FIELDS_STATE_DRAFT'); ?></strong></span>
	</div>
<?php } ?>

<section class="course section intro<?php echo ($this->course->get('logo')) ? ' with-identity' : ''; ?>">
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">
			<?php if (($field == 'blurb' || $field == 'tags') && $this->course->access('edit', 'course')) { ?>
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" class="form-inplace" method="post">
					<div class="form-group">
						<label for="field_title">
							<?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
							<input type="text" name="course[title]" id="field_title" class="form-control" value="<?php echo $this->escape($this->course->get('title')); ?>" />
						</label>
					</div>

					<div class="form-group">
						<label for="field_blurb">
							<?php echo Lang::txt('COM_COURSES_FIELD_BLURB'); ?>
							<textarea name="course[blurb]" id="field_blurb" class="form-control" cols="50" rows="5"><?php echo $this->escape($this->course->get('blurb')); ?></textarea>
						</label>
					</div>

					<div class="form-group">
						<label for="actags">
							<?php echo Lang::txt('COM_COURSES_FIELD_TAGS'); ?>
							<?php echo $this->autocompleter('tags', 'tags', $this->escape($this->course->tags('string')), 'actags'); ?>
							<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_TAGS_HINT'); ?></span>
						</label>
					</div>

					<div class="form-group form-check">
						<label for="params-allow_forks" class="form-check-label">
							<input type="checkbox" class="option form-check-input" name="params[allow_forks]" id="params-allow_forks" <?php if ($this->course->config('allow_forks')) { echo 'checked="checked"'; } ?> value="1" />
							<?php echo Lang::txt('COM_COURSES_ALLOW_FORKS'); ?>
						</label>
						<span class="hint"><?php echo Lang::txt('COM_COURSES_ALLOW_FORKS_HINT'); ?></span>
					</div>

					<p class="submit">
						<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
						<a class="btn btn-secondary" href="<?php echo Route::url($this->course->link()); ?>">
							<?php echo Lang::txt('JCANCEL'); ?>
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
							<?php echo Lang::txt('JACTION_EDIT'); ?>
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
			<div class="course-identity">
				<?php if ($logo = $this->course->logo('url')) { ?>
					<img src="<?php 
						$size = $this->course->logo('size');
						echo Route::url($logo);
						?>" class="<?php echo ($size['width'] >= $size['height']) ? 'landscape' : 'portrait'; ?>" alt="<?php echo $this->escape($this->course->get('title')); ?>" />
				<?php } else { ?>
					<span></span>
				<?php } ?>
			<?php if ($this->course->access('edit', 'course')) { ?>
				<div id="ajax-uploader"
					data-instructions="<?php echo Lang::txt('COM_COURSES_CLICK_OR_DROP_FILE'); ?>"
					data-action="<?php echo Route::url('index.php?option=' . $this->option . '&no_html=1&controller=media&task=upload&listdir=' . $this->course->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
					<noscript>
						<form action="<?php echo Route::url($this->course->link()); ?>" class="form-inplace" method="post">
							<div class="form-group">
								<label for="upload">
									<?php echo Lang::txt('COM_SUPPORT_COMMENT_FILE'); ?>:
									<input type="file" name="upload" id="upload" />
								</label>
							</div>

							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="media" />
							<input type="hidden" name="task" value="upload" />
							<input type="hidden" name="listdir" value="<?php echo $this->course->get('id'); ?>" />
							<?php echo Html::input('token'); ?>
						</form>
					</noscript>
				</div>
			<?php } ?>
			</div>
		</aside><!-- / .aside -->
	</div>
</section><!-- / .course section intro -->

<?php if ($this->course->access('edit', 'course') && !$offerings->total()) { ?>
	<section class="course section intro offering-help">
		<div class="section-inner hz-layout-with-aside">
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
	<div class="section-inner hz-layout-with-aside">
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
									<div class="form-group">
										<label for="field-title">
											<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_PAGES_REQUIRED'); ?></span>
											<input type="text" name="page[title]" id="field-title" class="form-control" value="<?php echo $this->escape(stripslashes($page->get('title'))); ?>" />
											<span class="hint"><?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_TITLE_HINT'); ?></span>
										</label>
									</div>
								</div>
								<div class="col span-half omega">
									<div class="form-group">
										<label for="field-url">
											<?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS'); ?> <span class="optional"><?php echo Lang::txt('PLG_COURSES_PAGES_OPTINAL'); ?></span>
											<input type="text" name="page[url]" id="field-url" class="form-control" value="<?php echo $this->escape(stripslashes($page->get('url'))); ?>" />
											<span class="hint"><?php echo Lang::txt('PLG_COURSES_PAGES_FIELD_ALIAS_HINT'); ?></span>
										</label>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="field_description">
									<?php echo $this->editor('page[content]', $this->escape(stripslashes($page->get('content'))), 35, 50, 'field_content', array('class' => 'form-control')); ?>
								</label>
							</div>

							<p class="submit">
								<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
								<a class="btn btn-secondary" href="<?php echo Route::url($this->course->link()); ?>">
									<?php echo Lang::txt('JCANCEL'); ?>
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
										<?php echo Lang::txt('JACTION_EDIT'); ?>
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
				<div class="form-group">
					<label for="field_length">
						<?php echo Lang::txt('COM_COURSES_COURSE_LENGTH'); ?><br />
						<input type="text" name="course[length]" id="field_length" class="form-control" value="<?php echo $this->escape($this->course->get('length')); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_COURSE_LENGTH_HINT'); ?>" />
					</label>
				</div>

				<div class="form-group">
					<label for="field_effort">
						<?php echo Lang::txt('COM_COURSES_COURSE_EFFORT'); ?><br />
						<input type="text" name="course[effort]" id="field_effort" class="form-control" value="<?php echo $this->escape($this->course->get('effort')); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_COURSE_EFFORT_HINT'); ?>" />
					</label>
				</div>

				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
					<a class="btn btn-secondary" href="<?php echo Route::url($this->course->link()); ?>">
						<?php echo Lang::txt('JCANCEL'); ?>
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
						<?php echo Lang::txt('JACTION_EDIT'); ?>
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
	</div>
</section><!-- / .course section -->

<?php
$after = Event::trigger('courses.onCourseViewAfter', array($this->course));
if ($after && count($after) > 0) { ?>
<section class="below course section">
	<?php echo implode("\n", $after); ?>
</section><!-- / .course section -->
<?php }
