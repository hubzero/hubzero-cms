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
$privacy = $this->project->private ? JText::_('COM_PROJECTS_PRIVATE') : JText::_('COM_PROJECTS_PUBLIC');
$goto  = 'alias=' . $this->project->alias;

?>
<div id="project-wrap">
	<div class="main section">
		<?php echo ProjectsHtml::writeProjectHeader($this, 1); ?>
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
			<div id="edit-project-content" class="withleft">
					<h3 class="edit-title"><?php echo ucwords(JText::_('COM_PROJECTS_EDIT_PROJECT')); ?></h3>
				<div class="aside">
					<ul id="panelist">	
						<?php foreach($this->sections as $section) { ?>
						<li <?php if($section == $this->active) { echo 'class="activepane"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=edit'.a.$goto).'/?edit='.strtolower($section); ?>"><?php echo JText::_('COM_PROJECTS_EDIT_PROJECT_PANE_'.strtoupper($section)); ?></a></li>
						<?php } ?>
					</ul>
					<?php if($this->active != 'info') { ?>		
					<div class="tips">
						<h3><?php echo JText::_('COM_PROJECTS_TIPS'); ?></h3>
					<?php if($this->active == 'team') { ?>							
							<h4><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_TIPS'); ?></h4>
							<p><span class="italic prominent"><?php echo ucfirst(JText::_('COM_PROJECTS_LABEL_COLLABORATORS')); ?> </span><?php echo JText::_('COM_PROJECTS_CAN'); ?>:</p>
							<ul>
								<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_ONE'); ?></li>
								<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_TWO'); ?></li>
								<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_THREE'); ?></li>
							</ul>
							<p><span class="italic prominent"><?php echo ucfirst(JText::_('COM_PROJECTS_LABEL_OWNERS')); ?> </span><?php echo JText::_('COM_PROJECTS_CAN'); ?>:</p>
							<ul>
								<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_ONE'); ?></li>
								<li><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_TWO'); ?></li>
								<li><strong><?php echo JText::_('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_THREE'); ?></strong></li>
							</ul>
					<?php } 
					 else if ($this->active == 'settings') { ?>		
							<h4><?php echo JText::_('COM_PROJECTS_HOWTO_PUBLIC_PAGE'); ?></h4>
							<p><?php echo JText::_('COM_PROJECTS_HOWTO_PUBLIC_PAGE_EXPLAIN'); ?></p>			
						<?php if($this->config->get('grantinfo', 0)) { ?>
							<h5><?php echo JText::_('COM_PROJECTS_HOWTO_GRANTINFO_WHY'); ?></h5>
							<p><?php echo JText::_('COM_PROJECTS_HOWTO_GRANTINFO_BECAUSE'); ?></p>
						<?php } ?>
					<?php } ?>
					</div>
					<?php } ?>
				</div><!-- / .aside -->
			    <div id="edit-project" class="subject">
						<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' 
						. $this->option . a . 'task=edit' . a . $goto); ?>">
						 <div>
							<input type="hidden" id="pid" name="id" value="<?php echo $this->project->id; ?>" />
							<input type="hidden"  name="task" value="edit" />
							<input type="hidden"  name="save" value="1" />
							<input type="hidden"  name="edit" value="<?php echo $this->active; ?>" />
							<input type="hidden"  name="name" value="<?php echo $this->project->alias; ?>" />
						</div>
						<div>
					<?php
						switch ($this->active) 
						{
							case 'info': 
							default: 
					?>
					<h4><?php echo ucwords(JText::_('COM_PROJECTS_EDIT_INFO')); ?></h4>	
						<div>
							<table id="infotbl">
								<tbody>
									<tr>
										<td class="htd"><?php echo JText::_('COM_PROJECTS_ALIAS'); ?></td>
										<td><?php echo $this->project->alias; ?></td>
									</tr>
									<tr>
										<td class="htd"><?php echo JText::_('COM_PROJECTS_TITLE'); ?></td>
										<td><input name="title" maxlength="250" type="text" value="<?php echo $this->project->title; ?>" class="long" /></td>
									</tr>								
									<tr>
										<td class="htd"><?php echo JText::_('COM_PROJECTS_ABOUT'); ?></td>
										<td>
											<span class="clear"></span>	
											<?php
												ximport('Hubzero_Wiki_Editor');
												$editor =& Hubzero_Wiki_Editor::getInstance();
												echo $editor->display('about', 'about', $this->project->about, '', '10', '25');
											?>
											<?php if(!JPluginHelper::isEnabled('projects', 'apps') && !$this->publishing) { ?>
												<input type="hidden"  name="type" value="<?php echo $this->project->type; ?>" />
											<?php } ?>
										</td>
									</tr>

									<tr>
										<td class="htd"><?php echo JText::_('COM_PROJECTS_THUMB'); ?></td>
										<td><iframe class="filer filerMini" src="<?php echo 'index.php?option='.$this->option.'&amp;no_html=1&amp;task=img&amp;file='.stripslashes($this->project->picture).'&amp;id='.$this->project->id; ?>"></iframe></td>
									</tr>
			
								</tbody>
							</table>
							<p class="submitarea">
								<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_CHANGES'); ?>"  />
								<span class="btn btncancel"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto); ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a></span>
							</p>
						</div><!-- / .basic info -->
					<?php  
							break;
							case 'team': 
					?>
					<h4><?php echo ucwords(JText::_('COM_PROJECTS_EDIT_TEAM')); ?></h4>	
					<div id="cbody">
						<?php echo $this->content; ?>
					</div>
					<h5 class="terms-question"><?php echo JText::_('COM_PROJECTS_PROJECT') . ' ' . JText::_('COM_PROJECTS_OWNER'); ?>:</h5>	
					<?php 	if($this->project->owned_by_group) {	
							$group = Hubzero_Group::getInstance( $this->project->owned_by_group );			
							$ownedby = '<a href="/groups/'.$group->get('cn').'">'.JText::_('COM_PROJECTS_GROUP').' '.$group->get('cn').'</a>';
						}
						else {
						 $ownedby = '<a href="/members/'.$this->project->owned_by_user.'">'.$this->project->fullname.'</a>';	
					} echo '<span class="mini">' . $ownedby . '</span>'; ?>
					<?php  
							break;
							case 'settings': 
					?>
					<h4><?php echo ucwords(JText::_('COM_PROJECTS_EDIT_SETTINGS')); ?></h4>
					<h5 class="terms-question"><?php echo JText::_('COM_PROJECTS_ACCESS'); ?></h5>	
					<label><input class="option" name="private" type="radio" value="1" <?php if($this->project->private == 1) { echo 'checked="checked"'; }?> /> <?php echo JText::_('COM_PROJECTS_PRIVACY_EDIT_PRIVATE'); ?></label>
					<label><input class="option" name="private" type="radio" value="0" <?php if($this->project->private == 0) { echo 'checked="checked"'; }?> /> <?php echo JText::_('COM_PROJECTS_PRIVACY_EDIT_PUBLIC'); ?></label>
					<?php if($this->project->private == 0) { ?>
					<h5 class="terms-question"><?php echo JText::_('COM_PROJECTS_OPTIONS_FOR_PUBLIC'); ?></h5>
					<p class="hint"><?php echo JText::_('COM_PROJECTS_YOUR_PROJECT_IS'); ?> <span class="prominent urgency"><?php echo $privacy; ?></span></p>				
					<label>
						<input type="hidden"  name="params[team_public]" value="0" />
						<input type="checkbox" class="option" name="params[team_public]" value="1" <?php if ($this->params->get( 'team_public')) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_TEAM_PUBLIC'); ?>
					</label>
					
					<?php if ($this->publishing) { ?>
					<label>
						<input type="hidden"  name="params[publications_public]" value="0" />
						<input type="checkbox" class="option" name="params[publications_public]" value="1" <?php if ($this->params->get( 'publications_public')) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_PUBLICATIONS_PUBLIC'); ?>
					</label>
					<?php } ?>
					
					<?php 
					$plugin = JPluginHelper::getPlugin( 'projects', 'notes' );
					$pparams = new JParameter( $plugin->params );
					if ($pparams->get('enable_publinks')) { ?>
					<label>
						<input type="hidden"  name="params[notes_public]" value="0" />
						<input type="checkbox" class="option" name="params[notes_public]" value="1" <?php if ($this->params->get( 'notes_public')) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_NOTES_PUBLIC'); ?>
					</label>
					<?php } ?>
					
					<?php 
					$plugin = JPluginHelper::getPlugin( 'projects', 'files' );
					$pparams = new JParameter( $plugin->params );
					if ($pparams->get('enable_publinks')) { ?>
					<label>
						<input type="hidden"  name="params[files_public]" value="0" />
						<input type="checkbox" class="option" name="params[files_public]" value="1" <?php if ($this->params->get( 'files_public')) { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_FILES_PUBLIC'); ?>
					</label>
					<?php } ?>
					
					<?php } ?>
					<?php if($this->config->get('grantinfo', 0)) { ?>
					<h5 class="terms-question"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_INFO'); ?></h5>
					<?php 
						$approved = ($this->params->get( 'grant_status') == 1) ? 1 : 0;
						if($approved) 
						{ ?>
						<p class="notice notice_passed"><?php echo JText::_('COM_PROJECTS_GRANT_APPROVED_WITH_CODE'); ?> <span class="prominent"><?php echo htmlentities(html_entity_decode($this->params->get( 'grant_approval', 'N/A'))); ?></span></p>
					<?php } else { ?>
						<p><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY'); ?></p>
					<?php } ?>
					<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
					<?php if($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->params->get( 'grant_title', 'N/A'))) . '</span>'; } else {  ?>
					 <input name="params[grant_title]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_title'))); ?>" class="long" />	
					<?php } ?>
					</label>
					<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
					<?php if($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->params->get( 'grant_PI', 'N/A'))) . '</span>'; } else {  ?>
					 <input name="params[grant_PI]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_PI'))); ?>" class="long"  />
					<?php } ?>
					</label>
					<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
					<?php if($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->params->get( 'grant_agency', 'N/A'))) . '</span>'; } else {  ?>
					 <input name="params[grant_agency]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_agency'))); ?>" class="long"  />
					<?php } ?>
					</label>
					<label class="terms-label"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
					<?php if($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->params->get( 'grant_budget', 'N/A'))) . '</span>'; } else {  ?>
					 <input name="params[grant_budget]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_budget'))); ?>" class="long"  />
					<?php } ?>
					</label>
					<?php if(!$approved) { ?>
						<label><input class="option" name="params[grant_status]" type="checkbox" value="0" <?php if($this->params->get( 'grant_status') == 2) { echo 'checked="checked"'; } ?> /> <?php echo $this->params->get( 'grant_status') == 2 
						? JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_RESUBMIT_FOR_APPROVAL') 
						: JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_NOTIFY_ADMIN') ; ?></label>
					<?php } ?>
					<?php } ?>					
					<p class="submitarea">
						<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_CHANGES'); ?>"  />
						<span class="btn btncancel"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto); ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a></span>
					</p>
					<?php 
						break;
						}
					?>
					 </div>
					</form>
				</div><!-- / .subject -->
				<?php if($this->active == 'info') { ?>
				<div id="cancel-project">
					<p class="right_align"><?php echo JText::_('Need to cancel project? You have an option to permanently '); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.a.'task=delete'); ?>" id="delproject"><?php echo strtolower(JText::_('delete')); ?></a> <?php echo JText::_('your project.'); ?></p>
				</div>
				<?php } ?>
			</div><!-- / #edit-project-content -->
 </div><!-- / .main section -->
</div>