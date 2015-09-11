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

$this->css()
     ->js();

$filters = $this->filters;
$filters['count'] = true;

$total = $this->offering->announcements($filters);

$filters['count'] = false;

$rows = $this->offering->announcements($filters);
$manager = $this->offering->access('manage', 'section');

$base = $this->offering->link() . '&active=announcements';
?>
<div class="course_members">
	<h3 class="heading">
		<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS'); ?>
	</h3>

	<form action="<?php echo Route::url($base); ?>" method="post">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_SEARCH_LEGEND'); ?></legend>
				<label for="entry-search-field"><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_SEARCH_LABEL'); ?></label>
				<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_SEARCH_PLACEHOLDER'); ?>"/>
			</fieldset>
		</div><!-- / .container -->

		<?php if ($manager) { ?>
			<p class="btn-container">
				<a class="icon-add add btn" href="<?php echo Route::url($base . '&action=new'); ?>">
					<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_NEW'); ?>
				</a>
			</p>
		<?php } ?>

		<div class="container">
			<?php if ($rows->total() > 0) { ?>
				<?php foreach ($rows as $row) { ?>
					<div class="announcement<?php if ($row->get('priority')) { echo ' high'; } ?>">
						<?php echo $row->content('parsed'); ?>
						<dl class="entry-meta">
							<dt class="entry-id"><?php echo $row->get('id'); ?></dt>
							<?php if ($manager) { ?>
								<dd class="entry-author">
									<?php echo $this->escape(stripslashes($row->creator()->get('name'))); ?>
								</dd>
							<?php } ?>
							<dd class="time">
								<time datetime="<?php echo $row->published(); ?>">
									<?php echo $row->published('time'); ?>
								</time>
							</dd>
							<dd class="date">
								<time datetime="<?php echo $row->published(); ?>">
									<?php echo $row->published('date'); ?>
								</time>
							</dd>
					<?php if ($manager) { ?>
							<dd class="entry-options">
							<?php if (User::get('id') == $row->get('created_by')) { ?>
								<a class="icon-edit edit" href="<?php echo Route::url($base . '&action=edit&entry=' . $row->get('id')); ?>" title="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_EDIT'); ?>">
									<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_EDIT'); ?>
								</a>
								<a class="icon-delete delete" href="<?php echo Route::url($base . '&action=delete&entry=' . $row->get('id')); ?>" data-confirm="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_CONFIRM_DELETE'); ?>" title="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_DELETE'); ?>">
									<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_DELETE'); ?>
								</a>
							<?php } ?>
							</dd>
					<?php } ?>
						</dl>
					</div>
				<?php } ?>
			<?php } else { ?>
					<p><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_NO_RESULTS'); ?></p>
			<?php } ?>

			<?php
			$pageNav = $this->pagination(
				$total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
			$pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
			$pageNav->setAdditionalUrlParam('active', 'announcements');
			echo $pageNav->render();
			?>
			<div class="clearfix"></div>
		</div><!-- / .container -->

		<div class="clear"></div>

		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
		<input type="hidden" name="active" value="announcements" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	</form>
</div><!--/ #course_members -->
