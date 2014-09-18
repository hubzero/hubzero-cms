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

// Get publication properties
if ($this->pub->id)
{
	// Are we allowed to edit?
	$canedit = ($this->pub->state == 1 || $this->pub->state == 0 || $this->pub->state == 6 ) ? 0 : 1;
}
else {
	// New pub
	$canedit = 1;
}

// Cannot pick a different database once draft is started
$selOff          = ($this->_pubTypeHelper->_changeAllowed == false && $this->row->id) ? 1 : 0;
$multi  		 = $this->_pubTypeHelper->_multiSelect;

// Determine pane title
$ptitle = '';
if ($this->version == 'dev')
{
	$ptitle .= $this->last_idx > $this->current_idx
		? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE_EDIT_'.strtoupper($this->_pubTypeHelper->_base))).' '
		: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_TITLE_SELECT_'.strtoupper($this->_pubTypeHelper->_base))).' ' ;
}
else
{
	$ptitle = ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT'));
}

?>

<?php if ($this->pub->id && $this->row->title) { ?>
	<?php echo $this->project->provisioned == 1
				? $this->helper->showPubTitleProvisioned( $this->pub, $this->route)
				: $this->helper->showPubTitle( $this->pub, $this->route, $this->title); ?>
<?php } else if ($this->project->provisioned == 1 ) { ?>
	<h3 class="prov-header"><a href="<?php echo $this->route; ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')); ?></h3>
<?php } else { ?>
	<div id="plg-header">
	<h3 class="publications c-header"><a href="<?php echo JRoute::_($this->route); ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS')); ?></a> &raquo; <span class="indlist"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_NEWPUB')); ?></span></h3>
	</div>
<?php } ?>

<?php

	// Draw status bar
	$this->contribHelper->drawStatusBar($this, 'primary');

	// Information text
	$infotext = JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INFO_PRIMARY_CONTENT_MORE_'. strtoupper($this->_pubTypeHelper->_base));

	// Section body starts:
?>
	<?php if (!$this->pub->id && $this->project->provisioned != 1 && count($this->choices) > 1) { ?>
		<p class="editing mini"><a href="<?php echo $this->url; ?>" >&laquo; <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION_CHANGE_TYPE'); ?></a></span>
	<?php } ?>
	<div id="pub-editor">
		<?php if ($selOff) { ?>
		<div id="c-pane" class="columns">
		<?php } else { ?>
		<div class="two columns first" id="c-selector">
			<div class="c-inner" id="c-item-picker">
				<h4><?php echo $ptitle; ?></h4>
				<?php if ($canedit) { ?>
				<p><?php echo $selOff ? JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_CHOICE_FROM_'.strtoupper($this->_pubTypeHelper->_base)) : JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_SELECT_FROM_'.strtoupper($this->_pubTypeHelper->_base)); ?></p>
				<!-- Load content selection browser //-->
				<div id="c-show">
					<noscript>
						<p class="nojs"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_NO_JS_MESSAGE'); ?></p>
					</noscript>
				</div>
				<!-- END content selection browser //-->
				<?php if ($infotext) { ?>
				<p class="pub-info">
					<?php echo $infotext; ?>
				</p>
				<?php } ?>
				<?php if ($this->project->provisioned == 1 && !$this->pub->id) { echo '<p class="notice">'.JText::_('PLG_PROJECTS_PUBLICATIONS_LOOKING_FOR_PROJECT_' . strtoupper($this->_pubTypeHelper->_base)).'</p>'; } ?>
				<?php } else { ?>
					<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
				<?php } ?>

			</div>
		</div>
		<div class="two columns second" id="c-output">
		<?php } ?>
			<form action="<?php echo JRoute::_($this->route); ?>" method="post" id="plg-form" enctype="multipart/form-data">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
				<input type="hidden" name="sel" value="<?php echo count($this->attachments); ?>" id="sel" />
				<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="base" id="base" value="<?php echo $this->_pubTypeHelper->_base; ?>" />
				<input type="hidden" name="primary" id="primary" value="1" />
				<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
				<?php if ($this->project->provisioned == 1 ) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
				<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
				<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
				<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="selections" id="selections" value="" />
			</fieldset>

			<div class="c-inner">
			<?php if ($canedit) { ?>
				<span class="c-submit"><input type="submit" class="btn" value="<?php if ($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" <?php if (count($this->attachments) == 0) { echo 'class="disabled"'; } ?> id="c-continue" /></span>
			<?php } ?>
				<h5><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION')).' '.JText::_('PLG_PROJECTS_PUBLICATIONS_PRIMARY_CONTENT'); ?>: </h5>
				<ul id="c-filelist" class="c-list <?php if (!$canedit || !$this->pub->id) { ?>noedit<?php } ?>">
					<li id="nosel" <?php if ($this->pub->id) { echo 'class="hidden"'; } ?> ><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NO_CONTENT_SELECTED_CLICK'); ?></li>
					<?php
					// If we have content items selected
					if (count($this->attachments) > 0)
					{
						$i = 1;

						foreach ($this->attachments as $att)
						{
							// Check if item is missing
							$gone = $this->_typeHelper->dispatchByType($att->type, 'checkMissing',
									$data = array('item' => $att, 'fpath' => $this->prefix . $this->fpath,
									'config' => $this->config ));

							$prop = $this->_typeHelper->dispatchByType($att->type, 'getMainProperty');

							?>
							<li id="clone-<?php echo $att->type ?>::<?php echo urlencode($att->$prop); ?>" class="<?php echo 'attached-' . $i; ?> c-drag <?php if ($gone) { echo ' i-missing'; } ?>">

							<?php
									// Draw item
									$itemHtml = $this->_typeHelper->dispatchByType($att->type, 'drawItem',
									$data = array(
											'att' 		=> $att,
											'item'		=> NULL,
											'canedit' 	=> $canedit,
											'pid' 		=> $this->row->publication_id,
											'vid'		=> $this->row->id,
											'url'		=> $this->url,
											'option'	=> $this->option,
											'move'		=> $this->move,
											'role'		=> 1,
											'path'		=> $this->prefix . $this->fpath
									));
									echo $itemHtml;
							?>
						</li>
					<?php
							$i++;
						}
					}  ?>
				</ul>

				<?php if ($canedit) { ?>
				<div id="pub-options"></div>
				<?php } ?>
				</div>
			</form>
		 </div>
	</div>

