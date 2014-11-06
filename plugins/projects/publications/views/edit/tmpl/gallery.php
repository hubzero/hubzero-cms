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

// Determine pane title
if ($this->version == 'dev')
{
	$ptitle = $this->last_idx > $this->current_idx  ?
	ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_GALLERY')) : ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_ADD_GALLERY_IMAGES')) ;
}
else
{
	$ptitle = ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_GALLERY'));
}
?>
<?php echo $this->project->provisioned == 1
			? $this->helper->showPubTitleProvisioned( $this->pub, $this->route)
			: $this->helper->showPubTitle( $this->pub, $this->route, $this->title); ?>

<?php
	// Draw status bar
	$this->contribHelper->drawStatusBar($this);

	$canedit = (
		$this->pub->state == 3
		|| $this->pub->state == 4
		|| $this->pub->state == 5
		|| in_array($this->active, $this->mayupdate))
		? 1 : 0;

// Section body starts:
?>
<div id="pub-body">
	<div id="pub-editor" class="pane-gallery">
		<div class="two columns first" id="c-selector">
		 <div class="c-inner" id="c-item-picker">
			<h4><?php echo $ptitle; ?> <?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span><?php } ?></h4>

			<?php if ($canedit) { ?>
			<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_SELECT'); ?></p>
			<!-- Load content selection browser //-->
			<div id="c-show">
				<noscript>
					<p class="nojs"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_NO_JS_MESSAGE'); ?></p>
				</noscript>
			</div>
			<!-- END content selection browser //-->
			<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INFO_GALLERY'); ?></p>
			<?php } else { ?>
				<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
			<?php } ?>
		 </div>
		</div>
		<div class="two columns second" id="c-output">
			<form action="<?php echo $this->url; ?>" method="post" id="plg-form" enctype="multipart/form-data">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
				<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="base" id="base" value="files" />
				<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
				<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
				<input type="hidden" name="selections" id="selections" value="" />
				<input type="hidden" name="required" id="required" value="<?php echo in_array($this->active, $this->required) ? 1 : 0; ?>" />
				<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
				<?php if ($this->project->provisioned == 1 ) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
		 		<div class="c-inner">
			<?php if ($canedit) { ?>
					<span class="c-submit"><input type="submit" class="btn" value="<?php if ($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" id="c-continue" /></span>
			<?php } ?>
					<h5><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_GALLERY'); ?>: </h5>

						<ul id="c-filelist" class="c-list">
							<li id="nosel" <?php if (count($this->shots) > 0) { echo 'class="hidden"'; } ?> ><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NO_SHOTS_SELECTED_CLICK'); ?></li>
							<?php
							// If we have files selected
							if (count($this->shots) > 0) {
								$ih = new ProjectsImgHandler();
								$i = 1;
								foreach ($this->shots as $shot) {
									$thumb = $ih->createThumbName($shot->srcfile, '_tn', $extension = 'png');
									$src = JRoute::_('index.php?option=com_publications&id=' . $this->pub->id . '&v=' . $this->row->id) . '/Image:' . $thumb;
									// Check if master file is still there
									$gone = is_file($this->prefix.$this->fpath.DS.$shot->filename) ? 0 : 1;
									?>
								<li class="<?php echo 'attached-' . $i; ?> c-drag <?php if ($gone) { echo 'i-missing'; } ?>" id="clone-file::<?php echo urlencode($shot->filename); ?>">
									<?php
										// Screenshot HTML
										$this->view('default', 'screenshot')
										     ->set('url', $this->url)
										     ->set('project', $this->project)
										     ->set('option', $this->option)
										     ->set('pid', $this->row->publication_id)
										     ->set('vid', $this->row->id)
										     ->set('ima', $shot->filename)
										     ->set('title', $shot->title)
										     ->set('src', $src)
										     ->set('move', $this->move)
										     ->set('canedit', $canedit)
										     ->display();
									?>
								</li>
							<?php $i++; } }  ?>
						</ul>
						<p id="c-instruct"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GALLERY_HINT_DRAG'); ?></p>
		 		</div>
			</form>
		</div>
			<iframe id="upload_target" name="upload_target" src="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=files').'/?action=blank'.a.'no_html=1'.a.'ajax=1'; ?>" class="iframe"></iframe>
	</div>
</div>
