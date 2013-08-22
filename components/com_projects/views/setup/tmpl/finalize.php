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

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
$this->project->about = ProjectsHtml::cleanText($this->project->about);

$goto  = 'alias=' . $this->project->alias;

$title = $this->project->title ? JText::_('COM_PROJECTS_NEW_PROJECT').': '.$this->project->title : $this->title;

$privacylink = $this->config->get('privacylink', '/legal/privacy');
$hipaa = $this->config->get('HIPAAlink', 'http://www.hhs.gov/ocr/privacy/');
$ferpa = $this->config->get('FERPAlink', 'http://www2.ed.gov/policy/gen/reg/ferpa/index.html');

$html  = '';

?>
<div id="content-header" class="full">
	<h2><?php echo $title; ?> <?php if($this->gid && is_object($this->group)) { ?> <?php echo JText::_('COM_PROJECTS_FOR').' '.ucfirst(JText::_('COM_PROJECTS_GROUP')); ?> <a href="<?php echo JRoute::_('index.php?option=com_groups'.a.'gid='.$this->group->get('cn')); ?>"><?php echo Hubzero_View_Helper_Html::shortenText($this->group->get('description'), 50, 0); ?></a><?php } ?></h2>
</div><!-- / #content-header -->

<div class="main section" id="setup">
	<ul id="status-bar" class="moving">
		<li <?php if($this->stage == 0) { echo 'class="active"'; } ?>><?php if($this->project->setup_stage > 0 && $this->stage != 0) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.$goto).'/?step=0'; ?>"<?php if($this->project->setup_stage >= 1) { echo ' class="c_passed"'; } ?>><?php } ?><?php echo JText::_('COM_PROJECTS_DESCRIBE_PROJECT'); ?><?php if($this->project->setup_stage > 0 && $this->stage != 0) { ?></a><?php } ?></li>
		<li <?php if($this->stage == 1) { echo 'class="active"'; } ?>><?php if($this->project->setup_stage >= 1 && $this->stage != 1) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.$goto).'/?step=1'; ?>"<?php if($this->project->setup_stage >= 2) { echo ' class="c_passed"'; } ?>><?php } ?><?php echo JText::_('COM_PROJECTS_ADD_TEAM'); ?><?php if($this->project->setup_stage >= 1 && $this->stage != 1) { ?></a><?php } ?></li>
		<li class="active"><?php echo JText::_('COM_PROJECTS_SETUP_ONE_LAST_THING'); ?></li>
		<li><?php echo JText::_('COM_PROJECTS_READY_TO_GO'); ?></li>
	</ul>
<div class="clear"></div>
	<div class="info_blurb">
		<div class="pthumb"><img src="<?php echo $this->thumb_src; ?>" alt="" /></div>
		<div class="pinfo">
			<div class="two columns first">
			<p class="info_title"><span class="block italic"><?php echo $this->typetitle.' '.strtolower(JText::_('COM_PROJECTS_PROJECT')); ?>:</span> <?php echo $this->project->title; ?> (<span class="aliasname"><?php echo $this->project->alias; ?></span>)</p>
			<?php if ($this->project->about && $this->project->about != '') { ?>
			<p class="mini"><?php echo Hubzero_View_Helper_Html::shortenText($this->project->about, 100, 0); ?></p>
			<?php } ?>
			<p class="actionlink"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.$goto).'/?step=0'; ?>">&laquo; <?php echo JText::_('COM_PROJECTS_CHANGE_THIS_INFO'); ?></a></p>
			</div>
			<div class="two columns second">
				<p class="info_title"><span class="italic"><?php echo JText::_('COM_PROJECTS_TEAM'); ?>:</span></p>
				<p class="mini"><?php echo $this->team; ?></p>
				<p class="actionlink"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=setup'.a.$goto).'/?step=1'; ?>">&laquo; <?php echo JText::_('COM_PROJECTS_CHANGE_THIS_INFO'); ?></a></p>
			 </div>
		</div>
		<div class="clear"></div>
	</div>
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
	<form id="hubForm" method="post" action="index.php">
			<?php if($this->config->get('grantinfo', 0)) { ?>
			<div class="explaination">
				<h4><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANTINFO_WHY'); ?></h4>
				<p><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANTINFO_BECAUSE'); ?></p>
			</div>
			
			<fieldset class="wider">
			<h2><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_INFO'); ?></h2>
			<p class="notice"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY'); ?></p>
			<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
			 <input name="grant_title" maxlength="250" type="text" value="<?php echo $this->params->get('grant_title'); ?>" class="long" />
			</label>
			<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
			 <input name="grant_PI" maxlength="250" type="text" value="<?php echo $this->params->get('grant_PI'); ?>" class="long" />
			</label>
			<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
			 <input name="grant_agency" maxlength="250" type="text" value="<?php echo $this->params->get('grant_agency'); ?>" class="long" />
			</label>
			<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
			 <input name="grant_budget" maxlength="250" type="text" value="<?php echo $this->params->get('grant_budget'); ?>" class="long" />
			</label>
			</fieldset>
			<div class="clear"></div>
			<?php } ?>
		<div class="explaination">
			<?php if($this->config->get('restricted_data', 0)) { ?>
			<h4><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE'); ?></h4>
			<p><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE_EXPLAIN'); ?> </p>
			<p><?php echo JText::_('COM_PROJECTS_SETUP_MORE_ON'); ?><a href="<?php echo $hipaa; ?>" rel="external" > <?php echo JText::_('COM_PROJECTS_SETUP_TERMS_HIPAA'); ?></a>. <?php echo JText::_('COM_PROJECTS_SETUP_MORE_ON'); ?><a href="<?php echo $ferpa; ?>" rel="external" > <?php echo JText::_('COM_PROJECTS_SETUP_TERMS_FERPA'); ?></a>.</p>
			<p class="info"><?php echo JText::_('COM_PROJECTS_ERROR_SETUP_TERMS_NOTE'); ?></p>
			<?php } else { ?>
				<h4><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PRIVACY_WHY'); ?></h4>
				<p><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PRIVACY_BECAUSE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo JText::_('COM_PROJECTS_SETUP_TERMS'); ?></a>.</p>
			<?php } ?>
		</div>
		<fieldset class="wider">
			<input type="hidden"  name="task" value="setup" />
			<input type="hidden"  name="step" value="2" />
			<input type="hidden"  name="save_stage" value="3" />
			<input type="hidden" id="option" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" id="pid" name="id" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" id="tempid" name="tempid" value="<?php echo $this->tempid; ?>" />
			<input type="hidden" id="gid" name="gid" value="<?php echo $this->gid; ?>" />
			<h2><?php echo JText::_('COM_PROJECTS_SETUP_BEFORE_COMPLETE'); ?></h2>
			<p><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_WHY_ASK'); ?></p>
			<?php if($this->config->get('restricted_data', 0) == 1) { ?>
			<h4 class="terms-question"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span></h4>
			<label class="terms-label dark">
				<input class="option restricted-answer" name="restricted" id="restricted-yes" type="radio" value="yes" <?php if($this->params->get('restricted_data') == 'yes') { echo 'checked="checked"'; } ?> />
				<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_YES'); ?>
			</label>
			<div class="ipadded" id="restricted-choice">
				<p class="hint prominent"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_MAKE_CHOICE'); ?></p>
				<label class="terms-label">
					<input class="option restricted-opt" name="export" id="export" type="checkbox" value="yes" <?php if($this->params->get('export_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
					<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_EXPORT_CONTROLLED'); ?>
				</label>
				<div id="stop-export" class="stopaction hidden"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_EXPORT'); ?></div>
				<label class="terms-label">
					<input class="option restricted-opt" name="irb" id="irb" type="checkbox" value="yes" <?php if($this->params->get('irb_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
					<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_IRB'); ?>
				</label>
				<div id="stop-irb" class="extraaction hidden">
					<h5><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE'); ?></h5>
					<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB'); ?>
					<label>
						<input class="option" name="agree_irb" id="agree_irb" type="checkbox" value="1"  />
						<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB_AGREE'); ?>
					</label>
				</div>
				<label class="terms-label">
					<input class="option restricted-opt" name="hipaa" id="hipaa" type="checkbox" value="yes" <?php if($this->params->get('hipaa_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
					<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_HIPAA'); ?>
				</label>
				<div id="stop-hipaa" class="stopaction hidden"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_HIPAA'); ?></div>
				<label class="terms-label">
					<input class="option restricted-opt" name="ferpa" id="ferpa" type="checkbox" value="yes" <?php if($this->params->get('ferpa_data') == 'yes' ) { echo 'checked="checked"'; } ?> />
					<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_FERPA'); ?>
				</label>
				<div id="stop-ferpa" class="extraaction hidden">
					<h5><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE'); ?></h5>
					<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA'); ?>
					<label>
						<input class="option" name="agree_ferpa" id="agree_ferpa" type="checkbox" value="1"  />
						<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA_AGREE'); ?>
					</label>
				</div>
			</div>
			<label class="terms-label dark">
				<input class="option restricted-answer" name="restricted" id="restricted-no" type="radio" value="no" <?php if($this->params->get('restricted_data') == 'no' ) { echo 'checked="checked"'; } ?> />
				<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_NO'); ?>
			</label>			
			
			<?php } ?>
			<?php if($this->config->get('restricted_data', 0) == 2) { ?>
			<h4 class="terms-question"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?><span class="required"><?php echo JText::_('REQUIRED'); ?></span></h4>
			<label class="terms-label dark">
				<input class="option" name="restricted" type="checkbox" value="no" />
				<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_SENSITIVE_DATA_AGREE'); ?>
			</label>	
			<?php } ?>
			<h4 class="terms-question"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo JText::_('COM_PROJECTS_SETUP_TERMS'); ?></a>? <span class="required"><?php echo JText::_('REQUIRED'); ?></span></h4>
			<label class="terms-label">
				<input class="option" name="agree" type="checkbox" value="1" />
				<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_AGREE'); ?> <a href="<?php echo $privacylink; ?>" rel="external" ><?php echo JText::_('COM_PROJECTS_SETUP_TERMS'); ?></a> <?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE_PROJECT'); ?> <span class="prominent"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_ALL_MEMBERS'); ?></span>.
			</label>
			<p class="submitarea"><input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>" class="btn" id="btn-finalize" /></p>
		</fieldset>
	</form>
	<div class="clear"></div>
</div>