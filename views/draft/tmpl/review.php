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

$this->css('jquery.datepicker.css', 'system')
	 ->css('jquery.timepicker.css', 'system')
	 ->js('jquery.timepicker', 'system');

$complete = $this->pub->curation('complete');
$params   = $this->pub->curation('params');

$elName = "reviewPanel";

$requireDoi   = isset($params->require_doi) ? $params->require_doi : 0;
$showArchival = isset($params->show_archival) ? $params->show_archival : 0;

// Get hub config
$site     = trim(Request::base(), DS);
$sitename = Config::get('sitename');

// Build our citation object
$citation = '';
if ($this->pub->doi)
{
	include_once( PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php' );

	$cite 		 	= new stdClass();
	$cite->title 	= $this->pub->get('title');
	$date 			= ($this->pub->published()) ? $this->pub->published() : $this->pub->submitted();
	$cite->year  	= Date::of($date)->format('Y');
	$cite->location = '';
	$cite->date 	= '';

	$cite->url      = $site . Route::url($this->pub->link('version'));
	$cite->type     = '';
	$cite->author   = $this->pub->getUnlinkedContributors();
	$cite->doi      = $this->pub->get('doi');
	$citation       = \Components\Citations\Helpers\Format::formatReference($cite);
}

// Embargo
$pubdate = Request::getVar('publish_date');

// Get configs
$termsUrl = $this->pub->config()->get('deposit_terms', '');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="draft-review <?php echo $complete ? 'draft-complete' : 'draft-incomplete'; ?> <?php echo $this->pub->state == 7 ? ' draft-resubmit' : ''; ?>">
	<?php if ($complete) { ?>
	<p class="review-prompt"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_COMPLETE'); ?></p>
	<?php } else { ?>
	<p class="review-prompt"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INCOMPLETE'); ?></p>
	<div class="submitarea">
		<a href="<?php echo Route::url( $this->pub->link('editversion') . '&active=publications&action=continue'); ?>" class="btn mini btn-success icon-next"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE_DRAFT'); ?></a>
	</div>
	<?php } ?>

	<?php if ($complete) {
		//Intructions for previewing page
	?>
	<div class="blockelement" id="review-preview">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_PREVIEW'); ?></h5>
				<div class="element-instructions">
					<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_PREVIEW'); ?></p>
					<div class="submitarea">
						<a href="<?php echo Route::url($this->pub->link('version')); ?>" class="btn mini btn-primary active icon-next" rel="external"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?></a>
					</div>
				</div>
			</div>
		</div>
	 </div>

	<?php // File list (archive package)
		if ($showArchival) {
		?>
	<div class="blockelement" id="review-archival">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_ARCHIVE_BUNDLE'); ?></h5>
				<div class="element-instructions">
					<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_ARCHIVE_PACKAGE'); ?></p>
					<div class="list-wrapper">
						<?php echo $this->pub->_curationModel->showPackageContents(); ?>
					</div>
				</div>
			</div>
		</div>
	 </div>
	<?php } ?>
	<?php // DOI block
		if ($requireDoi == 1 || $this->pub->get('doi')) {
	?>
	 <div class="blockelement" id="review-doi">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_TITLE_DOI'); ?></h5>
				<div class="element-instructions">
					<?php if ($citation) { echo '<p>' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_DOI_EXISTS') . '</p><div class="citeit">' . $citation . '</div>'; } else { echo '<p>' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_DOI') . '</p>'; } ?>
				</div>
			</div>
		</div>
	 </div>
	<?php } ?>
	<?php // Select publication date ?>
	<div class="blockelement" id="review-date">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_DATE'); ?></h5>
				<div class="element-instructions">
					<label>
						<span class="review-label"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_PUBLISH_WHEN'); ?>*:</span>
						<input type="text" id="publish_date" name="publish_date" value="<?php echo $pubdate; ?>" placeholder="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_IMMEDIATE'); ?>" />
						<span class="hint block"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_HINT_EMBARGO'); ?></span>
					</label>

					<?php if ($this->pub->submitter())
					{
						// Do we have a submitter choice?
						$submitter  = $this->pub->submitter()->name;
						$submitter .= $this->pub->submitter()->organization ? ', ' . $this->pub->submitter()->organization : '';
						$submitter .= '<input type="hidden" name="submitter" value="' . $this->pub->submitter()->user_id . '" />';
						if ($this->pub->submitter()->user_id != User::get('id'))
						{
							$submitter  = '<select name="submitter">' . "\n";
							$submitter .= '<option value="' . User::get('id') . '" selected="selected">' . User::get('name')
								. '</option>' . "\n";
							$submitter .= '<option value="' . $this->pub->submitter()->user_id . '">' . $this->pub->submitter()->name . '</option>' . "\n";
							$submitter .= '</select>';
						}
						?>
						<label>
							<span class="review-label"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMITTER')); ?>:</span> <?php echo $submitter; ?>
						</label>
					<?php } ?>

					<?php if ($this->pub->config()->get('repository', 1)) { ?>
						<h6><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_PRIMARY_CONTACT'); ?></h6>
						<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_PRIMARY_CONTACT_EXPLANATION'); ?></p>
						<ul class="itemlist" id="author-list">
						<?php foreach ($this->pub->authors() as $author) {
							$org = $author->organization ? $author->organization : $author->p_organization;
							$name = $author->name ? $author->name : $author->p_name;
							$name = trim($name) ? $name : $author->invited_name;
							$name = trim($name) ? $name : $author->invited_email;
							?>
							<li>
								<span class="item-order"><input type="checkbox" name="contact[]" value="<?php echo $this->escape($author->id); ?>" <?php if ($author->repository_contact) { echo ' checked="checked"'; } ?>/></span>
								<span class="item-title"><?php echo $name; ?> <span class="item-subtext"><?php echo $org ? ' - ' . $org : ''; ?></span></span>
							</li>
						<?php } ?>
						</ul>
					<?php } ?>

					<?php if ($requireDoi == 2 && !$this->pub->doi) { 	// Choice of publish/post  ?>
						<h6><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_POST_OR_PUBLISH'); ?></h6>
						<label>
							<input class="option" name="action" type="radio" value="publish" checked="checked" />
							<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_PUBLISH'); ?>
							<span class="hint block ipadded"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_PUBLISH_HINT'); ?></span>
						</label>
						<label>
							<input class="option" name="action" type="radio" value="post" />
							<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_POST'); ?>
							<span class="hint block ipadded"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_POST_HINT'); ?></span>
						</label>
					<?php } else { ?>
						<input type="hidden" name="action" value="publish" />
					<?php } ?>
				</div>
			</div>
		</div>
	 </div>
	<?php // Comments ?>
	<div class="blockelement" id="review-comment">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_TITLE_COMMENT'); ?></h5>
				<div class="element-instructions">
					<label><span class="optional"><?php echo Lang::txt('Optional'); ?></span>
					<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_DESC_COMMENT'); ?>	
						<textarea name="comment" cols="10" rows="5"></textarea>
					</label>
				</div>
			</div>
		</div>
	 </div>
	<?php // Agreements ?>
	<div class="blockelement" id="review-agreement">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_TITLE_AGREE'); ?></h5>
				<div class="element-instructions">
					<label><span class="required"><?php echo Lang::txt('Required'); ?></span>
						<input class="option" name="agree" type="checkbox" value="1" />
						<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_AGREE_TO'); if ($termsUrl) { echo ' <a href="'
						. $termsUrl . '" class="popup">'; } echo
						$sitename . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_TERMS_OF_DEPOSIT'); if ($termsUrl) { echo '</a>.'; }  ?>
					</label>
				</div>
			</div>
		</div>
	 </div>

	<?php // Submission ?>
	<div class="submitarea" id="submit-area">
		<span class="submit-question"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_LOOKING_GOOD'); ?></span>
		<span class="button-wrapper icon-apply">
			<input type="submit" value="<?php echo $this->pub->isWorked() ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_RESUBMIT_DRAFT') : Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMIT_DRAFT'); ?>" id="c-apply" class="submitbutton btn btn-success active icon-apply" />
		</span>
	</div>
	<?php } ?>
	<input type="hidden" name="version" value="<?php echo $this->pub->versionAlias(); ?>" />
	<input type="hidden" name="confirm" value="1" />
</div>
