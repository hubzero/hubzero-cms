<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
<li class="thread thread<?php echo $this->thread->thread;
	echo ($this->active == $this->thread->thread) ? ' active' : '';
	echo ($this->thread->sticky) ? ' stuck' : ''; ?>"
	id="<?php echo $prfx . ($this->thread->parent ? $this->thread->id . '-' : '') . $this->thread->thread; ?>"
	data-thread="<?php echo $this->thread->thread; ?>">
	<?php
		$name = Lang::txt('JANONYMOUS');
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