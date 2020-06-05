<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$this->view('submenu', 'partials')
	->set('group', $this->group)
	->set('projectcount', $this->projectcount)
	->set('newcount', $this->newcount)
	->set('tab', 'updates')
	->display();
?>

<section class="main section" id="s-projects">
	<?php
	if ($this->content && in_array(User::get('id'), $this->group->get('managers')))
	{
		// @TODO  Move this to plg_projects_feed?
		?>
		<div id="blab" class="miniblog">
			<form id="blogForm" method="post" class="focused" action="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=projects'); ?>">
				<fieldset>
					<input type="hidden" name="option" value="com_groups" />
					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="active" value="projects" />
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="managers_only" value="0" />
					<?php echo Html::input('token'); ?>

					<?php echo $this->editor('blogentry', '', 5, 3, 'blogentry', array('class' => 'minimal no-footer')); ?>

					<div class="grid">
						<div class="col span6">
							<label for="projectid">
								<?php echo Lang::txt('Post to:'); ?>
								<select name="projectid" name="projectid">
									<option value="0"><?php echo Lang::txt('PLG_GROUPS_PROJECTS_ALL'); ?></option>
									<?php foreach ($this->projects as $project) { ?>
										<option value="<?php echo $project; ?>"><?php
										$p = new Components\Projects\Models\Project($project);
										echo $p->get('title');
										?></option>
									<?php } ?>
								</select>
							</label>
						</div>
						<div class="col span6 omega">
							<p id="blog-submitarea">
								<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_PROJECTS_SHARE'); ?>" id="blog-submit" class="btn" />
							</p>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	<?php } ?>

	<div id="project-updates">
		<?php echo $this->content; ?>
	</div>
</section>
