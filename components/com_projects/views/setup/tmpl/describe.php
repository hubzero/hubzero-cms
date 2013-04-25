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
$html  = '';

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
$this->project->about = ProjectsHtml::cleanText($this->project->about);

$title = $this->project->title ? JText::_('COM_PROJECTS_NEW_PROJECT').': '.$this->project->title : $this->title;
?>
<div id="content-header">
	<h2><?php echo $title; ?> <?php if($this->gid && is_object($this->group)) { ?> <?php echo JText::_('COM_PROJECTS_FOR').' '.ucfirst(JText::_('COM_PROJECTS_GROUP')); ?> <a href="<?php echo JRoute::_('index.php?option=com_groups'.a.'gid='.$this->group->get('cn')); ?>"><?php echo Hubzero_View_Helper_Html::shortenText($this->group->get('description'), 50, 0); ?></a><?php } ?></h2>
</div><!-- / #content-header -->
<div class="main section" id="setup">
	<ul id="status-bar" class="moving">
		<li <?php if($this->stage == 0) { echo 'class="active"'; } ?>><?php if($this->project->setup_stage > 0 && $this->stage != 0) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.'alias='.$this->project->alias).'/?step=0'; ?>"<?php if($this->project->setup_stage == 1) { echo 'class=" c_passed"'; } ?>><?php } ?><?php echo JText::_('COM_PROJECTS_DESCRIBE_PROJECT'); ?><?php if($this->project->setup_stage > 0 && $this->stage != 0) { ?></a><?php } ?></li>
		<li <?php if($this->stage == 1) { echo 'class="active"'; } ?>><?php if($this->project->setup_stage >= 1 && $this->stage != 1) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.'alias='.$this->project->alias).'/?step=1'; ?>"<?php if($this->project->setup_stage >= 2) { echo ' class="c_passed"'; } ?>><?php } ?><?php echo JText::_('COM_PROJECTS_ADD_TEAM'); ?><?php if($this->project->setup_stage >= 1 && $this->stage != 1) { ?></a><?php } ?></li>
		<li><?php echo JText::_('COM_PROJECTS_READY_TO_GO'); ?></li>
	</ul>
<div class="clear"></div>
	<div class="status-msg">
	<?php 
		// Display error or success message
		if ($this->getError()) { 
			echo ('<p class="witherror">' . $this->getError().'</p>');
		}
		else if($this->msg) {
			echo ('<p>' . $this->msg . '</p>');
		} ?>
	</div>
<?php

	$html .= t.' <form id="hubForm" method="post" action="index.php">'.n;
	$html .= t.'<div class="explaination">'.n;
	$html .= t.t.'<h4>'.JText::_('COM_PROJECTS_HOWTO_TITLE_NAME_PROJECT').'</h4>'.n;
	$html .= t.t.'<p>'.JText::_('COM_PROJECTS_HOWTO_NAME_PROJECT').'</p>'.n;
	$html .= t.'</div>'.n;	
	$html .= t.t.'<fieldset class="oldstyle">'.n;
	$html .= t.t.'<h2>'.JText::_('COM_PROJECTS_PICK_NAME').'</h2>'.n;
	$html .= t.t.t.'<input type="hidden"  name="task" value="setup" />'.n;
	$html .= t.t.t.'<input type="hidden"  name="save_stage" id="save_stage" value="1" />'.n;
	$html .= t.t.t.'<input type="hidden"  name="step" value="'.$this->requested_step.'" />'.n;
	$html .= t.t.t.'<input type="hidden" id="option" name="option" value="'.$this->option.'" />'.n;
	$html .= t.t.t.'<input type="hidden" id="pid" name="id" value="'.$this->project->id.'" />'.n;
	$html .= t.t.t.'<input type="hidden" name="pid" value="'.$this->project->id.'" />'.n;
	$html .= t.t.t.'<input type="hidden" id="tempid" name="tempid" value="'.$this->tempid.'" />'.n;	
	$html .= t.t.t.'<input type="hidden" id="gid" name="gid" value="'.$this->gid.'" />'.n;	
	$html .= t.t.t.'<input type="hidden" name="restricted" value="'.$this->restricted.'" />'.n;
	$html .= t.t.t.'<input type="hidden" id="verified" name="verified" value="'.$this->verified.'" />'.n;
	$html .= t.t.t.'<input type="hidden" id="extended" name="extended" value="0" />'.n;	
	$html .= t.t.t.'<div id="verificationarea_title"></div>'.n;
	$html .= t.t.t.'<label>'.JText::_('COM_PROJECTS_TITLE').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
	$html .= t.t.t.'<input name="title" maxlength="250" id="ptitle" type="text" value="'.$this->project->title.'" /></label>'.n;
	$html .= t.t.t.'<p class="hint">'.JText::_('COM_PROJECTS_HINTS_TITLE').'</p>'.n;
	$html .= t.t.t.'<div id="verificationarea">';
	if($this->project->id) {
		$html .= t.t.t.t.'<p class="verify_passed">'.JText::_('COM_PROJECTS_NAME_RESERVED').' &rarr;</p>';	
	}
	$html .= t.t.t.'</div>'.n;
	$html .= t.t.t.'<label>'.JText::_('COM_PROJECTS_ALIAS_NAME').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
	$html .= t.t.t.'<input name="name" maxlength="20" id="name" type="text" value="'.$this->project->alias.'" /></label>'.n;
	$html .= t.t.t.'<p class="hint">'.JText::_('COM_PROJECTS_HINTS_NAME').'</p>'.n;
	$html .= t.'<div id="moveon" class="nogo">'.n;
	$html .= t.t.t.'<p class="submitarea"><input type="submit" value="'.JText::_('COM_PROJECTS_SAVE_AND_CONTINUE').'" class="btn disabled" disabled="disabled" /></p>'.n;
	$html .= t.'</div>'.n;
	$html .= t.'<div id="describe">'.n;
	$html .= t.t.'<h2>'.JText::_('COM_PROJECTS_DESCRIBE_PROJECT').'</h2>'.n;
	$html .= t.t.'<p class="question">'.JText::_('COM_PROJECTS_QUESTION_DESCRIBE_NOW_OR_LATER').'</p>'.n;
	$html .= t.t.'<p>'.n;
	$html .= t.t.t.'<span class="btn yesbtn">';
	$html .= '<a href="index.php?option='.$this->option.a.'task=setup'.a.'id='.$this->project->id.a.'extended=1#ext" id="next_desc">'.JText::_('COM_PROJECTS_QUESTION_DESCRIBE_YES').'</a>';
	$html .= '</span>'.n;
	$html .= t.t.t.'<span class="btn nobtn">';
	$html .= '<a href="index.php?option='.$this->option.a.'task=setup'.a.'id='.$this->project->id.a.'gonext=1" id="next_step">'.JText::_('COM_PROJECTS_QUESTION_DESCRIBE_NO').'</a>';
	$html .= '</span>'.n;
	$html .= t.t.'</p>'.n;
	$html .= t.'</div>'.n;
	$html .= t.t.'</fieldset>'.n;
	$html .= t.t.'<div class="clear"></div>'.n;
	$html .= t.t.'<div id="describearea">'.n;
	$html .= t.t.'<div class="explaination">'.n;
	$html .= t.t.t.'<h4>'.JText::_('COM_PROJECTS_HOWTO_TITLE_DESC').'</h4>'.n;
	$html .= t.t.t.'<p>'.JText::_('COM_PROJECTS_HOWTO_DESC_PROJECT').'</p>'.n;
	$html .= t.t.t.'<h4>'.JText::_('COM_PROJECTS_HOWTO_TITLE_THUMB').'</h4>'.n;
	$html .= t.t.t.'<p>'.JText::_('COM_PROJECTS_HOWTO_THUMB').'</p>'.n;
	$html .= t.t.'</div>'.n;
	$html .= t.t.'<fieldset>'.n;
	$html .= t.t.'<a name="ext"></a>'.n;
	$html .= t.t.'<h2>'.JText::_('COM_PROJECTS_DESCRIBE_PROJECT').'</h2>'.n;
	$html .= t.t.t.'<label>'.JText::_('COM_PROJECTS_ABOUT'). ': <span class="optional">'.JText::_('OPTIONAL').'</span>';
	//$html .= t.t.t.'<span class="hint rightfloat">'.JText::_('COM_PROJECTS_PLEASE_USE').' <a href="/topics/Help:WikiFormatting" rel="external">'.JText::_('COM_PROJECTS_WIKI_FORMATTING').'</a> '.JText::_('COM_PROJECTS_WIKI_TO_COMPOSE').'</span> '.n;
	$html .= t.t.t.'<span class="clear"></span>'.n;
	if($this->project->id) {
		//$html .= t.t.t.'<p id="previewit" class="previewit showaslink">'.JText::_('COM_PROJECTS_PREVIEW').'</p>'.n;
		ximport('Hubzero_Wiki_Editor');
		$editor =& Hubzero_Wiki_Editor::getInstance();
		$html .= $editor->display('about', 'about', $this->project->about, '', '10', '25');
	}
	else {
		$html .= t.t.t.'<textarea name="about" id="about" rows="10" cols="25">'.$this->project->about.'</textarea>'.n;	
	}
	$html .= t.t.t.'</label>'.n;
	$html .= t.t.t.'<label>'.JText::_('COM_PROJECTS_THUMBNAIL').':'.n;
	$html .= t.t.t.'<iframe class="filer filerMini" frameBorder="0" src="index.php?option='.$this->option.'&amp;no_html=1&amp;task=img&amp;file='.stripslashes($this->project->picture).'&amp;id='.$this->project->id.'&amp;tempid='.$this->tempid.'"></iframe>'."\n";			
	$html .= t.t.t.'</label>'.n;
	// Privacy
	$html .= t.t.'<h2 class="setup-h">'.JText::_('COM_PROJECTS_SETTING_APPEAR_IN_SEARCH').'</h2>'.n;
	$html .= t.t.t.'<label>'.n;
	$html .= t.t.t.t.'<input class="option" name="private" type="radio" value="1" ';
	$html .= $this->project->private == 1 ? 'checked="checked"' : '';
	$html .= ' /> '.JText::_('COM_PROJECTS_PRIVACY_EDIT_PRIVATE').'</label>'.n;
	$html .= t.t.t.'<label>'.n;
	$html .= t.t.t.t.'<input class="option" name="private" type="radio" value="0" ';
	$html .= $this->project->private == 0 ? 'checked="checked"' : '';
	$html .=' /> '.JText::_('COM_PROJECTS_PRIVACY_EDIT_PUBLIC').'</label>'.n;
	$html .= t.t.t.'<input type="hidden" name="type" value="1" />'.n;	
	$html .= t.t.t.'<p class="submitarea"><input type="submit" value="'.JText::_('COM_PROJECTS_SAVE_AND_CONTINUE').'" class="btn" /></p>'.n;
	$html .= t.t.'</fieldset>'.n;
	$html .= t.t.'</div>'.n;
	$html .= t.' </form>'.n;	
	echo $html;
?>
	<div class="clear"></div>
</div>
