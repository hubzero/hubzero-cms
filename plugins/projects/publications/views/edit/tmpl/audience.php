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
	$ptitle = $this->last_idx > $this->current_idx
		? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_AUDIENCE'))
		: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_SELECT_AUDIENCE')) ;
}
else
{
	$ptitle = ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_AUDIENCE'));
}

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form" enctype="multipart/form-data">
	<?php echo $this->project->provisioned == 1
				? $this->helper->showPubTitleProvisioned( $this->pub, $this->route)
				: $this->helper->showPubTitle( $this->pub, $this->route, $this->title); ?>

<?php
	// Draw status bar
	$this->contribHelper->drawStatusBar($this);

// Section body starts:
$levels 	= array();
$sel 		= 0;
$picked 	= '';

$canedit = (
	$this->pub->state == 3
	|| $this->pub->state == 4
	|| $this->pub->state == 5
	|| in_array($this->active, $this->mayupdate))
	? 1 : 0;

?>
<div id="pub-body">
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 <div class="c-inner">
				<h4><?php echo $ptitle; ?> <?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span><?php } ?></h4>
				<?php if ($canedit) { ?>
				<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE_SELECT'); ?></p>
				<div id="c-show">
					<ul id="c-browser">
					<?php foreach($this->levels as $level) {
							$label = $level->label;

							$sel = $this->audience->$label == 1 ? $sel + 1 : $sel;
							$picked .= $this->audience->$label == 1 ? $label.'-' : ''; ?>
						<li id="<?php echo $level->label; ?>" class="c-click" title="<?php echo htmlentities($level->title); ?>">	<?php echo PublicationsHtml::skillLevelCircle($this->levels, $level->label); ?> <span class="aud-desc"><?php echo $level->description; ?></span></li>
						<?php } ?>
					</ul>
				</div>
				<p class="and_or vpadded"><?php echo strtoupper(JText::_('PLG_PROJECTS_PUBLICATIONS_OR')); ?>...</p>
				<label><input type="checkbox" name="no_audience" id="no-audience" value="1" <?php echo (!$picked && $this->audience->id) ? 'checked="checked"' : '';  ?> />	<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE_NOT_SHOW'); ?>
				</label>
				<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_AUDIENCE'); ?></p>
				<?php } else { ?>
					<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
				<?php } ?>
		 </div>
		</div>
		<div class="two columns second" id="c-output">
		 <div class="c-inner">
			<?php if ($canedit) { ?>
				<span class="c-submit"><input type="submit" class="btn" value="<?php if($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" id="c-continue" /></span>
			<?php } ?>
				<h5><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_AUDIENCE')); ?>: </h5>
				<ul id="c-audience" class="c-list">
					<li id="nosel" <?php if($this->last_idx > $this->current_idx && $this->audience->id) { echo 'class="hidden"'; } ?> ><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE_NONE_SELECTED'); ?></li>
					<li id="c-sel-audience" class="prominent<?php if($this->last_idx <= $this->current_idx || !$this->audience->id) { echo ' hidden'; } ?>"><?php echo $picked ? PublicationsHtml:: showSkillLevel($this->audience, $showtips = 0) : JText::_('PLG_PROJECTS_PUBLICATIONS_AUDIENCE_NOT_SHOWN'); ?></li>
				</ul>
		 </div>
		</div>
	</div>
</div>
<fieldset>
	<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
	<input type="hidden" name="version" value="<?php echo $this->version; ?>" />
	<input type="hidden" name="active" value="publications" />
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="base" id="base" value="<?php echo $this->pub->base; ?>" />
	<input type="hidden" name="section" id="section" value="<?php echo $this->active; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="move" id="move" value="<?php echo $this->move; ?>" />
	<input type="hidden" name="review" value="<?php echo $this->inreview; ?>" />
	<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
	<input type="hidden" name="vid" id="vid" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="audience" id="audience" value="<?php echo $picked; ?>" />
	<input type="hidden" name="required" id="required" value="<?php echo in_array($this->active, $this->required) ? 1 : 0; ?>" />
	<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
	<?php if($this->project->provisioned == 1 ) { ?>
	<input type="hidden" name="task" value="submit" />
	<?php } ?>
</fieldset>
</form>