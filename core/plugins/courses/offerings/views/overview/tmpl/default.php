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
?>
<?php if ($this->course->access('edit', 'course')) { ?>
<div class="manager-options">
	<a class="icon-add btn btn-secondary" id="add-offering" href="<?php echo Route::url($this->course->link() . '&task=newoffering'); ?>">
		<?php echo Lang::txt('PLG_COURSES_OFFERINGS_NEW_OFFERING'); ?>
	</a>
	<span><strong><?php echo Lang::txt('PLG_COURSES_OFFERINGS_NEW_OFFERING_EXPLANATION'); ?></strong></span>
</div>
<?php } ?>
<div class="container">
	<table class="entries">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('PLG_COURSES_OFFERINGS_OFFERING'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_COURSES_OFFERINGS_ENROLLED'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_COURSES_OFFERINGS_ENROLLMENT'); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php
	/*$offerings = $this->course->offerings(array(
		'state'    => 1,
		'sort_Dir' => 'ASC'
	), true);*/
	$offerings = $this->course->offerings();

	if ($offerings->total() > 0)
	{
		$now = Date::toSql();

		foreach ($offerings as $offering)
		{
			if ($offering->isDeleted())
			{
				continue;
			}
			if ($this->course->isManager())
			{
				$offering->sections(array('available' => false));
			}
			?>
			<tr>
				<th class="offering-title">
					<span>
						<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
					</span>
				</th>
			<?php if ($offering->sections()->total() <= 1) { ?>
				<?php
				$section = $offering->sections()->fetch('first');
				if (is_object($section))
				{
					$offering->section($section->get('id'));
				}
				?>
				<td>
					<?php if ($this->course->isManager()) { ?>
						<a class="access btn" href="<?php echo Route::url($offering->link('enter')); ?>">
							<?php echo Lang::txt('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
						</a>
					<?php } else if ($offering->student(User::get('id'))->get('student')) { ?>
						<a class="access btn" href="<?php echo Route::url($offering->link('enter')); ?>">
							<?php echo Lang::txt('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
						</a>
					<?php } else { ?>
						<?php if ($offering->isAvailable()) { //$offerings->total() > 1 && ?>
							<a class="enroll btn" href="<?php echo Route::url($offering->link('enroll')); ?>">
								<?php echo Lang::txt('PLG_COURSES_OFFERINGS_ENROLL_IN_COURSE'); ?>
							</a>
						<?php } else { ?>
							--
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($offering->isAvailable()) { ?>
					<span class="accepting enrollment">
						<?php echo Lang::txt('PLG_COURSES_OFFERINGS_STATUS_ACCEPTING'); ?>
					</span>
					<?php } else { ?>
					<span class="closed enrollment">
						<?php echo Lang::txt('PLG_COURSES_OFFERINGS_STATUS_CLOSED'); ?>
					</span>
					<?php } ?>
				</td>
			<?php } else { ?>
				<td>
					&nbsp;
				</td>
				<td>
					&nbsp;
				</td>
			<?php } ?>
			</tr>
			<?php
			if ($offering->sections()->total() > 1)
			{
				foreach ($offering->sections() as $section)
				{
					if ($section->isDeleted())
					{
						continue;
					}
					if (!$this->course->isManager())
					{
						// If section is in draft mode or not published
						if ($section->isDraft() || !$section->isPublished())
						{
							continue;
						}
						// If section hasn't started or has ended
						if (!$section->started() || $section->ended())
						{
							continue;
						}
						// If a publish down time is set and that time happened before now
						if ($section->get('publish_down') != '0000-00-00 00:00:00' && $section->get('publish_down') <= $now)
						{
							continue;
						}
						// If not already a member and enrollment is closed
						if (!$section->isMember() && $section->get('enrollment') == 2)
						{
							continue;
						}
					}
					$offering->section($section->get('id'));
				?>
			<tr>
				<th class="section-title">
					<span>
						<?php echo $this->escape(stripslashes($section->get('title'))); ?>
					</span>
				</th>
				<td>
					<?php if ($this->course->isManager()) { ?>
						<a class="access btn" href="<?php echo Route::url($offering->link('enter')); ?>">
							<?php echo Lang::txt('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
						</a>
					<?php } else if ($section->isMember()) { ?>
						<a class="access btn" href="<?php echo Route::url($offering->link('enter')); ?>">
							<?php echo Lang::txt('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
						</a>
					<?php } else { ?>
						<?php if ($offering->isAvailable()) { //$offerings->total() > 1 && ?>
							<a class="enroll btn" href="<?php echo Route::url($offering->link('enroll')); ?>">
								<?php echo Lang::txt('PLG_COURSES_OFFERINGS_ENROLL_IN_COURSE'); ?>
							</a>
						<?php } else { ?>
							--
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php
					switch ($section->get('enrollment'))
					{
						case 0:
							?>
							<span class="accepting enrollment">
								<?php echo Lang::txt('PLG_COURSES_OFFERINGS_STATUS_ACCEPTING'); ?>
							</a>
							<?php
						break;

						case 1:
							?>
							<span class="restricted enrollment">
								<?php echo Lang::txt('PLG_COURSES_OFFERINGS_STATUS_RESTRICTED'); ?>
							</span>
							<?php
						break;

						case 2:
							?>
							<span class="closed enrollment">
								<?php echo Lang::txt('PLG_COURSES_OFFERINGS_STATUS_CLOSED'); ?>
							</span>
							<?php
						break;
					}
					?>
				</td>
			</tr>
				<?php
				}
			}
		}
	}
	else
	{
	?>
			<tr>
				<td><?php echo Lang::txt('PLG_COURSES_OFFERINGS_NONE_FOUND'); ?></td>
			</tr>
	<?php
	}
	?>
		</tbody>
	</table>
</div><!-- / .container -->