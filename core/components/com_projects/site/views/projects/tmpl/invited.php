<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js();

$html  = '';

$rtrn = Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->option . '&task=' . $this->task), 'server');

?>
<div id="project-wrap">
	<section class="main section">
		<?php
			$this->view('_header')
			     ->set('model', $this->model)
			     ->set('showPic', 1)
			     ->set('showPrivacy', 0)
			     ->set('goBack', 0)
			     ->set('showUnderline', 1)
			     ->set('option', $this->option)
			     ->display();
		?>
		<h3><?php echo Lang::txt('COM_PROJECTS_INVITED_CONFIRM'); ?></h3>
		<div id="confirm-invite" class="invitation">
			<div class="grid">
				<div class="col span6">
					<p>
						<?php echo Lang::txt('COM_PROJECTS_INVITED_CONFIRM_SCREEN') . ' "' . $this->model->get('title') . '". ' . Lang::txt('COM_PROJECTS_INVITED_NEED_ACCOUNT_TO_JOIN'); ?>
					</p>
				</div>
				<div class="col span6 omega">
					<p>
						<?php echo Lang::txt('COM_PROJECTS_INVITED_HAVE_ACCOUNT') . ' <a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)) .  '">' . Lang::txt('COM_PROJECTS_INVITED_PLEASE_LOGIN') . '</a>'; ?>
					</p>
					<p>
						<?php echo Lang::txt('COM_PROJECTS_INVITED_DO_NOT_HAVE_ACCOUNT') . ' <a href="' . Route::url('index.php?option=com_members&controller=register&return=' . base64_encode($rtrn)) .  '">' . Lang::txt('COM_PROJECTS_INVITED_PLEASE_REGISTER') . '</a>'; ?>
					</p>
				</div>
			</div>
		</div>
	</section><!-- / .main section -->
</div>