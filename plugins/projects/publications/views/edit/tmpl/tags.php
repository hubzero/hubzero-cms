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
if($this->version == 'dev') {
	$ptitle = $this->last_idx > $this->current_idx  ?
	ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_TAGS')) : ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_ADD_TAGS')) ;
}
else {
	$ptitle = ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_TAGS'));
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
	<div id="pub-editor" class="pane-tags">
		<div class="two columns first" id="c-selector">
		 <div class="c-inner">
			<h4><?php echo $ptitle; ?> <?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span><?php } ?></h4>
			<?php if ($canedit) { ?>
			<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_SELECT'); ?></p>
			<!-- Load content selection browser //-->
			<div id="c-show">
				<div id="pick-tags">
					<noscript>
							<p class="nojs"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_NO_JS_MESSAGE'); ?></p>
					</noscript>
				</div>
			</div>
			<!-- END content selection browser //-->
			<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INFO_TAGS'); ?></p>
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
					<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
					<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
					<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
					<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
					<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
					<input type="hidden" name="selections" id="selections" value="" />
					<input type="hidden" name="required" id="required" value="<?php echo in_array($this->active, $this->required) ? 1 : 0; ?>" />
					<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
					<?php if($this->project->provisioned == 1 ) { ?>
					<input type="hidden" name="task" value="submit" />
					<?php } ?>
				</fieldset>
			 <div class="c-inner">
					<?php if($canedit) { ?>
							<span class="c-submit"><input type="submit" class="btn" value="<?php if($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" id="c-continue" /></span>
					<?php } ?>
					<h5><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_TAGS'); ?>: </h5>
					<?php if($canedit) { ?>
					<label>
						<?php
						JPluginHelper::importPlugin( 'hubzero' );
						$dispatcher = JDispatcher::getInstance();

						$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->tags)) );

						if (count($tf) > 0) {
							echo $tf[0];
						} else {
							echo '<textarea name="tags" id="tags" rows="6" cols="35">'. $this->tags .'</textarea>'."\n";
						}
						?>
					</label>
					<?php if($this->categories) {

						$paramsClass = 'JParameter';
						if (version_compare(JVERSION, '1.6', 'ge'))
						{
							$paramsClass = 'JRegistry';
						}
						?>
					<h5><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_SELECT_CATEGORY'); ?></h5>
					<?php foreach($this->categories as $cat) {
						$params = new $paramsClass($cat->params);
						// Skip inaplicable category
						if (!$params->get('type_' . $this->pub->base, 1))
						{
							continue;
						}
						?>
						<label class="pubtype-block">
						 <input type="radio" name ="pubtype" value="<?php echo $cat->id; ?>"
						<?php if($this->pub->category == $cat->id) { echo 'checked="checked"'; } ?> />	<?php echo $cat->name; ?>
							<span><?php echo $cat->description; ?></span>
						</label>
					<?php } ?>
					<?php } ?>
					<?php } else {
						// Show tags
						if ($this->tags) {
								$this->helper->getTagCloud( 1 );
								echo $this->helper->tagCloud;
						}
						else {
							echo '<p class="nocontent">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NONE').'</p>';
						}
					} ?>
			 </div>
			</form>
		</div>
	</div>
</div>