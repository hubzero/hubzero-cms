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

$approved = ($this->model->params->get('grant_status') == 1) ? 1 : 0;
$hasGrantInfo = false;

if ($this->model->params->get('grant_PI')
|| $this->model->params->get('grant_title')
|| $this->model->params->get('grant_agency')
|| $this->model->params->get('grant_budget')
|| $approved)
{
	$hasGrantInfo = true;
}
?>

<h5 class="terms-question">
	<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO'); ?>
</h5>

<div class="form-group">
	<div class="form-group form-check">
		<label for="grant_info" class="form-check-label">
			<input class="option form-check-input" name="grant_info" id="grant_info-no" type="radio" value="0" <?php if (!$hasGrantInfo) { echo 'checked="checked"'; } ?> />
			<?php echo Lang::txt('JNO'); ?>
		</label>
	</div>
	<div class="form-group form-check">
		<label for="grant_info" class="form-check-label">
			<input class="option form-check-input" name="grant_info" id="grant_info-yes" type="radio" value="1" <?php if ($hasGrantInfo) { echo 'checked="checked"'; } ?> />
			<?php echo Lang::txt('JYES'); ?>
		</label>
	</div>
</div>
<div class="grant_info <?php if (!$hasGrantInfo) { echo 'hidden'; } ?>" id="grant_info_block">
	<?php if ($approved): ?>
		<p class="notice notice_passed">
			<?php echo Lang::txt('COM_PROJECTS_GRANT_APPROVED_WITH_CODE'); ?>
			<span class="prominent">
				<?php echo htmlentities(html_entity_decode($this->model->params->get('grant_approval', 'N/A'))); ?>
			</span>
		</p>
	<?php else: ?>
		<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY'); ?></p>
	<?php endif; ?>

	<div class="form-group">
		<label for="param-grant_title" class="terms-label">
			<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
			<?php if ($approved):  ?>
				<span class="prominent"><?php echo htmlentities(html_entity_decode($this->model->params->get('grant_title', 'N/A'))); ?></span>
			<?php else: ?>
				<input name="params[grant_title]" id="param-grant_title" class="form-control" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get('grant_title'))); ?>" class="long" />
			<?php endif ?>
		</label>
	</div>

	<div class="form-group">
		<label for="param-grant_PI" class="terms-label">
			<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
			<?php if ($approved): ?>
				<span class="prominent"><?php echo htmlentities(html_entity_decode($this->model->params->get('grant_PI', 'N/A'))); ?></span>
			<?php else: ?>
				<input name="params[grant_PI]" id="param-grant_PI"class="form-control"  maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get('grant_PI'))); ?>" class="long"  />
			<?php endif; ?>
		</label>
	</div>

	<div class="form-group">
		<label for="param-grant_agency" class="terms-label">
			<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
			<?php if ($approved): ?>
				<span class="prominent"><?php echo htmlentities(html_entity_decode($this->model->params->get('grant_agency', 'N/A'))); ?></span>
			<?php else: ?>
				<input name="params[grant_agency]" id="param-grant_agency" class="form-control" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get('grant_agency'))); ?>" class="long" />
			<?php endif ?>
		</label>
	</div>

	<div class="form-group">
		<label for="param-grant_budget" class="terms-label">
			<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
			<?php if ($approved): ?>
				<span class="prominent"><?php echo htmlentities(html_entity_decode($this->model->params->get('grant_budget', 'N/A'))); ?></span>
			<?php else: ?>
				<input name="params[grant_budget]" id="param-grant_budget" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get('grant_budget'))); ?>" class="long"  />
			<?php endif; ?>
		</label>
	</div>

	<?php if (!$approved): ?>
		<div class="form-group">
			<div class="form-group form-check">
				<label for="param-grant_status" class="form-check-label">
					<input class="option form-check-input" name="params[grant_status]" id="param-grant_status" type="checkbox" value="0" <?php if ($this->model->params->get('grant_status') == 2) { echo 'checked="checked"'; } ?> />
					<?php echo $this->model->params->get('grant_status') == 2 ? Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_RESUBMIT_FOR_APPROVAL'): Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_NOTIFY_ADMIN'); ?>
				</label>
			</div>
		</div>
	<?php endif; ?>
</div>