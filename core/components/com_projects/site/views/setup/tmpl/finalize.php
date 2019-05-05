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

$privacylink = $this->config->get('privacylink', '/legal/privacy');
$hipaa       = $this->config->get('HIPAAlink', 'http://www.hhs.gov/ocr/privacy/');
$ferpa       = $this->config->get('FERPAlink', 'http://www2.ed.gov/policy/gen/reg/ferpa/index.html');

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
			<div class="explaination">
				<?php if ($this->config->get('restricted_data', 0)): ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE'); ?></h4>
					<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE_EXPLAIN'); ?></p>
					<p><?php echo Lang::txt('COM_PROJECTS_SETUP_MORE_ON'); ?><a href="<?php echo $hipaa; ?>" rel="external" > <?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_HIPAA'); ?></a>. <?php echo Lang::txt('COM_PROJECTS_SETUP_MORE_ON'); ?><a href="<?php echo $ferpa; ?>" rel="external nofollow"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_FERPA'); ?></a>.</p>
					<p class="info"><?php echo Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_NOTE'); ?></p>
				<?php else: ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_WHY'); ?></h4>
					<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_BECAUSE'); ?> <a href="<?php echo $privacylink; ?>" rel="external nofollow"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></a>.</p>
				<?php endif; ?>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_PROJECTS_SETUP_BEFORE_COMPLETE'); ?></legend>

				<p class="notice"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_WHY_ASK'); ?></p>

				<?php if ($this->config->get('restricted_data', 0) == 1): ?>
					<h4 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?><span class="required"><?php echo Lang::txt('REQUIRED'); ?></span></h4>
					<div class="form-group form-check">
						<label for="restricted-yes" class="terms-label dark form-check-label">
							<input class="option form-check-input restricted-answer" name="restricted" id="restricted-yes" type="radio" value="yes" <?php if ($this->model->params->get('restricted_data') == 'yes') { echo 'checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_YES'); ?>
						</label>
					</div>

					<div class="ipadded" id="restricted-choice">
						<p class="hint prominent"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_MAKE_CHOICE'); ?></p>

						<div class="form-group form-check">
							<label for="export" class="terms-label form-check-label">
								<input class="option form-check-input restricted-opt" name="export" id="export" type="checkbox" value="yes" <?php if ($this->model->params->get('export_data') == 'yes') { echo 'checked="checked"'; } ?> />
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_EXPORT_CONTROLLED'); ?>
							</label>
						</div>
						<div id="stop-export" class="stopaction hidden"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_EXPORT'); ?></div>

						<div class="form-group form-check">
							<label for="irb" class="terms-label form-check-label">
								<input class="option form-check-input restricted-opt" name="irb" id="irb" type="checkbox" value="yes" <?php if ($this->model->params->get('irb_data') == 'yes') { echo 'checked="checked"'; } ?> />
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_IRB'); ?>
							</label>
						</div>
						<div id="stop-irb" class="extraaction hidden">
							<h5><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE'); ?></h5>
							<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB'); ?>
							<div class="form-group form-check">
								<label for="agree_irb" class="form-check-label">
									<input class="option form-check-input" name="agree_irb" id="agree_irb" type="checkbox" value="1"  />
									<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB_AGREE'); ?>
								</label>
							</div>
						</div>

						<div class="form-group form-check">
							<label for="hipaa" class="form-check-label terms-label">
								<input class="option form-check-input restricted-opt" name="hipaa" id="hipaa" type="checkbox" value="yes" <?php if ($this->model->params->get('hipaa_data') == 'yes') { echo 'checked="checked"'; } ?> />
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_HIPAA'); ?>
							</label>
						</div>
						<div id="stop-hipaa" class="stopaction hidden"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_HIPAA'); ?></div>

						<div class="form-group form-check">
							<label for="ferpa" class="form-check-label terms-label">
								<input class="option form-check-input restricted-opt" name="ferpa" id="ferpa" type="checkbox" value="yes" <?php if ($this->model->params->get('ferpa_data') == 'yes') { echo 'checked="checked"'; } ?> />
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_FERPA'); ?>
							</label>
						</div>
						<div id="stop-ferpa" class="extraaction hidden">
							<h5><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE'); ?></h5>
							<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA'); ?>
							<div class="form-group form-check">
								<label for="agree_ferpa" class="form-check-label">
									<input class="option form-check-input" name="agree_ferpa" id="agree_ferpa" type="checkbox" value="1"  />
									<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA_AGREE'); ?>
								</label>
							</div>
						</div>
					</div>

					<div class="form-group form-check">
						<label class="terms-label dark form-check-label">
							<input class="option form-check-input restricted-answer" name="restricted" id="restricted-no" type="radio" value="no" <?php if ($this->model->params->get('restricted_data') == 'no') { echo 'checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_NO'); ?>
						</label>
					</div>
				<?php endif; ?>
				<?php if ($this->config->get('restricted_data', 0) == 2): ?>
					<h4 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?><span class="required"><?php echo Lang::txt('REQUIRED'); ?></span></h4>

					<div class="form-group form-check">
						<label for="restricted" class="terms-label dark form-check-label">
							<input class="option form-check-input" name="restricted" id="restricted" type="checkbox" value="no" />
							<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_SENSITIVE_DATA_AGREE'); ?>
						</label>
					</div>
				<?php endif ?>
			</fieldset>
			<div class="clear"></div>

			<?php if ($this->config->get('grantinfo', 0)): ?>
				<div class="explaination">
					<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANTINFO_WHY'); ?></h4>
					<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANTINFO_BECAUSE'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO'); ?></legend>

					<div class="form-group">
						<div class="form-group form-check">
							<label for="grant_info" class="form-check-label">
								<input class="option form-check-input" name="grant_info" id="grant_info-no" type="radio" value="0" checked="checked" />
								<?php echo Lang::txt('JNO'); ?>
							</label>
						</div>
						<div class="form-group form-check">
							<label for="grant_info" class="form-check-label">
								<input class="option form-check-input" name="grant_info" id="grant_info-yes" type="radio" value="1" />
								<?php echo Lang::txt('JYES'); ?>
							</label>
						</div>
					</div>
					<div class="grant_info hidden" id="grant_info_block">
						<p class="notice"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY'); ?></p>
						<div class="form-group">
							<label class="terms-label">
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
								<input name="grant_title" maxlength="250" type="text" class="form-control" value="<?php echo $this->model->params->get('grant_title'); ?>" class="long" />
							</label>
						</div>
						<div class="form-group">
							<label class="terms-label">
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
								<input name="grant_PI" maxlength="250" type="text" class="form-control" value="<?php echo $this->model->params->get('grant_PI'); ?>" class="long" />
							</label>
						</div>
						<div class="form-group">
							<label class="terms-label">
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
								<input name="grant_agency" maxlength="250" type="text" class="form-control" value="<?php echo $this->model->params->get('grant_agency'); ?>" class="long" />
							</label>
						</div>
						<div class="form-group">
							<label class="terms-label">
								<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
								<input name="grant_budget" maxlength="250" type="text" class="form-control" value="<?php echo $this->model->params->get('grant_budget'); ?>" class="long" />
							</label>
						</div>
					</div>
				</fieldset>
				<div class="clear"></div>
			<?php endif; ?>

			<fieldset>
				<legend><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></legend>

				<div class="form-group form-check">
					<label class="form-check-label terms-label">
						<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></a>? <span class="required"><?php echo Lang::txt('REQUIRED'); ?></span></h4>
						<input class="option form-check-input" name="agree" type="checkbox" value="1" />
						<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_AGREE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></a> <?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE_PROJECT'); ?> <span class="prominent"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_ALL_MEMBERS'); ?></span>.
					</label>
				</div>
			</fieldset>
			<div class="clear"></div>

			<div class="submitarea">
				<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>" class="btn btn-success" id="btn-finalize" />
			</div>
		</form>
	</div>
</section>
