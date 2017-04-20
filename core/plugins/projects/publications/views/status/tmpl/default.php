<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Build our citation object
if ($this->pub->version->get('doi'))
{
	include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

	$cite = new stdClass();
	$cite->title    = $this->pub->get('title');
	$date = ($this->pub->published()) ? $this->pub->published() : $this->pub->submitted();
	$cite->year     = Date::of($date)->toLocal('Y');
	$cite->location = '';
	$cite->date     = '';
	$cite->type     = '';
	$cite->author   = $this->pub->getUnlinkedContributors();
	$cite->doi      = $this->pub->get('doi');
	$cite->url      = trim(Request::base(), DS) . Route::url($this->pub->link('version'));

	$citation = \Components\Citations\Helpers\Format::formatReference($cite);
}

// Get creator name
$creator = $this->pub->creator('name') . ' (' . $this->pub->creator('username') . ')';

// Version status
$status = $this->pub->getStatusName();
$class  = $this->pub->getStatusCss();

// Is draft ready?
$complete = $this->pub->curation('complete');

$showCitations  = $this->pub->_category->_params->get('show_citations', 1);
$allowUnpublish = $this->pub->_category->_params->get('option_unpublish', 0);

// We also need a citations block
$blockActive   = $this->pub->curation()->blockExists('citations');
$showCitations = $blockActive ? $showCitations : 0;

// Check if publication is within grace period (published status)
$allowArchive  = \Components\Publications\Helpers\Utilities::archiveOn();
$archiveDate   = $this->pub->futureArchivalDate();
$revertAllowed = $this->pub->config('graceperiod');

if ($revertAllowed && $this->pub->accepted())
{
	$monthFrom = Date::of($this->pub->accepted() . '+1 month')->toSql();
	if (strtotime($monthFrom) < Date::toUnix())
	{
		$revertAllowed = 0;
	}
}

?>

<form action="<?php echo Route::url($this->pub->link('edit')); ?>" method="post" id="plg-form" enctype="multipart/form-data">
	<?php echo \Components\Publications\Helpers\Html::showPubTitle( $this->pub, $this->title); ?>

		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" id="projectid" />
			<input type="hidden" name="version" value="<?php echo $this->pub->versionAlias; ?>" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="section" id="section" value="status" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->pub->version->get('id'); ?>" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->isProvisioned() ? 1 : 0; ?>" />
			<?php if ($this->project->isProvisioned()) { ?>
			<input type="hidden" name="task" value="submit" />
			<?php } ?>
		</fieldset>
<?php
	// Draw status bar
	echo $this->pub->_curationModel->drawStatusBar();
?>

<?php if ($this->project->access('content')) { ?>
<div id="pub-body" class="<?php echo $this->pub->versionAlias; ?>">
<?php } ?>
	<div id="pub-editor" class="grid">
		<?php if (!$this->project->access('content')) { ?>
		<div id="c-pane" class="col span12 omega">
			<div class="c-inner draftflow">
		<?php } else { ?>
		<div class="col span6" id="c-selector">
			<div class="c-inner">
		<?php } ?>
				<h4><?php echo $this->pub->title . '<span class="version-title">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' ' . $this->pub->get('version_label') . ' (' . $status . ')</span>'; ?></h4>
				<table class="tbl-panel">
					<tbody>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?>:</td>
							<td class="tbl-input"><span><?php echo $this->pub->title; ?></span></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_LABEL'); ?>:</td>
							<td class="tbl-input"><span <?php if (($this->pub->versionAlias == 'dev' || $this->pub->state == 4) && $this->task != 'edit' && ($this->project->access('content'))) { echo 'id="edit-vlabel" class="pub-edit"'; } ?>><?php echo $this->pub->get('version_label');  ?></span> <?php if ($this->pub->main == 1) { echo '<span id="v-label">(' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_DEFAULT') . ')</span>'; } ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_NUMBER'); ?>:</td>
							<td class="tbl-input"><span><?php echo $this->pub->version_number;  ?></span>&nbsp; &nbsp;<span >[<a href="<?php echo Route::url($this->pub->link('edit') . '&action=versions'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL_VERSIONS'); ?></a>]</span></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED')); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->created('date') .  ' (' . $this->pub->created('timeago') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_AGO') . ')'; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED_BY')); ?>:</td>
							<td class="tbl-input"><?php echo $creator; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT')); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->masterType()->type; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?>:</td>
							<td class="tbl-input">
								<span class="<?php echo $class; ?>"> <?php echo $status; ?></span>
								<?php if ($this->pub->isEmbargoed()) { ?>
								<span class="embargo"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_EMBARGO') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_UNTIL') . ' ' . $this->pub->published('date'); ?></span>
								<?php } ?>
							</td>
						</tr>
						<?php if ($this->pub->version->get('doi')) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOI'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->version->get('doi') ? $this->pub->version->get('doi') : Lang::txt('PLG_PROJECTS_PUBLICATIONS_NA') ; ?>
							<?php if ($this->pub->version->get('doi')) { echo ' <a href="' . $this->pub->config('doi_verify', 'http://data.datacite.org/') . $this->pub->version->get('doi') . '" rel="external">[&rarr;]</a>'; } ?>
							</td>
						</tr>
						<?php } if ($this->pub->submitted())  { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMITTED'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->submitted('date'); ?></td>
						</tr>
						<?php }  if ($this->pub->accepted()) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ACCEPTED'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->accepted('date') . ' (' . $this->pub->accepted('timeago') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_AGO') . ')'; ?></td>
						</tr>
						<?php } if ($this->pub->published()) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_RELEASE_DATE'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->published('date'); ?></td>
						</tr>
						<?php } if ($this->pub->archived()) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARCHIVED'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->archived('date'); ?></td>
						</tr>
						<?php } if ($this->pub->isUnpublished() || $this->pub->isDown()) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_UNPUBLISHED')); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->unpublished('date') . ' (' . $this->pub->unpublished('timeago') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_AGO') . ')'; ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_URL'); ?>:</td>
							<td class="tbl-input"><a href="<?php echo Route::url($this->pub->link('version')); ?>"><?php echo trim(Request::base(), DS) . Route::url($this->pub->link('version')); ?></a></td>
						</tr>
					</tbody>
				</table>

				<?php if ($this->pub->isDev() && $this->project->access('content')) { ?>
					<p class="c-instruct "><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_HINT_LABEL'); ?></p>
				<?php } ?>
			</div>
		</div>
		<?php if ($this->project->access('content')) { ?>
		<div class="col span6 omega" id="c-output">
			<div class="c-inner">
				<h4>
				<?php if ($this->pub->isDev() || $this->pub->isPending()) { ?>
					<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT'); ?>
				<?php } else { ?>
					<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_YOUR_OPTIONS'); ?>
				<?php } ?>
				</h4>
				<ul class="next-options">
				<?php
					switch ($this->pub->version->get('state'))
					{
						// Unpublished
						case 0:
							// Check who unpublished this
							$pubtitle = \Hubzero\Utility\String::truncate($this->escape($this->pub->version->get('title')), 100);
							$activity = Lang::txt('PLG_PROJECTS_PUBLICATIONS_ACTIVITY_UNPUBLISHED');
							$activity.= ' ' . strtolower(Lang::txt('version'))
										. ' ' . $this->pub->versionAlias
										. ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_OF')
										. ' ' . strtolower(Lang::txt('publication'))
										. ' "' . $pubtitle . '"';

							$admin = $this->project->table('Activity')->checkActivity( $this->project->get('id'), $activity);
							 if ($admin != 1) { ?>
							<li><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISHED_PUBLISH'); ?></p>
								<?php echo ' <a href="' . Route::url($this->pub->link('edit') . '&action=newversion&ajax=1') . '" class="showinbox btn icon-add">'
							. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION').'</a> ' ; ?></li>
							<?php } ?>
							<?php if ($admin == 1) { ?>
							<li id="next-question"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISHED_BY_ADMIN');  ?></p></li>
							<?php }
						break;

						// Published
						case 1: ?>
							<?php if ($allowUnpublish) { ?>
							<li id="next-cancel"><p>
							<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISHED_UNPUBLISH') .
							' <a href="' . Route::url($this->pub->link('editversion') . '&action=cancel') . '">'
							. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISH_VERSION') . ' &raquo;</a> ';  ?></p></li>
							<?php } ?>
							<li id="next-usage"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_WATCH_STATS')
							. ' <strong>' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_USAGE_STATS') . '</strong> '
							. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_FOLLOW_FEEDBACK');  ?>
								<span class="block italic"><a href="<?php echo Route::url($this->pub->link('editversion') . '&action=stats'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_USAGE'); ?> &raquo;</a></span></p></li>
							<?php if ($showCitations) { ?>
							<li id="next-citation"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_WATCH_ADD_CITATIONS');  ?>
								<span class="block italic"><a href="<?php echo Route::url($this->pub->link('editversion') . '&section=citations'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ADD_CITATIONS'); ?> &raquo;</a></span></p></li>
							<?php } ?>
							<?php if ($this->pub->archived()) { echo '<li id="next-archive"><p class="info">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARCHIVED_ON') . ' <strong class="highlighted">' . $this->pub->archived('date') . '</strong>. ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARCHIVED_NO_CHANGE') . '</p></li>'; } ?>
							<?php if ($this->pub->versionProperty('version_label', 'dev') && $this->pub->versionProperty('version_label', 'dev') != $this->pub->versionAlias) { ?>
							<li id="next-draft"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_VERSION_STARTED')
							. ' (<strong>v.'
							. $this->pub->versionProperty('version_label', 'dev') . '</strong>)  <span class="block"><a href="'
							. Route::url($this->pub->link('edit') .'&version=dev') . '">'
							. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION_CONTINUE') . '</a></span>';  ?></p></li>
							<?php } else if (!$this->pub->versionProperty('version_label', 'dev')) {
							?>
							<li id="next-edit">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CHANGES_NEEDED_OPTION'); if ($revertAllowed) { echo ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_GRACE_PERIOD'); } ?>
								<?php if ($revertAllowed && $allowArchive && $archiveDate) { echo '<p class="info">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WILL_BE_ARCHIVED') . ' <strong class="highlighted">' . Date::of($archiveDate)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . '</strong>, ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WILL_BE_ARCHIVED_NO_CHANGE') . '</p>'; } ?>
								<span class="revert-options">
								<?php if ($revertAllowed)
								{
									echo ' <a href="' . Route::url($this->pub->link('editversion') . '&action=revert') .'" class="btn icon-revert" id="action-revert">'
								. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_REVERT')
								. '</a> <span class="block and_or">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_OR') . '</span>';
								}
								echo ' <a href="' . Route::url($this->pub->link('edit') . '&action=newversion&ajax=1') . '" class="showinbox btn icon-add">'
							. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION').'</a> ' ;  ?>
								</span>
								</p></li>
							<?php } ?>
						<?php
						break;

						// Kicked back to authors
						case 7:
							if ($complete) { ?>
						<li id="next-publish"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_RESUBMIT');  ?></p>
							<p class="centeralign"><a href="<?php echo Route::url($this->pub->link('editversion') . '&action=review'); ?>" class="btn btn-success active"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_RESUBMIT_TO_PUBLISH_REVIEW'); ?></a></p></li>
						<?php } else { ?>
							<li id="next-edit">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DRAFT_INCOMPLETE_RESUBMIT'); ?></p>
								<p class="next-controls"><a href="<?php echo Route::url( $this->pub->link('editversion') . '&action=continue' ); ?>" id="start-curation" class="btn btn-primary active icon-next"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MAKE_CHANGES'); ?></a></p>
							</li>
						<?php }
						break;

						// Pending
						case 5: ?>
						<li id="next-pending">
							<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING');  ?></p>
							<?php if ($this->pub->version->get('doi') && !empty($citation)) {
								echo '<p>' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING_DOI_ISSUED') . '</p>'
								. '<div class="citeit">' . $citation . '</div>'; } ?>
						</li>
						<?php
						break;

						// Draft
						case 3:
						default: ?>
						<?php if ($complete) { ?>
						<li id="next-publish"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISH_READY');  ?></p>
							<p class="centeralign"><a href="<?php echo Route::url($this->pub->link('editversion') . '&action=review'); ?>" class="btn btn-success active"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_SUBMIT_TO_PUBLISH_REVIEW'); ?></a></p></li>
						<?php } else { ?>
							<li id="next-edit">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DRAFT_INCOMPLETE_CURATE'); ?></p>
								<p class="next-controls"><a href="<?php echo Route::url( $this->pub->link('editversion') . '&action=continue'); ?>" id="start-curation" class="btn btn-primary active icon-next"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE_DRAFT'); ?></a></p>
							</li>
						<?php } ?>

						<li id="next-cancel"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEED_TO_CANCEL') . ' <a href="' . Route::url($this->pub->link('editversion') . '&action=cancel') . '">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL') . '</a> ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL_BEFORE');  ?></p></li>
				<?php break;
					}
				?>
				</ul>
			</div>
		</div>
	</div>
	<?php } ?>
 </div>
</form>
<?php // We need this to make CKEditor checker work ?>
<script>
jQuery(document).ready(function($) {
	HUB.ProjectPublicationsDraft.initialize();
});
</script>
