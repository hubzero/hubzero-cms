<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get publication properties
$typetitle = \Components\Publications\Helpers\Html::writePubCategory($this->pub->cat_alias, $this->pub->cat_name);

// Suggest new label
$suggested = is_numeric($this->pub->version_label) ? number_format(($this->pub->version_label + 1.0), 1, '.', '') : '';

?>
<div id="abox-content">
<?php if ($this->ajax) { ?>
<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE_FOR_NEXT_PUB'); ?></h3>
<?php } ?>
<?php
// Display error  message
if ($this->getError()) {
	echo '<p class="error">' . $this->getError() . '</p>';
} ?>

<?php if (!$this->ajax) { ?>
<form action="<?php echo Route::url($this->pub->link('editbase')); ?>" method="post" id="plg-form" >
	<div id="plg-header">
	<?php if ($this->project->isProvisioned()) { ?>
		<h3 class="prov-header"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE')); ?></h3>
	<?php } else { ?>
		<h3 class="publications"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo $this->title; ?></a><span class="indlist"> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE')); ?></span>
		</h3>
	<?php } ?>
	</div>
	<h4><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE_FOR_NEXT_PUB'); ?></h4>
<?php } else { ?>
<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->pub->link('edit')); ?>">
<?php } ?>
	<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE_HOW'); ?></p>
	<fieldset>
		<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" id="projectid" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="save_license" />
		<input type="hidden" name="option" value="<?php echo $this->project->isProvisioned() ? 'com_publications' : $this->option; ?>" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
		<input type="hidden" name="version" id="version" value="<?php echo $this->pub->versionAlias; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->isProvisioned() ? 1 : 0; ?>" />
		<?php if ($this->project->isProvisioned()) { ?>
			<input type="hidden" name="task" value="submit" />
		<?php } ?>
	</fieldset>
	<div <?php if (!$this->ajax) { echo 'class="vform"'; } ?>>
		<label class="a-label">
			<span class="faded block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_TITLE'); ?></span>
			<input type="text" name="license_title"  class="long" value="" />
		</label>
		<label class="a-label">
			<span class="faded block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_URL'); ?></span>
			<input type="text" name="license_url"  class="long" value="" />
		</label>
		<label class="a-label">
			<span class="faded block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_LICENSE_DETAILS'); ?></span>
			<textarea name="details" id="details" rows="10" cols="50" class="long"></textarea>
		</label>
	</div>
		<p class="submitarea">
			<input type="submit" id="submit-ajaxform" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE'); ?>" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			<?php } else { $rtn = Request::getString('HTTP_REFERER', Route::url($this->pub->link('editversion')), 'server'); ?>
				<a href="<?php echo $rtn; ?>" class="btn btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
			<?php } ?>
		</p>
</form>
</div>