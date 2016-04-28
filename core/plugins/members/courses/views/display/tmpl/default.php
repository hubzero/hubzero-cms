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

$base = $this->member->link() . '&active=courses';

$this->css();
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_MEMBERS_COURSES'); ?>
</h3>

<section class="section">
<?php if ($this->hasRoles) { ?>

	<div class="container" id="courses-container">
		<form method="get" action="<?php echo Route::url($base); ?>">
			<nav class="entries-filters">

				<?php if ($this->roles && $this->hasRoles > 1) { ?>
					<ul class="entries-menu user-options">
						<?php foreach ($this->roles as $s) { ?>
							<?php
							if ($s->total <= 0)
							{
								continue;
							}
							$sel = '';
							if ($this->filters['task'] == $s->alias)
							{
								//$active = $s;
								$sel = 'active';
							}
							?>
							<li>
								<a class="<?php echo $s->alias . ' ' . $sel; ?>" title="<?php echo $this->escape(stripslashes($s->title)); ?>" href="<?php echo Route::url($base . '&task=' . $s->alias . '&sort=' . $this->filters['sort']); ?>">
									<?php echo $this->escape(stripslashes($s->title)); ?> (<?php echo $this->escape($s->total); ?>)
								</a>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>

				<ul class="entries-menu order-options">
					<li>
						<a<?php echo ($this->filters['sort'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo Route::url($base . '&task=' . urlencode($this->filters['task']) . '&sort=title'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COURSES_SORT_BY_TITLE'); ?>">
							<?php echo Lang::txt('PLG_MEMBERS_COURSES_SORT_TITLE'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->filters['sort'] == 'enrolled') ? ' class="active"' : ''; ?> href="<?php echo Route::url($base . '&task=' . urlencode($this->filters['task']) . '&sort=enrolled'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COURSES_SORT_BY_ENROLLED'); ?>">
							<?php echo Lang::txt('PLG_MEMBERS_COURSES_SORT_ENROLLED'); ?>
						</a>
					</li>
				</ul>
			</nav>

			<table class="courses entries">
				<caption>
					<?php
					$s = ($this->total > 0) ? $this->filters['start']+1 : 0; //($this->filters['start'] > 0) ? $this->filters['start']+1 : $this->filters['start'];
					$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

					echo $this->escape(stripslashes($this->active->title)); //Lang::txt('PLG_MEMBERS_COURSES_' . strtoupper($this->filters['task']));
					?>
					<span>(<?php echo Lang::txt('PLG_MEMBERS_COURSES_RESULTS_TOTAL', $s, $e, $this->total); ?>)</span>
				</caption>
				<tbody>
		<?php if (count($this->results) > 0) { ?>
			<?php
				foreach ($this->results as $row)
				{
					$cls = '';
					$sfx = '';

					if (isset($row->offering_alias))
					{
						$sfx .= '&offering=' . $row->offering_alias;
					}
					if (isset($row->section_alias) && !$row->is_default)
					{
						$sfx .= ':' . $row->section_alias;
					}

					switch ($this->filters['task'])
					{
						case 'student':
							$cls = 'student';
							$dateText = Lang::txt('PLG_MEMBERS_COURSES_ENROLLED');
						break;

						case 'manager':
						case 'instructor':
						case 'ta':
						default:
							$cls = 'manager';
							$dateText = Lang::txt('PLG_MEMBERS_COURSES_EMPOWERED');
						break;
					}
			?>
					<tr class="course<?php echo ($cls) ? ' ' . $cls : ''; ?>">
						<th>
							<span class="entry-id"><?php echo $row->id; ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo Route::url('index.php?option=com_courses&gid=' . $row->alias . $sfx); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a><br />
							<span class="entry-details">
								<?php echo $dateText; ?>
								<!--
								<span class="entry-date-at"><?php echo Lang::txt('PLG_MEMBERS_COURSES_AT'); ?></span>
								<span class="entry-time"><time datetime="<?php echo $row->enrolled; ?>"><?php echo Date::of($row->enrolled)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
								-->
								<span class="entry-date-on"><?php echo Lang::txt('PLG_MEMBERS_COURSES_ON'); ?></span>
								<span class="entry-date"><time datetime="<?php echo $row->enrolled; ?>"><?php echo Date::of($row->enrolled)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
								<?php if ($row->section_title) { ?>
								<span class="entry-section">
									 &mdash; <strong><?php echo Lang::txt('PLG_MEMBERS_COURSES_SECTION'); ?></strong> <?php echo $this->escape(stripslashes($row->section_title)); ?>
								</span>
								<?php } ?>
							</span>
						</td>
						<td>
							<?php if ($row->state == 3) { ?>
							<span class="entry-state draft">
								<?php echo Lang::txt('PLG_MEMBERS_COURSES_STATE_DRAFT'); ?>
							</span>
							<?php } ?>
						</td>
						<td>
							<?php if ($row->starts) { ?>
							<?php echo Lang::txt('PLG_MEMBERS_COURSES_STARTS'); ?><br />
							<span class="entry-details">
							<?php if ($row->starts != '0000-00-00 00:00:00') { ?>
								<span class="entry-date-at"><?php echo Lang::txt('PLG_MEMBERS_COURSES_AT'); ?></span>
								<span class="entry-time"><time datetime="<?php echo $row->starts; ?>"><?php echo Date::of($row->starts)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
								<span class="entry-date-on"><?php echo Lang::txt('PLG_MEMBERS_COURSES_ON'); ?></span>
								<span class="entry-date"><time datetime="<?php echo $row->starts; ?>"><?php echo Date::of($row->starts)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
							<?php } else { ?>
								<?php echo Lang::txt('PLG_MEMBERS_COURSES_NA'); ?>
							<?php } ?>
							</span>
							<?php } ?>
						</td>
						<td>
							<?php if ($row->ends) { ?>
							<?php echo Lang::txt('PLG_MEMBERS_COURSES_ENDS'); ?><br />
							<span class="entry-details">
								<?php if ($row->ends != '0000-00-00 00:00:00') { ?>
								<span class="entry-date-at"><?php echo Lang::txt('PLG_MEMBERS_COURSES_AT'); ?></span>
								<span class="entry-time"><time datetime="<?php echo $row->ends; ?>"><?php echo Date::of($row->ends)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
								<span class="entry-date-on"><?php echo Lang::txt('PLG_MEMBERS_COURSES_ON'); ?></span>
								<span class="entry-date"><time datetime="<?php echo $row->ends; ?>"><?php echo Date::of($row->ends)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
								<?php } else { ?>
									<?php echo Lang::txt('PLG_MEMBERS_COURSES_NA'); ?>
								<?php } ?>
							</span>
							<?php } ?>
						</td>
					</tr>
			<?php
				}
			?>
		<?php } else { ?>
					<tr>
						<td>
							<?php echo Lang::txt('PLG_MEMBERS_COURSES_NO_RESULTS'); ?>
						</td>
					</tr>
		<?php } // end if (count($this->results) > 0) { ?>
				</tbody>
			</table>

			<?php
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
			$pageNav->setAdditionalUrlParam('active', 'courses');
			$pageNav->setAdditionalUrlParam('task', $this->filters['task']);
			$pageNav->setAdditionalUrlParam('action', '');
			$pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);

			echo $pageNav->render();
			?>
			<div class="clearfix"></div>
		</form>
	</div>
<?php } else { ?>
	<div id="courses-introduction">
		<div class="instructions">
			<ol>
				<li><?php echo Lang::txt('PLG_MEMBERS_COURSES_FIND_COURSE', Route::url('index.php?option=com_courses')); ?></li>
				<li><?php echo Lang::txt('PLG_MEMBERS_COURSES_ENROLL'); ?></li>
				<li><?php echo Lang::txt('PLG_MEMBERS_COURSES_GET_LEARNING'); ?></li>
			</ol>
		</div><!-- / .instructions -->
		<div class="questions">
			<p><strong><?php echo Lang::txt('PLG_MEMBERS_COURSES_WHAT_ARE_COURSES'); ?></strong></p>
			<p><?php echo Lang::txt('PLG_MEMBERS_COURSES_EXPLANATION'); ?><p>
		</div><!-- / .post-type -->
	</div><!-- / #collection-introduction -->
<?php } ?>
</section>