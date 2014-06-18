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

$prov = $this->pub->_project->provisioned == 1 ? 1 : 0;

// Get block properties
$step 	  = $this->step;
$block	  = $this->pub->_curationModel->_progress->blocks->$step;
$complete = $block->status->status;
$name	  = $block->name;
$manifest = $this->pub->_curationModel->_manifest;

$props = $name . '-' . $this->step;

// Build url
$route = $prov
		? 'index.php?option=com_publications&task=submit&pid=' . $this->pub->id
		: 'index.php?option=com_projects&alias=' . $this->pub->_project->alias;
$selectUrl   = $prov
		? JRoute::_( $route) . '?active=team&action=select'
		: JRoute::_( $route . '&active=team&action=select') .'/?p=' . $props . '&pid='
		. $this->pub->id . '&vid=' . $this->pub->version_id;

$editUrl = $prov ? JRoute::_($route) : JRoute::_($route . '&active=publications&pid=' . $this->pub->id);

// Are we in draft flow?
$move = JRequest::getVar( 'move', '' );
$move = $move ? '&move=continue' : '';

$elName = "reviewPanel";

$complete = $this->pub->_curationModel->_progress->complete;

$requireDoi   = isset($manifest->params->require_doi) ? $manifest->params->require_doi : 0;
$showArchival = isset($manifest->params->show_archival) ? $manifest->params->show_archival : 0;

$juser = JFactory::getUser();

// Get hub config
$juri 	 = JURI::getInstance();
$jconfig = JFactory::getConfig();
$site 	 = $jconfig->getValue('config.live_site')
	? $jconfig->getValue('config.live_site')
	: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);
$sitename = $jconfig->getValue('config.sitename');

// Build our citation object
$citation = '';
if ($this->pub->doi)
{
	$pubHelper 		= $this->pub->_helpers->pubHelper;
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

// Embargo
$pubdate = JRequest::getVar('publish_date');

// Get configs
$config   = JComponentHelper::getParams( 'com_publications' );
$termsUrl = $config->get('deposit_terms', '');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="draft-review <?php echo $complete ? 'draft-complete' : 'draft-incomplete'; ?> <?php echo $this->pub->state == 7 ? ' draft-resubmit' : ''; ?>">
	<?php if ($complete) { ?>
	<p class="review-prompt"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_COMPLETE'); ?></p>
	<?php } else { ?>
	<p class="review-prompt"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INCOMPLETE'); ?></p>
	<div class="submitarea">
		<a href="<?php echo $editUrl; ?>/?action=continue&version=dev" class="btn mini btn-success icon-next"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTINUE_DRAFT'); ?></a>
	</div>
	<?php } ?>

	<?php if ($complete) {
		//Intructions for previewing page
	?>
	<div class="blockelement" id="review-preview">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_PREVIEW'); ?></h5>
				<div class="element-instructions">
					<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_PREVIEW'); ?></p>
					<div class="submitarea">
						<a href="<?php echo JRoute::_('index.php?option=com_publications&id=' . $this->pub->id . '&v=' . $this->pub->version_number); ?>" class="btn mini btn-primary active icon-next" rel="external"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?></a>
					</div>
				</div>
			</div>
		</div>
	 </div>

	<?php // File list (archive package)
		if ($showArchival) {

			// Get publication path for principal content
			$pubPath = $this->pub->_helpers->pubHelper->buildPath(
				$this->pub->id,
				$this->pub->version_id,
				'',
				$this->pub->secret,
				1
			);

			// Get all the file names
			$files = array();
			if (is_dir($pubPath))
			{
				$files = JFolder::files( $pubPath, '.', true, true, $exclude = array('.hash'));
			}
		?>
	<div class="blockelement" id="review-archival">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_ARCHIVE_PACKAGE'); ?></h5>
				<div class="element-instructions">
				<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_ARCHIVE_PACKAGE'); ?></p>
				<?php if (count($files) > 0) { $i = 0; ?>
					<div class="list-wrapper">
					<ul class="filelist">
					<?php foreach ($files as $file)
						{
							if (substr($file, -5, 5) == '.hash')
							{
								continue;
							}
							else
							{
								$trClass = $i % 2 == 0 ? ' even' : ' odd';

								$parts = explode('.', $file);
								$ext   = count($parts) > 1 ? array_pop($parts) : '';
								$ext   = strtolower($ext);

								echo '<li class="' . $trClass . '"><img alt="" src="' . ProjectsHtml::getFileIcon($ext) . '" /> <span>'
									. str_replace($pubPath . DS, '', $file) . '</span></li>';

								$i++;
							}
						} ?>
					<?php } ?>
					</ul>
					</div>
				</div>
			</div>
		</div>
	 </div>
	<?php } ?>
	<?php // DOI block
		if ($requireDoi == 1 || $this->pub->doi) {
	?>
	 <div class="blockelement" id="review-doi">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_TITLE_DOI'); ?></h5>
				<div class="element-instructions">
					<?php if ($citation) { echo '<p>' . JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_DOI_EXISTS') . '</p><div class="citeit">' . $citation . '</div>'; } else { echo '<p>' . JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_INFO_DOI') . '</p>'; } ?>
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
				<h5 class="element-title"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_REVIEW_DATE'); ?></h5>
				<div class="element-instructions">
					<label>
						<span class="review-label"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_PUBLISH_WHEN'); ?>*:</span>
						<input type="text" id="publish_date" name="publish_date" value="<?php echo $pubdate; ?>" placeholder="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_IMMEDIATE'); ?>" />
						<span class="hint block"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_HINT_EMBARGO'); ?></span>
					</label>

					<?php if (isset($this->pub->_submitter)) {
						// Do we have a submitter choice?
						$submitter = $this->pub->_submitter->name;
						$submitter.= $this->pub->_submitter->organization ? ', ' . $this->pub->_submitter->organization : '';
						$submitter.= '<input type="hidden" name="submitter" value="' . $this->pub->_submitter->user_id. '" />';
						if ($this->pub->_submitter->user_id != $juser->get('id'))
						{
							$submitter  = '<select name="submitter">' . "\n";
							$submitter .= '<option value="' . $juser->get('id') . '" selected="selected">' . $juser->get('name')
								. '</option>' . "\n";
							$submitter .= '<option value="' . $this->pub->_submitter->user_id . '">' . $this->pub->_submitter->name . '</option>' . "\n";
							$submitter .= '</select>';
						}

					?>
					<label>
						<span class="review-label"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMITTER')); ?>:</span> <?php echo $submitter; ?>
					</label>
					<?php } ?>
					<?php if ($requireDoi == 2 && !$this->pub->doi) { 	// Choice of publish/post  ?>
					<h6><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_POST_OR_PUBLISH'); ?></h6>
					<label>
						<input class="option" name="action" type="radio" value="publish" checked="checked" />
						<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_PUBLISH'); ?>
						<span class="hint block ipadded"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_PUBLISH_HINT'); ?></span>
					</label>
					<label>
						<input class="option" name="action" type="radio" value="post" />
						<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_POST'); ?>
						<span class="hint block ipadded"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_OPTION_POST_HINT'); ?></span>
					</label>
					<?php } else { ?>
					<input type="hidden" name="action" value="publish" />
					<?php } ?>
				</div>
			</div>
		</div>
	 </div>
	<?php // Agreements ?>
	<div class="blockelement" id="review-agreement">
		<div class="element_editing">
			<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<h5 class="element-title"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_TITLE_AGREE'); ?></h5>
				<div class="element-instructions">
					<label><span class="required"><?php echo JText::_('Required'); ?></span>
						<input class="option" name="agree" type="checkbox" value="1" />
						<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_AGREE_TO'); if ($termsUrl) { echo ' <a href="'
						. $termsUrl . '" class="popup">'; } echo
						$sitename . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_REVIEW_TERMS_OF_DEPOSIT'); if ($termsUrl) { echo '</a>.'; }  ?>
					</label>
				</div>
			</div>
		</div>
	 </div>

	<?php // Submission ?>
	<div class="submitarea" id="submit-area">
		<span class="submit-question"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_LOOKING_GOOD'); ?></span>
		<span class="button-wrapper icon-apply">
			<input type="submit" value="<?php echo $this->pub->state == 7 ? JText::_('PLG_PROJECTS_PUBLICATIONS_RESUBMIT_DRAFT') : JText::_('PLG_PROJECTS_PUBLICATIONS_SUBMIT_DRAFT'); ?>" id="c-apply" class="submitbutton btn btn-success active icon-apply" />
		</span>
	</div>
	<?php } ?>
	<input type="hidden" name="version" value="<?php echo $this->pub->version; ?>" />
	<input type="hidden" name="confirm" value="1" />
</div>