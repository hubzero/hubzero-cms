<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js()
	->css('jquery.fancybox.css', 'system')
	->css('edit')
	->js('setup');

$privacy = !$this->model->isPublic() ? Lang::txt('COM_PROJECTS_PRIVATE') : Lang::txt('COM_PROJECTS_PUBLIC');

// Get layout from project params or component
$layout = $this->model->params->get('layout', $this->config->get('layout', 'standard'));
$theme  = $this->model->params->get('theme', $this->config->get('theme', 'light'));

if ($layout == 'extended')
{
	// Include extended CSS
	$this->css('extended.css');

	// Include theme CSS
	$this->css('theme' . $theme . '.css');
}
else
{
	$this->css('standard.css');
}

?>
<div id="project-wrap" class="edit-project">
	<?php if ($layout == 'extended') {
		// Draw top header
		$this->view('_topheader', 'projects')
		     ->set('model', $this->model)
		     ->set('publicView', false)
		     ->set('option', $this->option)
		     ->display();
		// Draw top menu
		$this->view('_topmenu', 'projects')
		     ->set('model', $this->model)
		     ->set('active', 'edit')
		     ->set('tabs', array())
		     ->set('option', $this->option)
		     ->set('guest', false)
		     ->set('publicView', false)
		     ->display();
	?>
		<div class="project-inner-wrap">
	<?php
	} else {
		$this->view('_header', 'projects')
		     ->set('model', $this->model)
		     ->set('showPic', 1)
		     ->set('showPrivacy', 0)
		     ->set('goBack', 1)
		     ->set('showUnderline', 1)
		     ->set('option', $this->option)
		     ->display(); ?>
	<?php } ?>
	<?php
		// Display status message
		$this->view('_statusmsg', 'projects')
		     ->set('error', $this->getError())
		     ->set('msg', $this->msg)
		     ->display();
	?>

	<div id="edit-project-content">
		<h3 class="edit-title"><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_PROJECT')); ?></h3>
		<section class="main section">
			<div class="grid">
				<div class="col span3">
				<?php
					// Display sections menu
					$this->view('_sections')
					     ->set('sections', $this->sections)
					     ->set('section', $this->section)
					     ->set('option', $this->option)
					     ->set('model', $this->model)
					     ->display();
				?>

				<?php if ($this->section != 'info') { ?>
				<div class="tips">
					<h3><?php echo Lang::txt('COM_PROJECTS_TIPS'); ?></h3>
				<?php if ($this->section == 'team') { ?>
						<h4><?php echo Lang::txt('PLG_PROJECTS_TEAM_HOWTO_ROLES_TIPS'); ?></h4>
						<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_OWNERS')); ?> </span><?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
						<ul>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_ONE'); ?></li>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_TWO'); ?></li>
							<li><strong><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_THREE'); ?></strong></li>
						</ul>
						<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_COLLABORATORS')); ?> </span><?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
						<ul>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_ONE'); ?></li>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_TWO'); ?></li>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_THREE'); ?></li>
						</ul>
						<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_REVIEWER')); ?></span> <?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
						<ul>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_REVIEWER_CAN_ONE'); ?></li>
						</ul>
				<?php }
				 else if ($this->section == 'settings') { ?>
						<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE'); ?></h4>
						<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE_EXPLAIN'); ?></p>
					<?php if ($this->config->get('grantinfo', 0)) { ?>
						<h5><?php echo Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_WHY'); ?></h5>
						<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_BECAUSE'); ?></p>
					<?php } ?>
				<?php } ?>
				</div>
				<?php } ?>
			</div><!-- / .aside -->
			<div id="edit-project" class="col span9 omega">
				<form id="hubForm" class="full" method="post" action="<?php echo Route::url($this->model->link() . '&task=save'); ?>">
					<div>
						<input type="hidden" id="pid" name="id" value="<?php echo $this->model->get('id'); ?>" />
						<input type="hidden" name="task" value="save" />
						<input type="hidden" name="active" value="<?php echo $this->section; ?>" />
						<input type="hidden" name="name" value="<?php echo $this->model->get('alias'); ?>" />
						<?php echo Html::input('token'); ?>
						<?php echo Html::input('honeypot'); ?>
						<?php
							switch ($this->section)
							{
								case 'info':
								default:
									$this->view('_edit_info')
										->set('config', $this->config)
										->set('model', $this->model)
										->set('option', $this->option)
										->set('privacy', $privacy)
										->set('publishing', $this->publishing)
										->display();
									break;
								case 'team':
									$this->view('_edit_team')
										->set('content', $this->content)
										->set('model', $this->model)
										->display();
									break;
						?>
						<?php
							case 'info_custom': ?>
								<fieldset>
									<legend><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO')); ?></legend>

									<label for="field-name">
										<?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?>
										<input type="text" name="name" id="field-name" disabled="disabled" readonly="readonly" class="disabled readonly" value="<?php echo $this->model->get('alias'); ?>" />
									</label>

									<label for="field-title">
										<?php echo Lang::txt('COM_PROJECTS_TITLE'); ?>
										<input name="title" id="field-title" maxlength="250" type="text" value="<?php echo $this->escape($this->model->get('title')); ?>" class="long" />
									</label>

									<label for="field-about">
										<?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></td>
										<?php echo $this->editor('about', $this->escape($this->model->about('raw')), 35, 25, 'about', array('class' => 'minimal no-footer')); ?>
									</label>
								</fieldset>

								<fieldset>
									<legend><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO_EXTENDED')); ?></legend>
									<?php
									// Convert to XML so we can use the Form processor
									$xml = Components\Projects\Models\Orm\Description\Field::toXml($this->fields, 'edit');
									// Create a new form
									Hubzero\Form\Form::addFieldPath(Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'description' . DS. 'fields');

									$form = new Hubzero\Form\Form('description', array('control' => 'description'));
									$form->load($xml);

									$data = new stdClass;
									$data->textbox = 'abd';
									$data->projecttags = 'testing, tagging';

									$form->bind($this->data);

									foreach ($form->getFieldsets() as $fieldset)
									{
										foreach ($form->getFieldset($fieldset->name) as $field)
										{
											echo $field->label;
											echo $field->input;
											echo $field->description;
										}
									}
									?>
								</fieldset>
								<fieldset>
									<?php
									// Display project image upload
									$this->view('_picture')
									     ->set('model', $this->model)
									     ->set('option', $this->option)
									     ->display();
									?>
								</fieldset>
								<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_CHANGES'); ?>"  />
								<?php
							break;
							}
						?>
					</div>
				</form>
				</div><!-- / .omega -->
			</div><!-- / .grid -->
		</section><!-- / .main section -->
	</div><!-- / #edit-project-content -->
<?php if ($layout == 'extended') { ?>
</div><!-- / .project-inner-wrap -->
<?php } ?>
</div>
<?php if ($this->section == 'info') { ?>
	<div id="cancel-project">
		<p class="right_align"><?php echo Lang::txt('Need to cancel project? You have an option to permanently '); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=delete'); ?>" id="delproject"><?php echo strtolower(Lang::txt('delete')); ?></a> <?php echo Lang::txt('your project.'); ?></p>
	</div>
<?php } ?>
