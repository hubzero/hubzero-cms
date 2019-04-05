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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
