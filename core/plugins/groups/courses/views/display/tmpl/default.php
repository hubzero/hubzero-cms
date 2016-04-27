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

$this->css();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=courses';
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_GROUPS_COURSES'); ?>
</h3>

<section class="section">
<?php if (count($this->results) > 0) { ?>
	<div class="container" id="courses-container">
		<form method="get" action="<?php Route::url($base); ?>">
			<nav class="entries-filters">
				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				?>
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo Route::url($base . '&sortby=title' . $qs); ?>" title="<?php echo Lang::txt('PLG_GROUPS_COURSES_SORT_BY_TITLE'); ?>"><?php echo Lang::txt('PLG_GROUPS_COURSES_SORT_TITLE'); ?></a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'popularity') ? ' class="active"' : ''; ?> href="<?php echo Route::url($base . '&sortby=popularity' . $qs); ?>" title="<?php echo Lang::txt('PLG_GROUPS_COURSES_SORT_BY_ENROLLED'); ?>"><?php echo Lang::txt('PLG_GROUPS_COURSES_SORT_ENROLLED'); ?></a></li>
				</ul>
			</nav>

			<table class="courses entries">
				<caption>
					<?php
					$s = ($this->total > 0) ? $this->filters['start']+1 : 0;
					$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

					echo $this->escape(Lang::txt('PLG_GROUPS_COURSES'));
					?>
					<span>(<?php echo Lang::txt('PLG_GROUPS_COURSES_RESULTS_TOTAL', $s, $e, $this->total); ?>)</span>
				</caption>
				<tbody>
				<?php
				foreach ($this->results as $course)
				{
					?>
					<tr class="course">
						<th>
							<span class="entry-id"><?php echo $course->get('id'); ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo Route::url($course->link()); ?>">
								<?php echo $this->escape(stripslashes($course->get('title'))); ?>
							</a><br />
						<?php
							$instructors = $course->instructors();
							if (count($instructors) > 0)
							{
								$names = array();
								foreach ($instructors as $i)
								{
									$instructor = User::getInstance($i->get('user_id'));

									$names[] = '<a href="' . Route::url('index.php?option=com_members&id=' . $i->get('user_id')) . '">' . $this->escape(stripslashes($instructor->get('name'))) . '</a>';
								}
						?>
							<span class="entry-details">
								Instructors: <span class="entry-instructors"><?php echo implode(', ', $names); ?></span>
							</span>
							<span class="entry-content">
								<?php echo \Hubzero\Utility\String::truncate(stripslashes($course->get('blurb')), 200); ?>
							</span>
						</td>
						<td>
						<?php
							}
						?>
							<span class="<?php
							switch ($course->get('state'))
							{
								case 3: echo 'draft'; break;
								case 2: echo 'trashed'; break;
								case 1: echo 'published'; break;
								case 0: echo 'unpublished'; break;
							}
							?> entry-state">
							<?php
							switch ($course->get('state'))
							{
								case 3: echo Lang::txt('PLG_GROUPS_COURSES_STATE_DRAFT'); break;
								case 2: echo Lang::txt('PLG_GROUPS_COURSES_STATE_DELETED'); break;
								case 1: echo Lang::txt('PLG_GROUPS_COURSES_STATE_PUBLISHED'); break;
								case 0: echo Lang::txt('PLG_GROUPS_COURSES_STATE_UNPUBLISHED'); break;
							}
							?>
							</span>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>

			<?php
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'courses');
			$pageNav->setAdditionalUrlParam('action', '');
			$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

			echo $pageNav->render();
			?>
			<div class="clearfix"></div>
		</form>
	</div>
<?php } else { ?>
	<div id="courses-introduction">
		<div class="instructions">
			<p><?php echo Lang::txt('PLG_GROUPS_COURSES_NONE'); ?></p>
		</div><!-- / .instructions -->
		<div class="questions">
			<p><strong><?php echo Lang::txt('PLG_GROUPS_COURSES_WHAT_IS_THIS'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_GROUPS_COURSES_ABOUT_PLUGIN'); ?><p>
			<p><strong><?php echo Lang::txt('PLG_GROUPS_COURSES_WHAT_ARE_COURSES'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_GROUPS_COURSES_EXPLANATION'); ?><p>
		</div><!-- / .post-type -->
	</div><!-- / #collection-introduction -->
<?php } ?>
</section>