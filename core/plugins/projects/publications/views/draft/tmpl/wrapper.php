<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

// Are we in draft flow?
$move = ($this->showControls) ? 'continue' : '';

$title 	 = $move && $this->manifest->draftHeading ? $this->manifest->draftHeading : $this->manifest->title;
$tagline = isset($this->manifest->draftTagline) ? $this->manifest->draftTagline : null;

$activeEl = isset($this->master->props['showElement'])
			? $this->master->props['showElement'] : 0;
$element =  Request::getInt( 'el', $activeEl );

$isFirst = $this->pub->curation()->getFirstBlock() == $this->step ? true : false;

?>
<div id="pub-editor" class="pane-desc">
	<form action="<?php echo Route::url($this->pub->link('edit')); ?>" method="post" id="plg-form" enctype="multipart/form-data">
	 	 <fieldset>
			<input type="hidden" name="id" value="<?php echo $this->pub->_project->get('id'); ?>" id="projectid" />
			<input type="hidden" name="version" id="version" value="<?php echo $this->pub->versionAlias; ?>" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" id="action" value="save" />
			<input type="hidden" name="complete" id="complete" value="<?php echo $complete; ?>" />
			<input type="hidden" name="required" id="required" value="<?php echo $required; ?>" />
			<input type="hidden" name="selections" id="selections" value="" />
			<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
			<input type="hidden" name="element" id="element" value="<?php echo $element; ?>" />
			<input type="hidden" name="next" id="next" value="" />
			<input type="hidden" name="step" id="step" value="<?php echo $this->step; ?>" />
			<input type="hidden" name="move" id="move" value="<?php echo $move; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->get('id'); ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->pub->get('version_id'); ?>" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->pub->_project->isProvisioned() ? 1 : 0; ?>" />
		 </fieldset>
  		<div id="c-pane" class="columns">
			 <div class="c-inner draftflow">
						<h4><?php echo $title; ?></h4>
						<?php
							if ($tagline && $move)
							{ ?>
							<h5><?php echo $tagline; ?> <?php if ($this->manifest->about && !$this->pub->_project->isProvisioned()) { ?><a class="pub-info-pop more-content" href="#info-panel" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CLICK_TO_LEARN_MORE'); ?>">&nbsp;</a> <?php } ?></h5>
						<?php }
						?>
						<?php echo $this->content; ?>
						<div class="hidden">
							<div id="info-panel" class="full-content"><?php echo $this->manifest->about; ?></div>
						</div>

						<?php
						if ($this->active != 'review') { ?>
						<div class="submit-area <?php echo ($this->showControls == 2 || $this->showControls == 4) ? ' extended' : ''; ?>" id="submit-area">
							<?php if (!$isFirst && $this->showControls && $this->showControls != 3) { ?>
								<span class="button-wrapper bw-previous icon-prev">
									<input type="button" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GO_PREVIOUS'); ?>" id="c-previous" class="submitbutton btn icon-prev" />
								</span>
							<?php } ?>
							<?php if ($this->showControls == 4 || $this->showControls == 1) { ?>
							<span class="button-wrapper icon-apply">
								<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_APPLY_CHANGES'); ?>" id="c-apply" class="submitbutton btn icon-apply" />
							</span>
							<?php } ?>
							<?php if ($this->showControls && $this->showControls != 3) { ?>
							<span class="button-wrapper icon-next">
								<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GO_NEXT'); ?>" id="c-next" class="submitbutton btn icon-next" />
							</span>
							<?php } ?>
						</div>
					<?php } ?>
			 </div>
		</div>
	</form>
</div>

<div class="hidden">
	<div id="addnotice" class="addnotice">
		<form id="notice-form" name="noticeForm" action="<?php echo Route::url($this->pub->link('edit')); ?>" method="post">
		 <fieldset>
			<input type="hidden" name="pid" value="<?php echo $this->pub->get('id'); ?>" />
			<input type="hidden" name="version" value="<?php echo $this->pub->version->get('version_number'); ?>" />
			<input type="hidden" name="p" id="props" value="" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="dispute" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_TITLE'); ?></h5>
			<div class="form-group">
				<label>
					<span class="block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_LABEL'); ?></span>
					<textarea name="review" id="notice-review" rows="5" cols="10"></textarea>
				</label>
			</div>
			</fieldset>
			<p class="submitarea">
				<input type="submit" id="notice-submit" class="btn" value="<?php echo Lang::txt('COM_PUBLICATIONS_SAVE'); ?>" />
			</p>
		</form>
	</div>
</div>

<div class="hidden">
	<div id="skip-notice" class="addnotice">
		<form id="skip-notice-form" name="skipForm" action="<?php echo Route::url($this->pub->link('edit')); ?>" method="post">
		 <fieldset>
			<input type="hidden" name="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="version" value="<?php echo $this->pub->version_number; ?>" />
			<input type="hidden" name="p" id="skip-props" value="" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="skip" />
			<h5><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_SKIP_TITLE'); ?></h5>
			<label>
				<span class="block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_SKIP_LABEL'); ?></span>
				<textarea name="review" id="skip-notice-review" rows="5" cols="10"></textarea>
			</label>
			</fieldset>
			<p class="submitarea">
				<input type="submit" id="skip-notice-submit" class="btn" value="<?php echo Lang::txt('COM_PUBLICATIONS_SAVE'); ?>" />
			</p>
		</form>
	</div>
</div>
