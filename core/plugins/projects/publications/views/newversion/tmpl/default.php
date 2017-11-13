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

// Get publication properties
$typetitle = \Components\Publications\Helpers\Html::writePubCategory($this->pub->category()->alias, $this->pub->category()->name);

// Suggest new label
$suggested = is_numeric($this->pub->version_label) ? number_format(($this->pub->version_label + 1.0), 1, '.', '') : '';

?>

<?php if ($this->ajax) { ?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_PROVIDE_LABEL'); ?></h3>
<?php } ?>
<?php
// Display error message
if ($this->getError()) {
	echo '<p class="error">' . $this->getError() . '</p>';
} ?>

<?php if (!$this->ajax) { ?>
<form action="<?php echo Route::url($this->project->link('publications')); ?>" method="post" id="plg-form" >
	<div id="plg-header">
	<?php if ($this->project->isProvisioned()) { ?>
		<h3 class="prov-header"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <a href="<?php echo Route::url($this->pub->link('editversion')); ?>">"<?php echo $this->pub->title; ?>"</a> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION')); ?></h3>
	<?php } else { ?>
		<h3 class="publications"><a href="<?php echo Route::url($this->project->link('publications')); ?>"><?php echo $this->title; ?></a> &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo Route::url($this->pub->link('editversion')); ?>">"<?php echo $this->pub->title; ?>"</a></span> <span class="indlist"> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION')); ?></span>
		</h3>
	<?php } ?>
	</div>
	<h4><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_PROVIDE_LABEL'); ?></h4>
<?php } else { ?>
<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->pub->link('editversion')); ?>">
<?php } ?>
	<fieldset>
		<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" id="projectid" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="savenew" />
		<input type="hidden" name="option" value="<?php echo $this->project->isProvisioned() ? 'com_publications' : $this->option; ?>" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
		<input type="hidden" name="selected_version" value="<?php echo $this->selected_version; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->isProvisioned() ? 1 : 0; ?>" />
		<?php if ($this->project->isProvisioned()) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
	</fieldset>
	<div <?php if (!$this->ajax) { echo 'class="vform"'; } ?>>
		<p>
			<span class="faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PREVIOUS_LABEL')); ?></span> <?php echo $this->pub->version_label; ?>
		</p>
		<label>
			<span class="faded block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_LABEL'); ?></span>
			<input type="text" name="version_label" value="<?php echo $suggested; ?>" />
		</label>
	</div>
		<p class="submitarea">
			<input type="submit" class="btn active btn-success" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_NEW_VERSION'); ?>" />
				<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('COM_PROJECTS_CANCEL'); ?>" />
				<?php } else {
					$rtn = Request::getVar('HTTP_REFERER', Route::url($this->pub->link('editversion')), 'server');
				?>
				<span class="btn btncancel"><a href="<?php echo $rtn; ?>"><?php echo Lang::txt('COM_PROJECTS_CANCEL'); ?></a></span>
				<?php } ?>
		</p>
</form>
<?php if ($this->ajax) { ?>
</div>
<?php }