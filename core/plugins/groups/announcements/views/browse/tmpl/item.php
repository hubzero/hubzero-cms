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

// Default to unpublished
$class = 'unpublished';

// Is the announcement available?
// Checks that the announcement is:
//   * exists
//   * published (not deleted)
//   * publish up before now
//   * publish down after now
if ($this->announcement->get('state') == 1 && $this->announcement->inPublishWindow())
{
	$class = 'published';
}

// Is it high priority?
if ($this->announcement->get('priority'))
{
	$class .= ' high';
}

// Is it sticky?
if ($this->announcement->get('sticky'))
{
	$class .= ' sticky';
}

//did the user already close this
$closed = Request::getWord('group_announcement_' . $this->announcement->get('id'), '', 'cookie');
if ($closed == 'closed' && $this->showClose == true)
{
	return;
}
?>

<div class="announcement-container <?php echo $class; ?>">
	<?php if (strstr($class, 'unpublished')) : ?>
		<span class="unpublished-message"><?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_NOT_ACTIVE'); ?></span>
	<?php endif; ?>
	<div class="announcement">
		<?php echo $this->announcement->content; ?>
		<dl class="entry-meta">
			<dt class="entry-id">
				<?php echo $this->announcement->get('id'); ?>
			</dt>
		<?php if ($this->authorized == 'manager') : ?>
			<dd class="entry-author">
				<?php
				$profile = $this->announcement->creator();
				echo $this->escape($profile->get('name', Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_UNKNOWN')));
				?>
			</dd>
		<?php endif; ?>
			<dd class="time">
				<time datetime="<?php echo $this->announcement->published(); ?>">
					<?php echo $this->announcement->published('time'); ?>
				</time>
			</dd>
			<dd class="date">
				<time datetime="<?php echo $this->announcement->published(); ?>">
					<?php echo $this->announcement->published('date'); ?>
				</time>
			</dd>
		<?php if ($this->authorized == 'manager' && !$this->showClose) : ?>
			<dd class="entry-options">
				<?php if (User::get('id') == $this->announcement->get('created_by') || $this->authorized == 'manager') : ?>
					<a class="icon-edit edit" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=announcements&action=edit&id=' . $this->announcement->get('id')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_EDIT'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_EDIT'); ?>
					</a>
					<a class="icon-delete delete" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=announcements&action=delete&id=' . $this->announcement->get('id')); ?>" data-confirm="<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_CONFIRM_DELETE'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_DELETE'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_DELETE'); ?>
					</a>
				<?php endif; ?>
			</dd>
		<?php endif; ?>
		</dl>
	<?php if ($this->showClose) : ?>
		<a class="close" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=announcements'); ?>" data-id="<?php echo $this->announcement->get('id'); ?>" data-duration="30" title="<?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_CLOSE_TITLE'); ?>">
			<span><?php echo Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_CLOSE'); ?></span>
		</a>
	<?php endif; ?>
	</div>
</div>