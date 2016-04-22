<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as state by the Free Software
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('browse.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->config->get('access-create-course')) { ?>
	<div id="content-header-extra">
		<p>
			<a class="add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=course&task=new'); ?>"><?php echo Lang::txt('COM_COURSES_CREATE_COURSE'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
<?php } ?>

<section class="main section">
	<form class="section-inner" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>" method="get">
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_COURSES_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo Lang::txt('COM_COURSES_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo Lang::txt('COM_COURSES_SEARCH_LABEL'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="index" value="<?php echo $this->escape($this->filters['index']); ?>" />
				</fieldset>

				<?php if ($this->filters['tag']) { ?>
					<fieldset class="applied-tags">
						<ol class="tags">
						<?php
						$url  = 'index.php?option=' . $this->option . '&task=browse';
						$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
						$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
						$url .= ($this->filters['index']  ? '&index=' . $this->escape($this->filters['index'])   : '');
						$url .= ($this->filters['group']  ? '&group=' . $this->escape($this->filters['group'])   : '');

						$tags = $this->model->parseTags($this->filters['tag']);
						foreach ($tags as $tag)
						{
							?>
							<li>
								<a href="<?php echo Route::url($url . '&tag=' . implode(',', $this->model->parseTags($this->filters['tag'], $tag))); ?>">
									<?php echo $this->escape(stripslashes($tag)); ?>
									<span class="remove">x</a>
								</a>
							</li>
							<?php
						}
						?>
						</ol>
					</fieldset>
				<?php } ?>
			</div><!-- / .container -->

			<?php if ($this->filters['group']) { ?>
				<div class="course-group">
					<?php
					$group = \Hubzero\User\Group::getInstance($this->filters['group']);

					list($width, $height) = $group->getLogo('size');
					$atts = ($width > $height ? 'height="50"' : 'width="50"');
					?>
					<p class="course-group-img">
						<a href="<?php echo Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
							<img src="<?php echo $group->getLogo(); ?>" <?php echo $atts; ?> alt="<?php echo $this->escape($group->get('description')); ?>" />
						</a>
					</p>
					<p class="course-group-description">
						<?php echo Lang::txt('COM_COURSES_BROUGHT_BY_GROUP'); ?>
					</p>
					<h3 class="course-group-title">
						<a href="<?php echo Route::url('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
							<?php echo $this->escape($group->get('description')); ?>
						</a>
					</h3>
				</div>
			<?php } ?>

			<div class="container">
				<nav class="entries-filters">
					<?php
					$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
					$qs .= ($this->filters['index']  ? '&index=' . $this->escape($this->filters['index'])   : '');
					$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
					$qs .= ($this->filters['group']  ? '&group=' . $this->escape($this->filters['group'])   : '');
					?>
					<ul class="entries-menu order-options">
						<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&sortby=title' . $qs); ?>" title="<?php echo Lang::txt('COM_COURSES_SORT_BY_TITLE'); ?>"><?php echo Lang::txt('COM_COURSES_SORT_TITLE'); ?></a></li>
						<li><a<?php echo ($this->filters['sortby'] == 'alias') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&sortby=alias' . $qs); ?>" title="<?php echo Lang::txt('COM_COURSES_SORT_BY_ALIAS'); ?>"><?php echo Lang::txt('COM_COURSES_SORT_ALIAS'); ?></a></li>
						<li><a<?php echo ($this->filters['sortby'] == 'popularity') ? ' class="active"' : ''; ?> href="<?php echo Route::url('index.php?option='.$this->option.'&task=browse&sortby=popularity' . $qs); ?>" title="<?php echo Lang::txt('COM_COURSES_SORT_BY_POPULARITY'); ?>"><?php echo Lang::txt('COM_COURSES_SORT_POPULARITY'); ?></a></li>
					</ul>
				</nav>

				<h3>
					<?php
						$s = $this->filters['start']+1;
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '')
						{
							if ($this->filters['tag'] != '')
							{
								echo Lang::txt('COM_COURSES_SEARCH_FOR_IN_WITH', $this->escape($this->filters['search']), $this->escape($this->filters['tag']));
							}
							else
							{
								echo Lang::txt('COM_COURSES_SEARCH_FOR_IN', $this->escape($this->filters['search']));
							}
						}
						else if ($this->filters['tag'] != '')
						{
							echo Lang::txt('COM_COURSES_COURSES_WITH', $this->escape($this->filters['tag']));
						}
						else
						{
							echo Lang::txt('COM_COURSES');
						}
						/*if ($this->filters['index']) { ?>
							echo Lang::txt('starting with "%s"', strToUpper($this->filters['index']);
						}*/
					?>
					<?php if ($this->courses->total() > 0) { ?>
						<span><?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?></span>
					<?php } ?>
				</h3>

				<ol class="courses entries">
				<?php
				if ($this->courses->total() > 0)
				{
					require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

					foreach ($this->courses as $course)
					{
						//get status
						$status = '';

						//determine course status
						if ($course->get('state') == 1)
						{
							if ($course->access('manage'))
							{
								$status = 'manager';
							}
						}
						else
						{
							$status = 'new';
						}
						?>
						<li<?php echo ($status) ? ' class="' . $status . '"' : ''; ?>>
							<article>
								<h4>
									<a class="entry-title" href="<?php echo Route::url($course->link()); ?>">
										<?php echo $this->escape($course->get('title')); ?>
									</a>
								</h4>
								<p class="course-identity">
									<a href="<?php echo Route::url($course->link()); ?>">
										<?php if ($logo = $course->logo('url')) { ?>
											<img src="<?php echo Route::url($logo); ?>" alt="<?php echo $this->escape($course->get('title')); ?>" />
										<?php } else { ?>
											<span></span>
										<?php } ?>
									</a>
								</p>
								<dl class="entry-meta">
									<dt>
										<span>
											<?php echo Lang::txt('COM_COURSES_COURSE_NUMBER', $course->get('id')); ?>
										</span>
									</dt>
									<dd class="instructors">
										<?php
										$instructors = $course->instructors();
										if (count($instructors) > 0)
										{
											$names = array();
											foreach ($instructors as $i)
											{
												$instructor = Components\Members\Models\Member::oneOrNew($i->get('user_id'));
												$name = $this->escape(stripslashes($instructor->get('name')));

												$names[] = (in_array($instructor->get('access'), User::getAuthorisedViewLevels()) ? '<a href="' . Route::url($instructor->link()) . '">' . $name . '</a>' : $name);
											}
											echo Lang::txt('COM_COURSES_COURSE_INSTRUCTORS'); ?>: <span class="entry-instructors"><?php echo implode(', ', $names); ?></span><?php
										}
										?>
									</dd>
								</dl>
								<p class="entry-content">
									<?php echo \Hubzero\Utility\String::truncate($course->get('blurb'), 200); ?>
								</p>
							</article>
						</li>
						<?php
					} // for loop
				} else { ?>
					<li class="no-results"><p class="warning"><?php echo Lang::txt('COM_COURSES_NO_RESULTS_FOUND'); ?></p></li>
				<?php } ?>
				</ol>

				<?php
				// Initiate paging
				$pageNav = $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('index', $this->filters['index']);
				$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

				echo $pageNav->render();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo Lang::txt('COM_COURSES_FINDING_A_COURSE'); ?></h3>
				<p><?php echo Lang::txt('COM_COURSES_FINDING_A_COURSE_EXPLANATION'); ?></p>
			</div><!-- / .container -->
			<div class="container">
				<h3><?php echo Lang::txt('COM_COURSES_POPULAR_CATEGORIES'); ?></h3>
				<?php
				$tags = $this->model->tags('cloud', array(
					'limit'    => 20,
					'start'    => 0,
					'sort'     => 'total',
					'sort_Dir' => '',
					'scope'    => 'courses',
					'scope_id' => 0,
					'base'     => 'index.php?option=' . $this->option . '&task=browse',
					'filters'  => $this->filters
				));
				if ($tags) {
					echo $tags;
				} else {
					echo '<p>' . Lang::txt('COM_COURSES_POPULAR_CATEGORIES_NONE') . '</p>';
				} ?>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</form>
</section><!-- / .main section -->
