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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->active = (isset($this->active) ? $this->active : '');

if (!isset($this->instructors) || !is_array($this->instructors))
{
	$this->instructors = array();
	$inst = $this->course->instructors();
	if (count($inst) > 0)
	{
		foreach ($inst as $i)
		{
			$this->instructors[] = $i->get('user_id');
		}
	}
}

$prfx = 'thread';
if (isset($this->prfx))
{
	$prfx = $this->prfx;
}

if ($this->unit)
{
	$this->base .= '&unit=' . $this->unit;
}
if ($this->lecture)
{
	$this->base .= '&b=' . $this->lecture;
}

if (!$this->thread->thread)
{
	$this->thread->thread = $this->thread->id;
}
?>
<li class="thread thread<?php echo $this->thread->thread; if ($this->active == $this->thread->thread) { echo ' active'; } ?><?php echo ($this->thread->sticky) ? ' stuck' : '' ?>" id="<?php echo $prfx . ($this->thread->parent ? $this->thread->id . '-' : '') . $this->thread->thread; ?>" data-thread="<?php echo $this->thread->thread; ?>">
	<?php
		$name = Lang::txt('PLG_COURSES_DISCUSSIONS_ANONYMOUS');
		$huser = '';
		if (!$this->thread->anonymous)
		{
			$name = $this->escape(stripslashes($this->thread->creator->get('name')));
			if (in_array($this->thread->creator->get('access'), User::getAuthorisedviewLevels()))
			{
				$name = '<a href="' . Route::url($this->thread->creator->link()) . '">' . $name . '</a>';
			}
		}

		if ($this->thread->state == 3)
		{
			$comment = '<p class="warning">' . Lang::txt('PLG_COURSES_DISCUSSIONS_CONTENT_REPORTED') . '</p>';
		}
		else
		{
			if ($this->search)
			{
				$this->thread->title = preg_replace('#' . $this->search . '#i', "<span class=\"highlight\">\\0</span>", $this->thread->title);
			}
			$comment = $this->thread->title . ' &hellip;';
		}

		$this->thread->instructor_replied = 0;
		if (count($this->instructors))
		{
			$database = App::get('db');
			$database->setQuery("SELECT COUNT(*) FROM `#__forum_posts` AS c WHERE c.thread=" . $this->thread->thread . " AND c.state=1 AND c.created_by IN (" . implode(',', $this->instructors) . ")");
			$this->thread->instructor_replied = $database->loadResult();
		}
	?>
	<div class="comment-content">
		<p class="sticky-thread" title="<?php echo ($this->thread->sticky) ? Lang::txt('PLG_COURSES_DISCUSSIONS_THREAD_IS_STICKY') : Lang::txt('PLG_COURSES_DISCUSSIONS_THREAD_IS_NOT_STICKY'); ?>">
			<?php echo ($this->thread->sticky) ? Lang::txt('PLG_COURSES_DISCUSSIONS_STICKY') :  Lang::txt('PLG_COURSES_DISCUSSIONS_NOT_STICKY'); ?>
		</p>
		<?php if ($this->thread->instructor_replied) { ?>
			<p class="instructor-commented" title="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_INSTRUCTOR_COMMENTED'); ?>">
				<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_INSTRUCTOR'); ?>
			</p>
		<?php } ?>
		<p class="comment-title">
			<span class="date"><time datetime="<?php echo $this->thread->created; ?>"><?php echo Date::of($this->thread->created)->toLocal(Lang::txt('DATE_FORMAt_HZ1')); ?></time></span>
		</p>
		<p class="comment-body">
			<a href="<?php echo Route::url($this->base  . '&thread=' . $this->thread->id . ($this->search ? '&action=search&search=' . $this->search : '')); ?>"><?php echo $comment; ?></a>
		</p>
		<p class="comment-author">
			<strong><?php echo $name; ?></strong>
		</p>
	</div>
</li>