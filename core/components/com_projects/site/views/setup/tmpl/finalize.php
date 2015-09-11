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
	?>
	<?php
		// Display metadata
		$this->view('_metadata')
		     ->set('model', $this->model)
		     ->set('step', $this->step)
		     ->set('option', $this->option)
		     ->display();
	?>
	<?php
	// Display steps
	$this->view('_steps')
	     ->set('model', $this->model)
	     ->set('step', $this->step)
	     ->display();
	?>
	<div class="clear"></div>
	<div class="setup-wrap">
		<form id="hubForm" method="post" action="index.php">
			<div class="explaination">
				<?php if ($this->config->get('restricted_data', 0)) { ?>
				<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE'); ?></h4>
				<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE_EXPLAIN'); ?> </p>
				<p><?php echo Lang::txt('COM_PROJECTS_SETUP_MORE_ON'); ?><a href="<?php echo $hipaa; ?>" rel="external" > <?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_HIPAA'); ?></a>. <?php echo Lang::txt('COM_PROJECTS_SETUP_MORE_ON'); ?><a href="<?php echo $ferpa; ?>" rel="external" > <?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_FERPA'); ?></a>.</p>
				<p class="info"><?php echo Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_NOTE'); ?></p>
				<?php } else { ?>
					<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_WHY'); ?></h4>
					<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_BECAUSE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></a>.</p>
				<?php } ?>
			</div>
			<fieldset>
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
				<legend><?php echo Lang::txt('COM_PROJECTS_SETUP_BEFORE_COMPLETE'); ?></legend>
				<p class="notice"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_WHY_ASK'); ?></p>
				<?php if ($this->config->get('restricted_data', 0) == 1) { ?>
				<h4 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?><span class="required"><?php echo Lang::txt('REQUIRED'); ?></span></h4>
				<label class="terms-label dark">
					<input class="option restricted-answer" name="restricted" id="restricted-yes" type="radio" value="yes" <?php if ($this->model->params->get('restricted_data') == 'yes') { echo 'checked="checked"'; } ?> />
					<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_YES'); ?>
				</label>
				<div class="ipadded" id="restricted-choice">
					<p class="hint prominent"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_MAKE_CHOICE'); ?></p>
					<label class="terms-label">
						<input class="option restricted-opt" name="export" id="export" type="checkbox" value="yes" <?php if ($this->model->params->get('export_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_EXPORT_CONTROLLED'); ?>
					</label>
					<div id="stop-export" class="stopaction hidden"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_EXPORT'); ?></div>
					<label class="terms-label">
						<input class="option restricted-opt" name="irb" id="irb" type="checkbox" value="yes" <?php if ($this->model->params->get('irb_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_IRB'); ?>
					</label>
					<div id="stop-irb" class="extraaction hidden">
						<h5><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE'); ?></h5>
						<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB'); ?>
						<label>
							<input class="option" name="agree_irb" id="agree_irb" type="checkbox" value="1"  />
							<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB_AGREE'); ?>
						</label>
					</div>
					<label class="terms-label">
						<input class="option restricted-opt" name="hipaa" id="hipaa" type="checkbox" value="yes" <?php if ($this->model->params->get('hipaa_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_HIPAA'); ?>
					</label>
					<div id="stop-hipaa" class="stopaction hidden"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_HIPAA'); ?></div>
					<label class="terms-label">
						<input class="option restricted-opt" name="ferpa" id="ferpa" type="checkbox" value="yes" <?php if ($this->model->params->get('ferpa_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
						<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_FERPA'); ?>
					</label>
					<div id="stop-ferpa" class="extraaction hidden">
						<h5><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE'); ?></h5>
						<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA'); ?>
						<label>
							<input class="option" name="agree_ferpa" id="agree_ferpa" type="checkbox" value="1"  />
							<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA_AGREE'); ?>
						</label>
					</div>
				</div>
				<label class="terms-label dark">
					<input class="option restricted-answer" name="restricted" id="restricted-no" type="radio" value="no" <?php if ($this->model->params->get('restricted_data') == 'no' ) { echo 'checked="checked"'; } ?> />
					<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_NO'); ?>
				</label>

				<?php } ?>
				<?php if ($this->config->get('restricted_data', 0) == 2) { ?>
				<h4 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?><span class="required"><?php echo Lang::txt('REQUIRED'); ?></span></h4>
				<label class="terms-label dark">
					<input class="option" name="restricted" type="checkbox" value="no" />
					<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_SENSITIVE_DATA_AGREE'); ?>
				</label>
				<?php } ?>
			</fieldset>
			<div class="clear"></div>
		<?php if ($this->config->get('grantinfo', 0)) { ?>
			<div class="explaination">
				<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANTINFO_WHY'); ?></h4>
				<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANTINFO_BECAUSE'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO'); ?></legend>
				<p class="notice"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY'); ?></p>
				<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
				 <input name="grant_title" maxlength="250" type="text" value="<?php echo $this->model->params->get('grant_title'); ?>" class="long" />
				</label>
				<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
				 <input name="grant_PI" maxlength="250" type="text" value="<?php echo $this->model->params->get('grant_PI'); ?>" class="long" />
				</label>
				<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
				 <input name="grant_agency" maxlength="250" type="text" value="<?php echo $this->model->params->get('grant_agency'); ?>" class="long" />
				</label>
				<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
				 <input name="grant_budget" maxlength="250" type="text" value="<?php echo $this->model->params->get('grant_budget'); ?>" class="long" />
				</label>
			</fieldset>
			<?php } ?>
			<div class="clear"></div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></legend>
				<label class="terms-label">
					<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></a>? <span class="required"><?php echo Lang::txt('REQUIRED'); ?></span></h4>
					<input class="option" name="agree" type="checkbox" value="1" />
					<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_AGREE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS'); ?></a> <?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE_PROJECT'); ?> <span class="prominent"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_ALL_MEMBERS'); ?></span>.
				</label>
			</fieldset>
			<div class="clear"></div>
			<div class="submitarea">
				<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>" class="btn btn-success" id="btn-finalize" />
			</div>
		<form>
	</div>
</section>