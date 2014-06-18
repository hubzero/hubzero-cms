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
		? ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_ACCESS'))
		: ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_SPECIFY_ACCESS')) ;
}
else
{
	$ptitle = ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PANEL_ACCESS'));
}

// Which access is selected?
$access = $this->row->access;
// 0 - public; 1 - registered; 2 - restricted; 3 - private

switch ($access)
{
	case 0: default: 	$accesstext = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PUBLIC'); 		break;
	case 1: 			$accesstext = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_REGISTERED'); 	break;
	case 2: case 3:		$accesstext = JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED'); 	break;
}

// Restricted to group?
$groups = '';
if ($this->access_groups)
{
	$i = 1;
	foreach ($this->access_groups as $gr)
	{
		$groups .= $gr->cn;
		$i++;
		$groups .= ', ';
	}
}

$canedit = (
	$this->pub->state == 3
	|| $this->pub->state == 4
	|| $this->pub->state == 5
	|| in_array($this->active, $this->mayupdate))
	? 1 : 0;

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
		<input type="hidden" name="access" id="access" value="<?php echo $access; ?>" />
		<input type="hidden" name="required" id="required" value="<?php echo in_array($this->active, $this->required) ? 1 : 0; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
		<?php if($this->project->provisioned == 1 ) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
	</fieldset>
<?php
	// Draw status bar
	$this->contribHelper->drawStatusBar($this);

// Section body starts:
$row = $this->row;
?>
<div id="pub-body">
	<div id="pub-editor">
		<div class="two columns first" id="c-selector">
		 <div class="c-inner">
			<h4><?php echo $ptitle; ?> <?php if (in_array($this->active, $this->required)) { ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span><?php } ?></h4>
			<?php if ($canedit) { ?>
			<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_SELECT'); ?></p>
			<div id="c-show">
				<ul id="c-browser">
					<li id="access-public" class="c-radio"><span class="prominent"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PUBLIC'); ?></span>: <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_PUBLIC_EXPLANATION'); ?> (<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_RECOMMENDED'); ?>)</li>
					<li id="access-registered" class="c-radio"><span class="prominent"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_REGISTERED'); ?></span>: <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_REGISTERED_EXPLANATION'); ?></li>
					<li id="access-restricted" class="c-radio"><span class="prominent"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED'); ?></span>: <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED_EXPLANATION'); ?></li>
				</ul>
			</div>
			<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_ACCESS'); ?></p>
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
			<h5><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_ACCESS')); ?>: </h5>
				<ul id="c-access" class="c-list">
					<li id="nosel" <?php if($access != '') { echo 'class="hidden"'; } ?> ><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_NONE_SELECTED'); ?></li>
					<li id="c-sel-access" class="prominent<?php if($access == '') { echo ' hidden'; } ?>"><?php echo $accesstext; ?></li>
				</ul>
				<div id="extra-0" class="c-extra<?php if($access == '' || $access != 0) { echo ' hidden'; } ?>">
					<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_ACCESS_PUBLIC'); ?></p>
				</div>
				<div id="extra-1" class="c-extra<?php if($access != 1) { echo ' hidden'; } ?>">
					<p class="pub-info"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_TIPS_ACCESS_REGISTERED'); ?></p>
				</div>
				<div id="extra-2" class="c-extra<?php if($access != 2 && $access != 3) { echo ' hidden'; } ?>">
					<h5><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED_TO'); ?></h5>
					<label>
						<input type="checkbox" checked="checked" disabled="disabled" name="sysgroup" value="1" />	<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_RESTRICTED_TO_SYSGROUP'); ?>
					</label>
					<p class="and"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_AND'); ?> <span class="optional">(<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_OPTIONAL'); ?>)</span></p>
					<label><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_SELECT_GROUPS'); ?>
						<input type="text" name="access_group" id="access-group" value="<?php echo $groups; ?>" class="block long" />
					</label>
					<label><input type="checkbox" name="private" value="1" <?php echo $access == 3 ? 'checked="checked"' : '';  ?> />	<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ACCESS_ALL_PRIVATE'); ?>
					</label>
				</div>
		 </div>
		</div>
		<div class="clear"></div>
	</div>
</div>
</form>