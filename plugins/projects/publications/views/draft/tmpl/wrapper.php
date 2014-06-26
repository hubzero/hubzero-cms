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

$project = $this->pub->_project;

$prov = $this->pub->_project->provisioned ? 1 : 0;

// Build url
$route = $this->pub->_project->provisioned
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias='
				. $this->pub->_project->alias . '&active=publications';

$url = $this->pub->id ? JRoute::_($route . '&pid=' . $this->pub->id) : JRoute::_($route);

// Are we in draft flow?
//$move = JRequest::getVar( 'move', '' );
$move = ($this->showControls) ? 'continue' : '';

$title 	 = $move && $this->manifest->draftHeading ? $this->manifest->draftHeading : $this->manifest->title;
$tagline = isset($this->manifest->draftTagline) ? $this->manifest->draftTagline : NULL;

// Get block completion status
$step 	  =  $this->step;
$complete =  $this->pub->_curationModel->_progress->blocks->$step->status->status;

// Is panel content (of any kind) required?
$required = isset($this->pub->_curationModel->_progress->blocks->$step->manifest->params->required)
	? $this->pub->_curationModel->_progress->blocks->$step->manifest->params->required : 0;

$activeEl = isset($this->master->props['showElement'])
			? $this->master->props['showElement'] : 0;
$element =  JRequest::getInt( 'el', $activeEl );
?>
<div id="pub-editor" class="pane-desc">
	<form action="<?php echo $url; ?>" method="post" id="plg-form" enctype="multipart/form-data">
	 	 <fieldset>
			<input type="hidden" name="id" value="<?php echo $project->id; ?>" id="projectid" />
			<input type="hidden" name="version" id="version" value="<?php echo $this->pub->version; ?>" />
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
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $this->pub->version_id; ?>" />
			<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
			<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $prov == 1 ? 1 : 0; ?>" />
		 </fieldset>
  		<div id="c-pane" class="columns">
			 <div class="c-inner draftflow">
						<h4><?php echo $title; ?></h4>
						<?php
							if ($tagline && $move)
							{ ?>
							<h5><?php echo $tagline; ?> <?php if ($this->manifest->about && !$prov && $this->manifest->elements) { ?><a class="pub-info-pop more-content" href="#info-panel" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CLICK_TO_LEARN_MORE'); ?>">&nbsp;</a> <?php } ?></h5>
						<?php }
						?>
						<?php echo $this->content; ?>
						<div class="hidden">
							<div id="info-panel" class="full-content"><?php echo $this->manifest->about; ?></div>
						</div>

						<?php
						if ($this->active != 'review') { ?>
						<div class="submit-area <?php echo $this->showControls == 2 ? ' extended' : ''; ?>" id="submit-area">
							<?php if ($this->step != 1 && $this->showControls) { ?>
								<span class="button-wrapper bw-previous icon-prev">
									<input type="button" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GO_PREVIOUS'); ?>" id="c-previous" class="submitbutton btn icon-prev" />
								</span>
							<?php } ?>
							<span class="button-wrapper icon-apply">
								<input type="submit" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_APPLY_CHANGES'); ?>" id="c-apply" class="submitbutton btn icon-apply" />
							</span>
							<?php if ($this->showControls) { ?>
							<span class="button-wrapper icon-next">
								<input type="submit" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GO_NEXT'); ?>" id="c-next" class="submitbutton btn icon-next" />
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
		<form id="notice-form" name="noticeForm" action="<?php echo $url; ?>" method="post">
		 <fieldset>
			<input type="hidden" name="pid" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="version" value="<?php echo $this->pub->version_number; ?>" />
			<input type="hidden" name="p" id="props" value="" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="dispute" />
			<h5 id="notice-title"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_TITLE'); ?></h5>
			<label>
				<span class="block"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_LABEL'); ?></span>
				<textarea name="review" id="notice-review" rows="5" cols="10"></textarea>
			</label>
			</fieldset>
			<p class="submitarea">
				<input type="submit" id="notice-submit" class="btn" value="<?php echo JText::_('COM_PUBLICATIONS_SAVE'); ?>" />
			</p>
		</form>
	</div>
</div>