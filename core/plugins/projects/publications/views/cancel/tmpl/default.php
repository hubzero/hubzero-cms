<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get publication properties
$typetitle = \Components\Publications\Helpers\Html::writePubCategory($this->pub->category()->alias, $this->pub->category()->name);
$draft = ($this->pub->state == 3 || $this->pub->state == 4) ? 1 : 0;

$heading = $draft
		? Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH_DRAFT')
		: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH');
$heading.= ' (' . $this->pub->version_label . ')';
$crumbs = $draft
		? Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH_DRAFT_CRUMBS')
		: Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH_CRUMBS');
?>
<div id="abox-content">
<?php if ($this->ajax) { ?>
<h3><?php echo $heading; ?></h3>
<?php } ?>

<?php if (!$this->ajax) { ?>
<form action="<?php echo Route::url($this->pub->link('editversion')); ?>" method="post" id="plg-form" >
	<div id="plg-header">
	<?php if ($this->project->isProvisioned()) { ?>
		<h3 class="prov-header"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <a href="<?php echo Route::url($this->pub->link('editversion')); ?>">"<?php echo $this->pub->title; ?>"</a> &raquo; <?php echo $crumbs; ?></h3>
	<?php } else { ?>
		<h3 class="publications"><a href="<?php echo Route::url($this->pub->link('editbase')); ?>"><?php echo $this->title; ?></a> &raquo; <span class="restype indlist"><?php echo $typetitle; ?></span> <span class="indlist"><a href="<?php echo Route::url($this->pub->link('editversion')); ?>">"<?php echo $this->pub->title; ?>"</a></span> <span class="indlist"> &raquo; <?php echo $crumbs; ?></span>
		</h3>
	<?php } ?>
	</div>
	<h4><?php echo $heading; ?></h4>
<?php } else { ?>
<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->pub->link('editbase')); ?>">
<?php } ?>
	<fieldset>
		
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="cancel" />
		<input type="hidden" name="confirm" value="1" />
		
		<input type="hidden" name="version" value="<?php echo $this->pub->versionAlias; ?>" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->isProvisioned() ? 1 : 0; ?>" />
		<?php if ($this->project->isProvisioned()) { ?>
			<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="task" value="submit" />
			<input type="hidden" name="option" value="com_publications" />
		<?php } else { ?>
			<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" id="projectid" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<?php } ?>
	</fieldset>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } else { ?>
		<?php if ($this->pub->state == 1) { ?>
		<p class="notice">
			<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH_INFO'); ?>
		</p>
		<?php } ?>
		<?php if ($this->pub->state == 3  || $this->pub->state == 4 || ($this->pub->state == 1 && $this->publishedCount == 1)) {
			$warning = Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH_WARNING');
			if ($this->pub->state == 3 || $this->pub->state == 4) {
				$warning = '';
				if ($this->pub->versionCount() == 0) {
					$warning .= Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH_DRAFT_WARNING_SINGLE').' ';
				}
				$warning .= Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_UNPUBLISH_DRAFT_WARNING');
			}
		 ?>
		<p class="warning"><?php echo $warning; ?></p>
		<?php } ?>
		<p><span><input type="submit" value="<?php echo $crumbs; ?>" class="btn btn-success active" /></span><span><a href="<?php echo Route::url($this->pub->link('editversion')); ?>" class="btn btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></a></span></p>
	<?php } ?>
</form>
</div>