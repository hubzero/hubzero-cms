<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$class       = $this->activity->log->details->get('class', 'activity');
$commentable = (!$this->activity->log->get('parent'));
$deletable   = $this->model->access('manager');
$showProject = isset($this->showProject) ? $this->showProject : false;
$edit        = isset($this->edit) ? $this->edit : true;

$creator = User::getInstance($this->activity->log->get('created_by'));

$new = false;
if ($this->model->member())
{
	$new = $this->model->member()->lastvisit && $this->model->member()->lastvisit <= $this->activity->get('created') ? true : false;
}

$recorded = $this->activity->get('created');

$name = Lang::txt('JANONYMOUS');

$online = false;

// Is it not anonymous?
if (!$this->activity->log->get('anonymous'))
{
	// Get their full name
	$name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_PROJECTS_ACTIVITY_UNKNOWN'))));

	// Can we see their profile?
	if (in_array($creator->get('access'), User::getAuthorisedViewLevels()))
	{
		$name = '<a href="' . Route::url($creator->link()) . '">' . $name . '</a>';
	}

	if (isset($this->online) && in_array($this->activity->log->get('created_by'), $this->online))
	{
		$online = true;
	}
}
?>
<div id="li_<?php echo $this->activity->log->get('id'); ?>" class="activity <?php echo $new ? ' newitem' : ''; ?>" data-recorded="<?php echo $recorded; ?>">

	<div class="activity-actor-picture<?php if ($online) { echo ' tooltips" title="' . Lang::txt('PLG_PROJECTS_FEED_ONLINE'); } ?>">
		<?php if ($showProject) { ?>
			<span class="user-img-wrap">
				<img class="project-image" src="<?php echo Route::url($this->model->link('thumb')); ?>" alt="" />
				<?php if ($online) { ?>
					<span class="online"><?php echo Lang::txt('PLG_PROJECTS_FEED_ONLINE'); ?></span>
				<?php } ?>
			</span>
		<?php } else { ?>
			<a class="user-img-wrap" href="<?php echo Route::url($creator->link()); ?>">
				<img src="<?php echo $creator->picture(); ?>" alt="" />
				<?php if ($online) { ?>
					<span class="online"><?php echo Lang::txt('PLG_PROJECTS_FEED_ONLINE'); ?></span>
				<?php } ?>
			</a>
		<?php } ?>
	</div><!-- / .activity-actor-picture -->

	<div class="activity-content <?php echo $this->escape($this->activity->log->get('action')); ?> <?php //echo $this->escape(str_replace('.', '-', $this->activity->log->get('scope'))); ?>">
		<div class="activity-body">
			<div class="activity-details">
				<?php if ($showProject) { ?>
					<span class="project-name">
						<a href="<?php echo Route::url($this->model->link()); ?>"><?php echo \Hubzero\Utility\Str::truncate($this->model->get('title'), 65); ?></a>
					</span>
				<?php } ?>
				<span class="activity-actor"><?php echo $name; ?></span>
				<span class="activity-time"><time datetime="<?php echo Date::of($this->activity->get('created'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo \Components\Projects\Helpers\Html::showTime($this->activity->log->get('created'), true); ?></time></span>
			</div><!-- / .activity-details -->

			<div class="activity-event">
				<div class="activity-event-content <?php echo $class; //if ($a->admin) { echo ' admin-action'; } ?>" id="activity-event-content<?php echo $this->activity->log->get('id'); ?>">
					<?php
					$content = $this->activity->log->get('description');
					$short = null;

					$isHtml = false;
					if (preg_match('/^(<([a-z]+)[^>]*>.+<\/([a-z]+)[^>]*>|<(\?|%|([a-z]+)[^>]*).*(\?|%|)>)/is', $content))
					{
						$isHtml = true;
					}

					if (!$isHtml)
					{
						$content = preg_replace("/\n/", '<br />', trim($content));
					}

					$content = \Components\Projects\Helpers\Html::replaceUrls($content, 'external');
					$content = \Components\Projects\Helpers\Html::replaceEmoIcons($content);
					?>
					<?php if ($this->activity->log->get('scope') == 'project.comment') { ?>
						<span class="activity-action"><?php echo 'said'; //$this->activity->log->get('action'); ?></span>
						<?php
						if (strlen(strip_tags($content)) > 250)
						{
							$short = Hubzero\Utility\Str::truncate($content, 250, array('html' => true));
							?>
							<div class="activity-event-preview">
								<?php echo $short; ?>
								<p>
									<a class="more-content" href="#activity-event-full-content<?php echo $this->activity->log->get('id'); ?>">
										<?php echo Lang::txt('COM_PROJECTS_MORE'); ?>
									</a>
								</p>
							</div>
							<?php
						}
						?>
						<div class="activity-event-content<?php echo ($short) ? ' hide' : ''; ?>" id="activity-event-full-content<?php echo $this->activity->log->get('id'); ?>">
							<?php echo $content; ?>
						</div>
					<?php } else { ?>
						<span id="activity-action">
							<?php echo $content; ?>
						</span>
					<?php } ?>

					<?php
					// Is this a file?
					if ($ref = $this->activity->log->details->get('referenceid'))
					{
						$files = explode(',', $ref);
						$selected  = array();
						$maxHeight = 0;
						$minHeight = 0;
						$minWidth  = 0;
						$maxWidth  = 0;

						$to_path = DS . trim($this->model->config()->get('imagepath', '/site/projects'), DS) . DS . strtolower($this->model->get('alias')) . DS . 'preview';

						foreach ($files as $item)
						{
							$parts = explode(':', $item);
							$file  = count($parts) > 1 ? $parts[1] : $parts[0];
							$hash  = count($parts) > 1 ? $parts[0] : null;

							if ($hash)
							{
								// Only preview mid-size images from now on
								$hashed = md5(basename($file) . '-' . $hash) . '.png';

								if (is_file(PATH_APP . $to_path . DS . $hashed))
								{
									$preview['image'] = $hashed;
									$preview['url']   = null;
									$preview['title'] = basename($file);

									// Get image properties
									list($width, $height, $type, $attr) = getimagesize(PATH_APP . $to_path . DS . $hashed);

									$preview['width'] = $width;
									$preview['height'] = $height;
									$preview['orientation'] = $width > $height ? 'horizontal' : 'vertical';

									// Record min and max width and height to build image grid
									if ($height >= $maxHeight)
									{
										$maxHeight = $height;
									}
									if ($height && $height <= $minHeight)
									{
										$minHeight = $height;
									}
									else
									{
										$minHeight = $height;
									}
									if ($width > $maxWidth)
									{
										$maxWidth = $width;
									}

									$selected[] = $preview;
								}
							}
						}

						if (count($selected))
						{
							// Show preview
							$this->view('files', 'preview')
								->set('maxHeight', $maxHeight)
								->set('maxWidth', $maxWidth)
								->set('minHeight', $minHeight)
								->set('selected', $selected)
								->set('option', $this->option)
								->set('model', $this->model)
								->display();
						}
					}
					?>
				</div>
			</div><!-- / .activity-event -->

			<?php if ($commentable || $deletable || $edit) { ?>
				<div class="activity-options">
					<ul>
						<?php if ($edit && $commentable) { ?>
							<?php if ($this->model->access('content')) { ?>
								<li>
									<a class="icon-reply reply tooltips" href="#commentform_<?php echo $this->activity->log->get('id'); ?>" id="addc_<?php echo $this->activity->log->get('id'); ?>" title="<?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?>" data-inactive="<?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?>" data-active="<?php echo Lang::txt('JCANCEL'); ?>"><!--
										--><?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?><!--
									--></a>
								</li>
							<?php } ?>
						<?php } ?>
						<?php if ($edit && in_array($class, array('blog', 'quote')) && $this->model->access('manager')) { ?>
							<li>
								<a class="icon-edit edit tooltips" data-form="activity-form<?php echo $this->activity->log->get('id'); ?>" data-content="activity-event-content<?php echo $this->activity->log->get('id'); ?>" href="<?php echo Route::url($this->model->link('feed') . '&action=edit&activity=' . $this->activity->log->get('id'));  ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>" data-inactive="<?php echo Lang::txt('JACTION_EDIT'); ?>" data-active="<?php echo Lang::txt('JCANCEL'); ?>"><!--
									--><?php echo Lang::txt('JACTION_EDIT'); ?><!--
								--></a>
							</li>
						<?php } ?>
						<?php if ($deletable) { ?>
							<li>
								<a class="icon-delete delete tooltips" data-confirm="<?php echo Lang::txt('PLG_PROJECTS_BLOG_DELETE_CONFIRMATION'); ?>" href="<?php echo Route::url($this->model->link('feed') . '&action=delete&activity=' . $this->activity->log->get('id'));  ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>"><!--
									--><?php echo Lang::txt('JACTION_DELETE'); ?><!--
								--></a>
							</li>
						<?php } ?>
					</ul>
				</div><!-- / .activity-options -->

				<?php if ($edit && in_array($class, array('blog', 'quote')) && $this->model->access('manager')) { ?>
					<div class="commentform editcomment hidden" id="activity-form<?php echo $this->activity->log->get('id'); ?>">
						<form method="post" action="<?php echo Route::url($this->model->link()); ?>">
							<fieldset>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="task" value="view" />
								<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
								<input type="hidden" name="active" value="feed" />
								<input type="hidden" name="action" value="save" />
								<input type="hidden" name="parent_activity" value="<?php echo $this->activity->log->get('parent'); ?>" />

								<input type="hidden" name="activity" value="<?php echo $this->activity->log->get('id'); ?>" />
								<?php echo Html::input('token'); ?>

								<?php echo $this->editor('comment', $this->activity->log->get('description'), 5, 5, 'comment' . $this->activity->log->get('id'), array('class' => 'minimal no-footer')); ?>

								<p class="blog-submit">
									<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE'); ?>" class="btn c-submit" />
								</p>
							</fieldset>
						</form>
					</div>
				<?php } ?>
			<?php } ?>
		</div><!-- / .activity-body -->

		<?php
		// Add comment
		if ($edit && $this->model->access('content'))
		{
			$this->view('_addcomment')
				->set('option', $this->option)
				->set('model', $this->model)
				->set('activity', $this->activity)
				->display();
		}
		?>
	</div><!-- / .activity-content -->

	<?php
	// Show comments
	$recipient = Hubzero\Activity\Recipient::all();

	$r = $recipient->getTableName();
	$l = Hubzero\Activity\Log::blank()->getTableName();

	$rows = $recipient
		->select($r . '.*')
		->including('log')
		->join($l, $l . '.id', $r . '.log_id')
		->whereEquals($r . '.scope', 'project')
		->whereEquals($r . '.scope_id', $this->model->get('id'))
		->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
		->whereEquals($l . '.parent', $this->activity->log->get('id'))
		->order('id', 'asc')
		->rows();

	if ($rows->count())
	{
		?>
		<ul class="activity-comments" id="comments_<?php echo $this->activity->log->get('id'); ?>">
			<?php
			foreach ($rows as $row)
			{
				$this->view('_activity')
					->set('option', $this->option)
					->set('model', $this->model)
					->set('activity', $row)
					->set('online', $this->online)
					->display();
			}
			?>
		</ul>
		<?php
	}
	?>
	<div id="tail_<?php echo $this->activity->log->get('id'); ?>"></div>
</div><!-- / .activity -->
