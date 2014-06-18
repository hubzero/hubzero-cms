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

$yearFormat = '%Y';
$dateFormat = '%m/%d/%Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$yearFormat = 'Y';
	$dateFormat = 'm/d/Y';
	$tz = false;
}

$pubHelper 		= $this->pub->_helpers->pubHelper;
$htmlHelper 	= $this->pub->_helpers->htmlHelper;
$projectsHelper = $this->pub->_helpers->projectsHelper;

// Get hub config
$juri 	 = JURI::getInstance();
$jconfig = JFactory::getConfig();
$site 	 = $jconfig->getValue('config.live_site')
	? $jconfig->getValue('config.live_site')
	: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);

$now 	= JFactory::getDate()->toSql();
$v 		= $this->pub->version == 'default' ? '' : '?v=' . $this->pub->version;

// Build our citation object
$citation = '';
if ($this->pub->doi)
{
	include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php' );

	$cite 		 	= new stdClass();
	$cite->title 	= $this->pub->title;
	$date 			= ($this->pub->published_up && $this->pub->published_up != '0000-00-00 00:00:00')
					? $this->pub->published_up : $this->pub->submitted;
	$cite->year  	= JHTML::_('date', $date, 'Y');
	$cite->location = '';
	$cite->date 	= '';

	$cite->url = $site . DS . 'publications' . DS . $this->pub->id.'?v='.$this->pub->version_number;
	$cite->type = '';
	$cite->author = $pubHelper->getUnlinkedContributors( $this->pub->_authors);
	$cite->doi = $this->pub->doi;
	$citation = CitationFormat::formatReference($cite);
}

// Get creator name
$profile = \Hubzero\User\Profile::getInstance($this->pub->created_by);
$creator = $profile->get('name') . ' (' . $profile->get('username') . ')';

// Version status
$status = $pubHelper->getPubStateProperty($this->pub, 'status');
$class 	= $pubHelper->getPubStateProperty($this->pub, 'class');

$complete 	= $this->pub->_curationModel->_progress->complete;

$pubRoute = $this->pub->id ? $this->route . '&pid=' . $this->pub->id : $this->route;

$showCitations  = $this->pub->_category->_params->get('show_citations', 1);
$allowUnpublish = $this->pub->_category->_params->get('option_unpublish', 0);

// We also need a citations block
$blockActive   = $this->pub->_curationModel->blockExists('citations');
$showCitations = $blockActive ? $showCitations : 0;

// Check if publication is within grace period (published status)
$revertAllowed = $this->pubconfig->get('graceperiod', 0);
if ($revertAllowed && $this->pub->accepted && $this->pub->accepted != '0000-00-00 00:00:00')
{
	$monthFrom = JFactory::getDate($this->pub->accepted . '+1 month')->toSql();
	if (strtotime($monthFrom) < strtotime(JFactory::getDate()))
	{
		$revertAllowed = 0;
	}
}
?>

<form action="<?php echo $this->url; ?>" method="post" id="plg-form" enctype="multipart/form-data">
	<?php echo $this->project->provisioned == 1
				? $pubHelper->showPubTitleProvisioned( $this->pub, $this->route)
				: $pubHelper->showPubTitle( $this->pub, $this->route, $this->title); ?>

		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
			<input type="hidden" name="version" value="<?php echo $this->pub->version; ?>" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="section" id="section" value="status" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->pub->version_id; ?>" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
			<?php if($this->project->provisioned == 1 ) { ?>
			<input type="hidden" name="task" value="submit" />
			<?php } ?>
		</fieldset>
<?php
	// Draw status bar
	echo $this->pub->_curationModel->drawStatusBar();
?>

<div id="pub-body" class="<?php echo $this->pub->version; ?>">
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 	<div class="c-inner">
				<h4><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION')
					. ' ' . $this->pub->version_label . ' (' . $status . ')'; ?>
				</h4>
				<table class="tbl-panel">
					<tbody>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?>:</td>
							<td class="tbl-input"><span><?php echo $this->pub->title; ?></span></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_LABEL'); ?>:</td>
							<td class="tbl-input"><span <?php if(($this->pub->version == 'dev' || $this->pub->state == 4) && $this->task != 'edit') { echo 'id="edit-vlabel" class="pub-edit"'; } ?>><?php echo $this->pub->version_label;  ?></span> <?php if($this->pub->main == 1) { echo '<span id="v-label">('.JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_DEFAULT').')</span>'; } ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_NUMBER'); ?>:</td>
							<td class="tbl-input"><span><?php echo $this->pub->version_number;  ?></span><?php if($this->pub->versions) { ?> &nbsp; &nbsp;<span >[<a href="<?php echo $this->url . '/?action=versions'; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL_VERSIONS'); ?></a>]</span><?php } ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_CREATED')); ?>:</td>
							<td class="tbl-input"><?php echo JHTML::_('date', $this->pub->created, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->pub->created).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_CREATED_BY')); ?>:</td>
							<td class="tbl-input"><?php echo $creator; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT')); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->_type->type; ?></td>
						</tr>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?>:</td>
							<td class="tbl-input">
								<span class="<?php echo $class; ?>"> <?php echo $status; ?></span>
								<?php if ($this->pub->published_up > $now ) { ?>
								<span class="embargo"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_EMBARGO') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_UNTIL') . ' ' . JHTML::_('date', $this->pub->published_up, $dateFormat, $tz); ?></span>
								<?php } ?>
							</td>
						</tr>
						<?php if($this->pub->doi) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DOI'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->doi ? $this->pub->doi : JText::_('PLG_PROJECTS_PUBLICATIONS_NA') ; ?>
							<?php if($this->pub->doi) { echo ' <a href="' . $this->config->get('doi_verify', 'http://n2t.net/ezid/id/') . 'doi:' . $this->pub->doi . '" rel="external">[&rarr;]</a>'; } ?>
							</td>
						</tr>
						<?php } ?>
						<?php if(($this->pubconfig->get('issue_arch') && $this->pub->state == 6) || $this->pub->ark) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ARCH'); ?>:</td>
							<td class="tbl-input"><?php echo $this->pub->ark ? $this->pub->ark : JText::_('PLG_PROJECTS_PUBLICATIONS_NA') ; ?>
							<?php if($this->pub->ark) { echo ' <a href="' . $this->config->get('doi_verify', 'http://n2t.net/ezid/id/') . 'ark:' . $this->pub->ark . '" rel="external">[&rarr;]</a>'; } ?>
							</td>
						</tr>
						<?php } ?>
						<?php if ($this->pub->state == 1 || $this->pub->state == 0) {  ?>
						<?php
							if ($this->pub->submitted && $this->pub->submitted != '0000-00-00 00:00:00')  { ?>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTED'); ?>:</td>
							<td class="tbl-input"><?php echo JHTML::_('date', $this->pub->submitted, $dateFormat, $tz); ?></td>
						</tr>

						<?php } elseif ($this->pub->published_up <= $now) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLISH_FROM'); ?>:</td>
							<td class="tbl-input"><?php echo JHTML::_('date', $this->pub->published_up, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->pub->published_up).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
						</tr>
						<?php } ?>
						<?php if ($this->pub->accepted != '0000-00-00 00:00:00') { ?>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCEPTED'); ?>:</td>
							<td class="tbl-input"><?php echo JHTML::_('date', $this->pub->accepted, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->pub->accepted).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
						</tr>
						<?php } ?>
						<?php } elseif ($this->pub->state != 3) {
							$date = $this->pub->published_up;
							if ($this->pub->state == 5 || $this->pub->state == 7) {
								$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTED');
								$date = $this->pub->submitted != '0000-00-00 00:00:00'
									? $this->pub->submitted : $this->pub->published_up;
							}
							elseif ($this->pub->state == 4)
							{
								$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_FINALIZED');
							}
							elseif ($this->pub->state == 6)
							{
								$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_ARCHIVED');
							}
							else {
								$show_action = JText::_('PLG_PROJECTS_PUBLICATIONS_RELEASED');
							}
						?>
						<tr>
							<td class="tbl-lbl"><?php echo $show_action; ?>:</td>
							<td class="tbl-input"><?php echo JHTML::_('date', $date, $dateFormat, $tz) . ' (' . ProjectsHtml::timeAgo($date).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
						</tr>
						<?php } ?>
						<?php if($this->pub->state == 0) { ?>
						<tr>
							<td class="tbl-lbl"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_UNPUBLISHED')); ?>:</td>
							<td class="tbl-input"><?php echo JHTML::_('date', $this->pub->published_down, $dateFormat, $tz).' ('.ProjectsHtml::timeAgo($this->pub->published_down).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_AGO').')'; ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td class="tbl-lbl"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_URL'); ?>:</td>
							<td class="tbl-input"><a href="<?php echo JRoute::_('index.php?option=com_publications'.a.'id='.$this->pub->id.$v); ?>"><?php echo trim($site, DS) .'/publications/'.$this->pub->id.$v; ?></a></td>
						</tr>
					</tbody>
				</table>

				<?php if ($this->pub->version == 'dev') { ?>
					<p class="c-instruct "><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_HINT_LABEL'); ?></p>
				<?php } ?>
			</div>
		</div>
		<div class="two columns second" id="c-output">
		 	<div class="c-inner">
				<h4>
				<?php if ($this->pub->version == 'dev' || $this->pub->state == 5) { ?>
					<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT'); ?>
				<?php } else { ?>
					<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_YOUR_OPTIONS'); ?>
				<?php } ?>
				</h4>
				<ul class="next-options">
				<?php
					switch ($this->pub->state)
					{
						// Unpublished
						case 0:
						break;

						// Published
						case 1: ?>
							<?php if ($allowUnpublish) { ?>
							<li id="next-cancel"><p>
							<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISHED_UNPUBLISH');
							echo ' <a href="' . $this->url . '/?action=cancel' . a . 'version=' . $this->pub->version . '">'
							.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_UNPUBLISH_VERSION').' &raquo;</a> ';  ?></p></li>
							<?php } ?>
							<li id="next-usage"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_WATCH_STATS')
							.' <strong>'.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_USAGE_STATS').'</strong> '
							.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_FOLLOW_FEEDBACK');  ?>
								<span class="block italic"><a href="<?php echo $this->url . '/?action=stats' . a . 'version=' . $this->pub->version; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_USAGE'); ?> &raquo;</a></span></p></li>
							<?php if ($showCitations) { ?>
							<li id="next-citation"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_WATCH_ADD_CITATIONS');  ?>
								<span class="block italic"><a href="<?php echo $this->url . '/?section=citations' . a . 'version=' . $this->pub->version; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADD_CITATIONS'); ?> &raquo;</a></span></p></li>
							<?php } ?>

							<?php if ($this->pub->dev_version_label && $this->pub->dev_version_label != $this->pub->version_label) { ?>
							<li id="next-draft"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_VERSION_STARTED')
							.' (<strong>v.'
							.$this->pub->dev_version_label.'</strong>)  <span class="block"><a href="'
							. $this->url .'/?version=dev">'
							. JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION_CONTINUE').'</a></span>';  ?></p></li>
							<?php } else if (!$this->pub->dev_version_label) {
							?>
							<li id="next-edit">
								<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CHANGES_NEEDED_OPTION'); if ($revertAllowed) { echo ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_GRACE_PERIOD'); } ?>
								<span class="revert-options">
								<?php if ($revertAllowed)
								{
									echo ' <a href="' . $this->url .'/?action=revert&version=' . $this->pub->version .'" class="btn icon-revert" id="action-revert">'
								. JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_REVERT')
								. '</a> <span class="block and_or">' . JText::_('PLG_PROJECTS_PUBLICATIONS_OR') . '</span>';
								}
								echo ' <a href="' . $this->url .'/?action=newversion" class="showinbox btn icon-add">'
							.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION').'</a> ' ;  ?>
								</span>
								</p></li>
							<?php } ?>
						<?php
						break;

						// Kicked back to authors
						case 7:
						?>

						<?php if ($complete) { ?>
						<li id="next-publish"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_RESUBMIT');  ?></p>
							<p class="centeralign"><a href="<?php echo $this->url.'/?action=review'. a . 'version='.$this->pub->version; ?>" class="btn btn-success active"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_RESUBMIT_TO_PUBLISH_REVIEW'); ?></a></p></li>
						<?php } else { ?>
							<li id="next-edit">
								<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DRAFT_INCOMPLETE_RESUBMIT'); ?></p>
								<p class="next-controls"><a href="<?php echo JRoute::_( $pubRoute ) . '?action=continue&version=' .$this->pub->version; ?>" id="start-curation" class="btn btn-primary active icon-next"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_MAKE_CHANGES'); ?></a></p>
							</li>
						<?php }
						break;


						// Pending
						case 5: ?>
						<li id="next-pending">
							<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING');  ?>	</p>
							<?php if ($this->pub->doi) {
								echo '<p>' . JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PENDING_DOI_ISSUED') . '</p>'
								. '<div class="citeit">' . $citation . '</div>'; } ?>
						</li>
						<?php
						break;

						// Draft
						case 3:
						default: ?>

						<?php if ($complete) { ?>
						<li id="next-publish"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_PUBLISH_READY');  ?></p>
							<p class="centeralign"><a href="<?php echo $this->url.'/?action=review'. a . 'version='.$this->pub->version; ?>" class="btn btn-success active"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_SUBMIT_TO_PUBLISH_REVIEW'); ?></a></p></li>
						<?php } else { ?>
							<li id="next-edit">
								<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_DRAFT_INCOMPLETE_CURATE'); ?></p>
								<p class="next-controls"><a href="<?php echo JRoute::_( $pubRoute ) . '?action=continue&version=' .$this->pub->version; ?>" id="start-curation" class="btn btn-primary active icon-next"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTINUE_DRAFT'); ?></a></p>
							</li>
						<?php } ?>

						<li id="next-cancel"><p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEED_TO_CANCEL').' <a href="'.$this->url.'/?action=cancel' . a . 'version='.$this->pub->version.'">'.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL').'</a> '.JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_CANCEL_BEFORE');  ?></p></li>
				<?php		break;
					}
				?>
				</ul>
			</div>
		</div>
	</div>
 </div>
</form>

<script>
jQuery(document).ready(function($){
	HUB.ProjectPublicationsDraft.initialize();
});
</script>
