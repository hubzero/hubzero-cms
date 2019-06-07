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
	->css('jquery.fancybox.css', 'system')
	->css('edit')
	->js('setup');

$privacy = !$this->model->isPublic() ? Lang::txt('COM_PROJECTS_PRIVATE') : Lang::txt('COM_PROJECTS_PUBLIC');

// Get layout from project params or component
$layout = $this->model->params->get('layout', $this->config->get('layout', 'standard'));
$theme  = $this->model->params->get('theme', $this->config->get('theme', 'light'));

if ($layout == 'extended'):
	// Include extended CSS
	$this->css('extended.css');

	// Include theme CSS
	$this->css('theme' . $theme . '.css');
else:
	$this->css('standard.css');
endif;

?>
<div id="project-wrap" class="edit-project">
	<?php if ($layout == 'extended'):
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
	else:
		$this->view('_header', 'projects')
		     ->set('model', $this->model)
		     ->set('showPic', 1)
		     ->set('showPrivacy', 0)
		     ->set('goBack', 1)
		     ->set('showUnderline', 1)
		     ->set('option', $this->option)
		     ->display();
	endif;

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

					<div class="tips">
						<h3><?php echo Lang::txt('COM_PROJECTS_TIPS'); ?></h3>
						<?php if ($this->section == 'team'): ?>
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
							<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_REVIEWER')); ?> </span><?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
							<ul>
								<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_REVIEWER_CAN_ONE'); ?></li>
							</ul>
						<?php endif; ?>

						<?php if ($this->section == 'settings'): ?>
							<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE'); ?></h4>
							<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE_EXPLAIN'); ?></p>
							<?php if ($this->config->get('grantinfo', 0)): ?>
								<h5><?php echo Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_WHY'); ?></h5>
								<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_BECAUSE'); ?></p>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ($this->section == 'info' || $this->section == 'info_custom'): ?>
							<p>
								<?php echo Lang::txt('COM_PROJECTS_CANCEL_PROJECT_NEED'); ?>
							</p>
							<p>
								<a class="btn btn-danger" href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=delete'); ?>" id="delproject"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
							</p>
						<?php endif; ?>
					</div>
				</div><!-- / .col span3 -->
				<div id="edit-project" class="col span9 omega">
					<form id="hubForm" class="full" method="post" action="<?php echo Route::url($this->model->link() . '&task=save'); ?>">
						<input type="hidden" id="pid" name="id" value="<?php echo $this->model->get('id'); ?>" />
						<input type="hidden" name="task" value="save" />
						<input type="hidden" name="active" value="<?php echo $this->section; ?>" />
						<input type="hidden" name="name" value="<?php echo $this->model->get('alias'); ?>" />
						<?php echo Html::input('token'); ?>
						<?php echo Html::input('honeypot'); ?>
						<?php
						switch ($this->section):
							case 'team':
								$this->view('_edit_team')
									->set('content', $this->content)
									->set('model', $this->model)
									->display();
								break;

							case 'info_custom':
							case 'info':
							default:
								$fields = (isset($this->fields)) ? $this->fields : null;
								$data = (isset($this->data)) ? $this->data : array();

								$this->view('_edit_info')
									->set('config', $this->config)
									->set('model', $this->model)
									->set('option', $this->option)
									->set('privacy', $privacy)
									->set('publishing', $this->publishing)
									->set('fields', $fields)
									->set('data', $data)
									->display();
								break;
						endswitch;
						?>
					</form>
				</div><!-- / .col span9 omega -->
			</div><!-- / .grid -->
		</section><!-- / .main section -->
	</div><!-- / #edit-project-content -->
<?php if ($layout == 'extended'): ?>
</div><!-- / .project-inner-wrap -->
<?php endif; ?>
</div>
