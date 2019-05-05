<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js()
	->js('setup')
	->css('jquery.fancybox.css', 'system');

// Display page title
$this->view('_title')
     ->set('model', $this->model)
     ->set('step', $this->step)
     ->set('option', $this->option)
     ->set('title', $this->title)
     ->display();

?>

<section class="main section" id="setup">
	<?php
	// Display status message
	$this->view('_statusmsg', 'projects')
	     ->set('error', $this->getError())
	     ->set('msg', $this->msg)
	     ->display();

	// Display metadata
	$this->view('_metadata')
	     ->set('model', $this->model)
	     ->set('step', $this->step)
	     ->set('option', $this->option)
	     ->display();

	// Display steps
	$this->view('_steps')
	     ->set('model', $this->model)
	     ->set('step', $this->step)
	     ->display();
	?>
	<div class="clear"></div>
	<div class="setup-wrap">
		<form id="hubForm" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
			<div class="explaination">
				<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_TITLE_ROLES'); ?></h4>
				<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_OWNERS')); ?></span> <?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
				<ul>
					<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_ONE'); ?></li>
					<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_TWO'); ?></li>
					<li><strong><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_THREE'); ?></strong></li>
				</ul>
				<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_COLLABORATORS')); ?></span> <?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
				<ul>
					<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_ONE'); ?></li>
					<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_TWO'); ?></li>
					<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_THREE'); ?></li>
				</ul>
				<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_REVIEWER')); ?></span> <?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
				<ul>
					<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_REVIEWER_CAN_ONE'); ?></li>
				</ul>
				<?php if ($this->model->get('owned_by_group')): ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_GROUP_PROJECT'); ?></h4>
					<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_GROUP_EXPLAIN'); ?></p>
				<?php endif; ?>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_PROJECTS_ADD_TEAM'); ?></legend>
				<?php 
				// Display form fields
				$this->view('_form')
				     ->set('model', $this->model)
				     ->set('step', $this->step)
				     ->set('option', $this->option)
				     ->set('controller', 'setup')
				     ->set('section', $this->section)
				     ->display();
				?>
				<div id="cbody">
					<?php echo $this->content; ?>
				</div>
			</fieldset>
			<div class="clear"></div>
			<div class="submitarea">
				<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>" class="btn btn-success" id="gonext" />
			</div>
		</form>
	</div>
</section>
