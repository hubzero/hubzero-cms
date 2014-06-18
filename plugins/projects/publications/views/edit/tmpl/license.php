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
	$ptitle = $this->last_idx > $this->current_idx  ? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_LICENSE')) : ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_CHOOSE_LICENSE')) ;
}
else
{
	$ptitle = ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_LICENSE'));
}

?>
<form action="<?php echo $this->url; ?>" method="post" id="plg-form" enctype="multipart/form-data">
	<?php echo $this->project->provisioned == 1
				? $this->helper->showPubTitleProvisioned( $this->pub, $this->route)
				: $this->helper->showPubTitle( $this->pub, $this->route, $this->title); ?>
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
		<input type="hidden" name="license" id="license" value="<?php echo $this->license ? strtolower(urlencode($this->license->name)) : ''; ?>" />
		<input type="hidden" name="required" id="required" value="<?php echo in_array($this->active, $this->required) ? 1 : 0; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
		<?php if($this->project->provisioned == 1 ) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
	</fieldset>

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
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 	<div class="c-inner">
				<h4><?php echo $ptitle; ?> <?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span><?php } ?></h4>
				<?php if ($canedit) { ?>
				<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SELECT'); ?></p>
				<div id="c-show">
					<?php if(!$this->licenses) { echo '<p class="notice">'.JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NONE_FOUND').'</p>'; } else { ?>
					<ul id="c-browser">
						<?php foreach ($this->licenses as $lic) { ?>
						<li id="<?php echo 'lic-'.strtolower(urlencode($lic->name)); ?>" class="c-radio" title="<?php echo htmlentities($lic->title); ?>"><?php if($lic->icon) { echo '<img src="'.$lic->icon.'" alt="'.htmlentities($lic->title).'" />'; } ?><?php echo $lic->title; ?></li>
						<?php } ?>
					</ul>
					<?php if($this->pubconfig->get('suggest_licence')) { ?>
						<p class="hint"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_DONT_SEE_YOURS') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_YOU_CAN') ; ?> <a href="<?php echo $this->url . '?action=suggest_license' . a . 'version=' . $this->version; ?>" class="showinbox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGEST'); ?></a></p>
					<?php } ?>
					<?php } ?>
				</div>
				<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_LICENSE'); ?></p>
				<?php } else { ?>
					<p class="notice"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADVANCED_CANT_CHANGE').' <a href="'.$this->url.'/?action=newversion">'.ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_WHATS_NEXT_NEW_VERSION')).'</a>'; ?></p>
				<?php } ?>
			 </div>
		</div>
		<div class="two columns second" id="c-output">
		 <div class="c-inner">
			<?php if($canedit) { ?>
					<span class="c-submit"><input type="submit" class="btn" value="<?php if($this->move) { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_AND_CONTINUE'); } else { echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE_CHANGES'); } ?>" id="c-continue" /></span>
			<?php } ?>
			<h5><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_LICENSE')); ?>: </h5>
			<ul id="c-license" class="c-list">
				<li id="nosel" <?php if($this->license) { echo 'class="hidden"'; } ?> ><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NONE_SELECTED'); ?></li>
				<li id="c-sel-license" class="prominent<?php if(!$this->license) { echo ' hidden'; } ?>"><?php echo $this->license ? $this->license->title : ''; ?></li>
			</ul>

			<?php foreach ($this->licenses as $lic) { ?>
			<div id="extra-<?php echo strtolower(urlencode($lic->name)); ?>" class="c-extra<?php if(!$this->license or $lic->id != $this->row->license_type) { echo ' hidden'; } ?>">
				<?php if($lic->info) {
					$info = $lic->info;
					if($lic->url) {
						 $info .= ' <a href="'.$lic->url.'" rel="external">Read license terms &rsaquo;</a>';
					}
					?>
					<p class="pub-info"><?php echo $info; ?></p>
				<?php } ?>
				<?php if($lic->customizable && $lic->text) { ?>
					<label>
						<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_WRITE'); ?>
						<textarea name="license_text[<?php echo strtolower(urlencode($lic->name)); ?>]" id="license-text-<?php echo strtolower(urlencode($lic->name)); ?>" cols="50" rows="10" class="pubinput"><?php echo $lic->id == $this->row->license_type ? $this->row->license_text : $lic->text; ?></textarea>
					</label>
					<p class="hidden" id="template-<?php echo strtolower(urlencode($lic->name)); ?>"><?php echo $lic->text; ?></p>
					<p class="hint"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_REMOVE_DEFAULTS'); ?></p>
					<span class="mini pub-edit" id="reload-<?php echo strtolower(urlencode($lic->name)); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_RELOAD_TEMPLATE_TEXT'); ?></span>
				<?php } ?>
				<?php if ($lic->agreement == 1 && $canedit) {
					$txt = JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_AGREED').' '.$lic->title.' '.JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE');
					if($lic->url) {
						 $txt = preg_replace("/license terms/", '<a href="'.$lic->url.'" rel="external">license terms</a>', $txt);
					}
					$txt = preg_replace("/".$lic->title."/", '<strong>'.$lic->title.'</strong>', $txt);
					?>
					<label class="agreement"><input type="checkbox" name="agree[<?php echo strtolower(urlencode($lic->name)); ?>]" value="1" id="agree-<?php echo strtolower(urlencode($lic->name)); ?>" <?php echo $lic->id == $this->row->license_type ? 'checked="checked"' : '';  ?> />	<?php echo $txt; ?>.
					</label>
				<?php } ?>
			</div>
			<?php } ?>
		 </div>
		</div>
	</div>
</div>
</form>