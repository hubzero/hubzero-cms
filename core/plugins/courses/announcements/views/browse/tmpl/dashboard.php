<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('announcements.dashboard.js');

$rows = $this->offering->announcements(array(
	'limit'     => $this->params->get('display_limit', 1),
	'published' => true
));
?>
	<div class="sub-section announcements">
		<div class="grid">
			<div class="col span-half">
				<h3><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_LATEST'); ?></h3>
				<?php
				if ($rows->total() > 0)
				{
					foreach ($rows as $row)
					{
						?>
						<div class="announcement<?php if ($row->get('priority')) { echo ' high'; } ?>">
							<?php echo $row->content('parsed'); ?>
							<dl class="entry-meta">
								<dt class="entry-id"><?php echo $row->get('id'); ?></dt>
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
							</dl>
						</div><!-- / .announcement -->
						<?php
					}
				}
				else
				{
				?>
					<p><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_NONE_MADE'); ?></p>
				<?php
				}
				?>
			</div><!-- / .col -->

			<div class="col span-half omega">
				<h3><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_NEW'); ?></h3>
				<form action="<?php echo Route::url($this->offering->link() . '&active=announcements'); ?>" method="post" id="announcementForm" class="full">
					<fieldset>
						<legend>
							<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_NEW'); ?>
						</legend>

						<label for="field_content">
							<span class="label-text"><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_REQUIRED'); ?></span></span>
							<?php
							echo $this->editor('fields[content]', '', 35, 3, 'field_content', array('class' => 'minimal no-footer'));
							?>
						</label>

						<label for="field-priority" id="priority-label">
							<input class="option" type="checkbox" name="fields[priority]" id="field-priority" value="1" />
							<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_PRIORITY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_SUBMIT'); ?>" />
						</p>
					</fieldset>
					<div class="clear"></div>

					<input type="hidden" name="fields[id]" value="" />
					<input type="hidden" name="fields[state]" value="1" />
					<input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
					<input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
					<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
					<input type="hidden" name="active" value="announcements" />
					<input type="hidden" name="action" value="save" />

					<?php echo Html::input('token'); ?>
				</form>
			</div><!-- / .col -->
		</div><!-- / .grid -->
	</div><!-- / .sub-section announcements -->