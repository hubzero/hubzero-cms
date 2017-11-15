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

defined('_HZEXEC_') or die();

$this->css()
     ->js('jquery.infinitescroll', 'com_collections')
     ->js();

$no_html = Request::getInt('no_html', 0);

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn');

// get all sessions
$online = array();
$sessions = Hubzero\Session\Helper::getAllSessions(array(
	'guest'    => 0,
	'distinct' => 1
));
if ($sessions)
{
	// see if any session matches our userid
	foreach ($sessions as $session)
	{
		$online[] = $session->userid;
	}
}

if (!$no_html) {
?>
<div class="activities">
	<form action="<?php echo Route::url($base . '&active=activity'); ?>" method="get">
		<fieldset class="filters">
			<div class="grid">
				<div class="col span6">
					<input type="text" name="q" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_SEARCH_PLACEHOLDER'); ?>" />
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_SEARCH'); ?>" />
				</div>
				<div class="col span6 omega">
					<?php if ($this->filters['filter'] == 'starred') { ?>
						<a class="icon-starred tooltips active" href="<?php echo Route::url($base . '&active=activity'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_ALL'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_ALL'); ?>
						</a>
					<?php } else { ?>
						<a class="icon-starred tooltips" href="<?php echo Route::url($base . '&active=activity&filter=starred'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_STARRED'); ?>">
							<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FILTER_STARRED'); ?>
						</a>
					<?php } ?>
					<?php /*<a class="icon-config tooltips" href="<?php echo Route::url($base . '&active=activity&action=settings'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_SETTINGS'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_SETTINGS'); ?>
					</a>*/ ?>
				</div>
			</div>
		</fieldset>
	</form>

	<?php if ($this->group->published == 1) { ?>
		<form action="<?php echo Route::url($base . '&active=activity'); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<img src="<?php echo User::picture(!User::isGuest() ? 0 : 1); ?>" alt="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_USER_PHOTO'); ?>" />
			</p>

			<fieldset>
				<div class="input-wrap">
					<label for="activity-description">
						<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_COMMENTS'); ?></span>
						<?php echo $this->editor('activity[description]', '', 5, 3, 'activity-description', array('class' => 'minimal no-footer')); ?>
					</label>
				</div>

				<?php if (in_array(User::get('id'), $this->group->get('managers'))) { ?>
					<div class="input-wrap">
						<label for="activity-recipients">
							<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS'); ?></span>
							<select name="activity_recipients" id="activity-recipients">
								<option value="all"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_ALL'); ?></option>
								<option value="managers"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_MANAGERS'); ?></option>
							</select>
						</label>
					</div>
				<?php } ?>

				<div class="input-wrap">
					<label class="upload-label" for="activity-file">
						<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_FILE'); ?></span>
						<input type="file" class="inputfile" name="activity_file" id="activity-file" data-multiple-caption="<?php echo Lang::txt('{count} files selected'); ?>" multiple="multiple" />
					</label>
				</div>

				<p class="submit">
					<?php echo Html::input('token'); ?>
					<input type="hidden" name="option" value="com_groups" />
					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="active" value="activity" />
					<input type="hidden" name="action" value="post" />
					<input type="hidden" name="activity[id]" value="0" />
					<input type="hidden" name="activity[action]" value="created" />
					<input type="hidden" name="activity[scope]" value="activity.comment" />
					<input type="hidden" name="activity[scope_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
					<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_SUBMIT'); ?>" class="btn" />
				</p>
			</fieldset>
		</form>
	<?php } ?>
<?php } ?>

		<?php if ($this->rows->count()) { ?>
			<ul class="activity-feed" data-url="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity'); ?>">
				<?php
				foreach ($this->rows as $row)
				{
					$this->view('default_item')
						->set('group', $this->group)
						->set('row', $row)
						->set('online', $online)
						->display();
				}
				?>
			</ul>
			<form action="<?php echo Route::url($base . '&active=activity'); ?>" method="get">
				<?php
				//echo $this->rows->pagination;
				$pageNav = $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
				$pageNav->setAdditionalUrlParam('active', 'activity');
				if ($this->filters['filter'])
				{
					$pageNav->setAdditionalUrlParam('filter', $this->filters['filter']);
				}
				if ($this->filters['search'])
				{
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
				}
				echo $pageNav;
				?>
			</form>
		<?php } else { ?>
			<div class="results-none">
				<div class="messages">
					<p><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_NO_RESULTS'); ?></p>
				</div>
				<div class="questions">
					<p>
						<strong><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ABOUT_TITLE'); ?></strong><br />
						<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ABOUT'); ?>
					<p>
				</div>
			</div>
		<?php } ?>

<?php if (!$no_html) { ?>
</div>
<?php }
