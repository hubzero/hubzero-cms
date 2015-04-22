<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = 'm/d/Y';

$site 	 = Config::get('config.live_site')
	? Config::get('config.live_site')
	: trim(Request::base(), DS);

$now 	= Date::toSql();
$v 		= $this->pub->versionAlias == 'default' ? '' : '?v=' . $this->pub->versionAliasAlias;

// Build our citation object
$citation = '';
if ($this->pub->doi)
{
	include_once( PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php' );

	$cite 		 	= new stdClass();
	$cite->title 	= $this->pub->title;
	$date 			= ($this->pub->published_up && $this->pub->published_up != '0000-00-00 00:00:00')
					? $this->pub->published_up : $this->pub->submitted;
	$cite->year  	= Date::of($date)->format('Y');
	$cite->location = '';
	$cite->date 	= '';

	$cite->url = $site . DS . 'publications' . DS . $this->pub->id . '?v=' . $this->pub->versionAlias_number;
	$cite->type = '';
	$cite->author = $this->pub->getUnlinkedContributors();
	$cite->doi = $this->pub->doi;
	$citation = \Components\Citations\Helpers\Format::formatReference($cite);
}

// Get creator name
$creator = $this->pub->creator('name') . ' (' . $this->pub->creator('username') . ')';

// Version status
$status = \Components\Publications\Helpers\Html::getPubStateProperty($this->pub, 'status');
$class 	= \Components\Publications\Helpers\Html::getPubStateProperty($this->pub, 'class');

$complete 	= $this->pub->_curationModel->_progress->complete;

$showCitations  = $this->pub->_category->_params->get('show_citations', 1);
$allowUnpublish = $this->pub->_category->_params->get('option_unpublish', 0);

// We also need a citations block
$blockActive   = $this->pub->_curationModel->blockExists('citations');
$showCitations = $blockActive ? $showCitations : 0;

// Check if publication is within grace period (published status)
$revertAllowed = $this->pubconfig->get('graceperiod', 0);
if ($revertAllowed && $this->pub->accepted && $this->pub->accepted != '0000-00-00 00:00:00')
{
	$monthFrom = Date::of($this->pub->accepted . '+1 month')->toSql();
	if (strtotime($monthFrom) < Date::toUnix())
	{
		$revertAllowed = 0;
	}
}
$allowArchive = \Components\Publications\Helpers\Utilities::archiveOn();

$archiveDate  = $this->pub->accepted && $this->pub->accepted != '0000-00-00 00:00:00' ? Date::of($this->pub->accepted . '+1 month')->toSql() : NULL;
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

<div id="pub-body" class="<?php echo $this->pub->versionAlias; ?>">
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 	<div class="c-inner">
				<h4><?php echo $this->pub->title . '<span class="version-title">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION')
					. ' ' . $this->pub->get('version_label') . ' (' . $status . ')</span>'; ?>
				</h4>
				<table class="tbl-panel">
					<tbody>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?>:</td>
							<td class="tbl-input"><span><?php echo $this->pub->title; ?></span></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_LABEL'); ?>:</td>
							<td class="tbl-input"><span <?php if (($this->pub->versionAlias == 'dev' || $this->pub->state == 4) && $this->task != 'edit') { echo 'id="edit-vlabel" class="pub-edit"'; } ?>><?php echo $this->pub->get('version_label');  ?></span> <?php if ($this->pub->main == 1) { echo '<span id="v-label">(' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_DEFAULT') . ')</span>'; } ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_NUMBER'); ?>:</td>
							<td class="tbl-input"><span><?php echo $this->pub->version_number;  ?></span>&nbsp; &nbsp;<span >[<a href="<?php echo Route::url($this->pub->link('edit') . '&active=versions'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL_VERSIONS'); ?></a>]</span></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED')); ?>:</td>
							<td class="tbl-input"><?php echo Date::of($this->pub->created)->format($dateFormat) .  ' (' . \Components\Projects\Helpers\Html::timeAgo($this->pub->created) . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_AGO') . ')'; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED_BY')); ?>:</td>
							<td class="tbl-input"><?php echo $creator; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT')); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->_type->type; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?>:</td>
							<td class="tbl-input">
								<span class="<?php echo $class; ?>"> <?php echo $status; ?></span>
								<?php if ($this->pub->published_up > $now ) { ?>
								<span class="embargo"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_EMBARGO') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_UNTIL') . ' ' . Date::of($this->pub->published_up)->format($dateFormat); ?></span>
								<?php } ?>
							</td>
						</tr>
						<?php if ($this->pub->doi) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DOI'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->doi ? $this->pub->doi : Lang::txt('PLG_PROJECTS_PUBLICATIONS_NA') ; ?>
							<?php if ($this->pub->doi) { echo ' <a href="' . $this->pubconfig->get('doi_verify', 'http://data.datacite.org/') . $this->pub->doi . '" rel="external">[&rarr;]</a>'; } ?>
							</td>
						</tr>
						<?php } if ($this->pub->submitted && $this->pub->submitted != '0000-00-00 00:00:00')  { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMITTED'); ?>:</td>
							<td class="tbl-input"><?php echo Date::of($this->pub->submitted)->format($dateFormat); ?></td>
						</tr>
						<?php }  if ($this->pub->accepted && $this->pub->accepted != '0000-00-00 00:00:00') { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ACCEPTED'); ?>:</td>
							<td class="tbl-input"><?php echo Date::of($this->pub->accepted)->format($dateFormat) . ' (' . \Components\Projects\Helpers\Html::timeAgo($this->pub->accepted) . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_AGO') . ')'; ?></td>
						</tr>
						<?php } if ($this->pub->published_up && $this->pub->published_up != '0000-00-00 00:00:00') { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_RELEASE_DATE'); ?>:</td>
							<td class="tbl-input"><?php echo Date::of($this->pub->published_up)->format($dateFormat); ?></td>
						</tr>
						<?php } if ($this->pub->archived && $this->pub->archived != '0000-00-00 00:00:00') { ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARCHIVED'); ?>:</td>
							<td class="tbl-input"><?php echo Date::of($this->pub->archived)->format($dateFormat); ?></td>
						</tr>
						<?php } if ($this->pub->state == 0) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_UNPUBLISHED')); ?>:</td>
							<td class="tbl-input"><?php echo Date::of($this->pub->published_down)->format($dateFormat) . ' (' . \Components\Projects\Helpers\Html::timeAgo($this->pub->published_down) . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_AGO') . ')'; ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td class="tbl-lbl"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_URL'); ?>:</td>
							<td class="tbl-input"><a href="<?php echo Route::url($this->pub->link() . $v); ?>"><?php echo trim($site, DS) . '/publications/' . $this->pub->id . $v; ?></a></td>
						</tr>
					</tbody>
				</table>

				<?php if ($this->pub->versionAlias == 'dev') { ?>
					<p class="c-instruct "><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_HINT_LABEL'); ?></p>
				<?php } ?>
			</div>
		</div>
		<div class="two columns second" id="c-output">
		 	<div class="c-inner">
				<h4>
				<?php if ($this->pub->versionAlias == 'dev' || $this->pub->state == 5) { ?>
					<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT'); ?>
				<?php } else { ?>
					<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_YOUR_OPTIONS'); ?>
				<?php } ?>
				</h4>
				<ul class="next-options">
				<?php
					switch ($this->pub->state)
					{
						// Unpublished
						case 0:
							// Check who unpublished this
							$objAA = new \Components\Projects\Tables\Activity( $this->database );
							$pubtitle = \Hubzero\Utility\String::truncate($this->pub->title, 100);
							$activity = Lang::txt('PLG_PROJECTS_PUBLICATIONS_ACTIVITY_UNPUBLISHED');
							$activity.= ' '.strtolower(Lang::txt('version'))
										. ' ' . $this->pub->versionAlias_label
										. ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_OF')
										. ' ' . strtolower(Lang::txt('publication'))
										. ' "' . $pubtitle . '"';

							$admin = $objAA->checkActivity( $this->project->get('id'), $activity);
							 if ($admin != 1) { ?>
							<li id="next-publish">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISHED_PUBLISH')
							. ' <a href="' . Route::url($this->pub->link('edit') . '&action=republish&amp;version=' . $this->pub->versionAlias) . '">'
							. Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_REPUBLISH') . ' &raquo;</a>';  ?></p>
							</li>
							<?php } ?>
							<?php if ($admin == 1) { ?>
							<li id="next-question"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISHED_BY_ADMIN');  ?></p></li>
							<?php }
						break;

						// Published
						case 1: ?>
							<?php if ($allowUnpublish) { ?>
							<li id="next-cancel"><p>
							<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISHED_UNPUBLISH');
							echo ' <a href="' . Route::url($this->pub->link('edit') . '&action=cancel&version=' . $this->pub->versionAlias) . '">'
							.Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISH_VERSION').' &raquo;</a> ';  ?></p></li>
							<?php } ?>
							<li id="next-usage"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_WATCH_STATS')
							.' <strong>'.Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_USAGE_STATS').'</strong> '
							.Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_FOLLOW_FEEDBACK');  ?>
								<span class="block italic"><a href="<?php echo Route::url($this->pub->link('edit') . '&action=stats&version=' . $this->pub->versionAlias); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_USAGE'); ?> &raquo;</a></span></p></li>
							<?php if ($showCitations) { ?>
							<li id="next-citation"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_WATCH_ADD_CITATIONS');  ?>
								<span class="block italic"><a href="<?php echo Route::url($this->pub->link('edit') . '&section=citations&version=' . $this->pub->versionAlias); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ADD_CITATIONS'); ?> &raquo;</a></span></p></li>
							<?php } ?>
							<?php if ($this->pub->archived && $this->pub->archived != '0000-00-00 00:00:00') { echo '<li id="next-archive"><p class="info">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARCHIVED_ON') . ' <strong class="highlighted">' . Date::of($this->pub->archived)->format($dateFormat) . '</strong>. ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_ARCHIVED_NO_CHANGE') . '</p></li>'; } ?>
							<?php if ($this->pub->dev_version_label && $this->pub->dev_version_label != $this->pub->versionAlias_label) { ?>
							<li id="next-draft"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_VERSION_STARTED')
							. ' (<strong>v.'
							. $this->pub->dev_version_label . '</strong>)  <span class="block"><a href="'
							. Route::url($this->pub->link('edit') .'&version=dev') . '">'
							. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION_CONTINUE') . '</a></span>';  ?></p></li>
							<?php } else if (!$this->pub->dev_version_label) {
							?>
							<li id="next-edit">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CHANGES_NEEDED_OPTION'); if ($revertAllowed) { echo ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_GRACE_PERIOD'); } ?>
								<?php if ($revertAllowed && $allowArchive && $archiveDate) { echo '<p class="info">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WILL_BE_ARCHIVED') . ' <strong class="highlighted">' . Date::of($archiveDate)->format($dateFormat) . '</strong>, ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WILL_BE_ARCHIVED_NO_CHANGE') . '</p>'; } ?>
								<span class="revert-options">
								<?php if ($revertAllowed)
								{
									echo ' <a href="' . Route::url($this->pub->link('edit') . '&action=revert&version=' . $this->pub->versionAlias) .'" class="btn icon-revert" id="action-revert">'
								. Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_REVERT')
								. '</a> <span class="block and_or">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_OR') . '</span>';
								}
								echo ' <a href="' . Route::url($this->pub->link('edit') . '&action=newversion') . '" class="showinbox btn icon-add">'
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
							<p class="centeralign"><a href="<?php echo Route::url($this->pub->link('edit') . '&action=review&version=' . $this->pub->versionAlias); ?>" class="btn btn-success active"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_RESUBMIT_TO_PUBLISH_REVIEW'); ?></a></p></li>
						<?php } else { ?>
							<li id="next-edit">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DRAFT_INCOMPLETE_RESUBMIT'); ?></p>
								<p class="next-controls"><a href="<?php echo Route::url( $this->pub->link('edit') ) . '?action=continue&amp;version=' . $this->pub->versionAlias; ?>" id="start-curation" class="btn btn-primary active icon-next"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MAKE_CHANGES'); ?></a></p>
							</li>
						<?php }
						break;

						// Pending
						case 5: ?>
						<li id="next-pending">
							<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING');  ?>	</p>
							<?php if ($this->pub->doi) {
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
							<p class="centeralign"><a href="<?php echo Route::url($this->pub->link('edit') . '&action=review&version=' . $this->pub->versionAlias); ?>" class="btn btn-success active"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUB_SUBMIT_TO_PUBLISH_REVIEW'); ?></a></p></li>
						<?php } else { ?>
							<li id="next-edit">
								<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DRAFT_INCOMPLETE_CURATE'); ?></p>
								<p class="next-controls"><a href="<?php echo Route::url( $this->pub->link('edit') . '&action=continue&amp;version=' . $this->pub->versionAliasAlias ); ?>" id="start-curation" class="btn btn-primary active icon-next"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE_DRAFT'); ?></a></p>
							</li>
						<?php } ?>

						<li id="next-cancel"><p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEED_TO_CANCEL').' <a href="' . Route::url($this->pub->link('edit') . '&action=cancel&version=' . $this->pub->versionAlias) . '">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL') . '</a> ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL_BEFORE');  ?></p></li>
				<?php		break;
					}
				?>
				</ul>
			</div>
		</div>
	</div>
 </div>
</form>
<?php // We need this to make CKEditor checker work ?>
<script>
jQuery(document).ready(function($){
	HUB.ProjectPublicationsDraft.initialize();
});
</script>
