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

?>

<h4><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_SETTINGS')); ?></h4>

<h5 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_ACCESS'); ?></h5>

<label>
	<input class="option" name="private" type="radio" value="1" <?php if (!$this->model->isPublic()) { echo 'checked="checked"'; }?> />
	<?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PRIVATE'); ?>
</label>

<label>
	<input class="option" name="private" type="radio" value="0" <?php if ($this->model->isPublic()) { echo 'checked="checked"'; }?> />
	<?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PUBLIC'); ?>
</label>

<?php if ($this->model->isPublic()): ?>
	<h5 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_OPTIONS_FOR_PUBLIC'); ?></h5>
	<p class="hint">
		<?php echo Lang::txt('COM_PROJECTS_YOUR_PROJECT_IS'); ?>
		<span class="prominent urgency"><?php echo $privacy; ?></span>
	</p>
	<label for="params-allow_membershiprequest">
		<input type="hidden"  name="params[allow_membershiprequest]" value="0" />
		<input type="checkbox" class="option" name="params[allow_membershiprequest]" id="params-allow_membershiprequest" value="1" <?php if ($this->model->params->get('allow_membershiprequest')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_MEMBERSHIPREQUEST'); ?>
	</label>
	<label>
		<input type="hidden"  name="params[team_public]" value="0" />
		<input type="checkbox" class="option" name="params[team_public]" value="1" <?php if ($this->model->params->get( 'team_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_TEAM_PUBLIC'); ?>
	</label>

	<?php if ($this->publishing): ?>
		<label>
			<input type="hidden"  name="params[publications_public]" value="0" />
			<input type="checkbox" class="option" name="params[publications_public]" value="1" <?php if ($this->model->params->get( 'publications_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS_PUBLIC'); ?>
		</label>
	<?php endif; ?>

	<?php
		$pparams = Plugin::params( 'projects', 'notes' );
		if ($pparams->get('enable_publinks')): ?>
			<label>
				<input type="hidden"  name="params[notes_public]" value="0" />
				<input type="checkbox" class="option" name="params[notes_public]" value="1" <?php if ($this->model->params->get( 'notes_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_NOTES_PUBLIC'); ?>
			</label>
	<?php endif; ?>

	<?php
		$pparams = Plugin::params( 'projects', 'files' );
		if ($pparams->get('enable_publinks')): ?>
			<label>
				<input type="hidden"  name="params[files_public]" value="0" />
				<input type="checkbox" class="option" name="params[files_public]" value="1" <?php if ($this->model->params->get( 'files_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_FILES_PUBLIC'); ?>
			</label>
	<?php endif; ?>
<?php endif; ?>

<?php
if ($this->config->get('grantinfo', 0))
{
	$this->view('_edit_grant_info')
		->set('model', $this->model)
		->display();
}
?>

<p class="submitarea">
	<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_CHANGES'); ?>" />
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" class="btn btn-cancel">
			<?php echo Lang::txt('COM_PROJECTS_CANCEL'); ?>
		</a>
</p>

