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

if($this->reviewer == 'sponsored')
{
	$title = JText::_('COM_PROJECTS_REVIEW_PROJECT_SPS');			
	$approved = $this->params->get('grant_approval') || $this->params->get('grant_status') == 1 ? 1 : 0;
	
	$b_action = $approved ?
			JText::_('COM_PROJECTS_SAVE_SPS_APPROVED') :
			JText::_('COM_PROJECTS_SAVE_SPS') ;	
}
else {
	$title = $this->project->state == 5 
			? JText::_('COM_PROJECTS_REVIEW_PROJECT_HIPAA') 
			: JText::_('COM_PROJECTS_REVIEW_PROJECT_HIPAA_SAVE');
	$b_action = $this->project->state == 5  
			? JText::_('COM_PROJECTS_SAVE_HIPAA') 
			: JText::_('COM_PROJECTS_SAVE');
}

ximport('Hubzero_User_Profile');
$profile = Hubzero_User_Profile::getInstance($this->project->created_by_user);
$notes = ProjectsHtml::getAdminNotes($this->project->admin_notes, $this->reviewer);

?>
<?php if(!$this->ajax) { ?>
	<div id="content-header">
		<h2><?php echo $title ?></h2>
	</div>
<?php } ?>
<div id="abox-content" class="reviewer">
<?php if($this->ajax) { ?>
<h3><?php echo $title ?></h3>
<?php } ?>
<?php
// Display error  message
if ($this->getError()) { 
	echo ('<p class="error">'.$this->getError().'</p>');
} ?>

<?php if($this->project->id) { ?>
	
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&task=process') . '?reviewer=' . $this->reviewer; ?>" method="post" id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" >

	<fieldset>	
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="task" value="process" />
		<input type="hidden" name="ajax" value="1" />
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="reviewer" value="<?php echo $this->reviewer; ?>" />
		<input type="hidden" name="filterby" value="<?php echo $this->filterby; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	</fieldset>
	<div class="info_blurb">
		<div class="pthumb"><img src="<?php echo $this->thumb_src; ?>" alt="" /></div>
		<div class="pinfo">
			<p class="info_title">
			<?php echo $this->project->title; ?> (<span class="aliasname"><?php echo $this->project->alias; ?></span>)</p>
			<p class="info_title"><span class="italic"><?php echo JText::_('COM_PROJECTS_CREATED_BY') . ': ' . $profile->get('name'); ?></span></p>
		</div>
	</div>

	<?php if($this->reviewer == 'sponsored') 
	{ 
	?>
	<div id="spsinfo" class="faded mini">
		<table>
			<tr>
				<td>	
					<label><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
					 <input name="grant_title" maxlength="250" type="text" value="<?php echo $this->params->get('grant_title'); ?>"  />
					</label>
				</td>
				<td class="tdmini"></td>
				<td>
					<label><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
					 <input name="grant_PI" maxlength="250" type="text" value="<?php echo $this->params->get('grant_PI'); ?>"  />
					</label>
				</td>
			</tr>
			<tr>
				<td>	
					<label><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
					 <input name="grant_agency" maxlength="250" type="text" value="<?php echo $this->params->get('grant_agency'); ?>"  />
					</label>
				</td>
				<td class="tdmini"></td>
				<td>
					<label><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
					 <input name="grant_budget" maxlength="250" type="text" value="<?php echo $this->params->get('grant_budget'); ?>"  />
					</label>
				</td>
			</tr>
			<tr>
				<td>	
					<label class="<?php if($approved) { echo ' spsapproved'; } else { echo 'spsapproval'; } ?>"><?php echo $approved 
						? ucfirst(JText::_('COM_PROJECTS_APPROVAL_CODE_APPROVED')) 
						: JText::_('COM_PROJECTS_APPROVAL_CODE_PROVIDE'); ?>:
					 <input name="grant_approval" id="grant_approval" maxlength="250" type="text" value="<?php echo $this->params->get('grant_approval'); ?>"  />
					<?php if(!$approved) { ?>
					 <p class="hint mini"><?php echo JText::_('COM_PROJECTS_SPS_APPROVAL_HINT'); ?></p>
					<?php } ?>
					</label>
				</td>
				<td class="tdmini"><?php if(!$approved) { echo '<span class="or">' . JText::_('COM_PROJECTS_OR') . '</span>';  } ?></td>
				<td>
					<?php if(!$approved) { ?>
					<label class="dark">
						 <input class="option" name="rejected" id="rejected" type="checkbox" value="1" <?php if($this->params->get('grant_status') == 2) { echo 'checked="checked"'; } ?> />
						<?php echo $this->params->get('grant_status') == 2 ? JText::_('COM_PROJECTS_SPS_REJECTED_KEEP') : JText::_('COM_PROJECTS_SPS_REJECT'); ?>  
					</label>
					<?php } ?>
				</td>
			</tr>
		</table>
	</div>
	<?php } ?>
	<?php if($this->project->state == 5 && $this->reviewer == 'sensitive') { ?>	
	 <div>
		<label id="sdata-approve"><input class="option" name="approve" type="checkbox" value="1" /> <?php echo ucfirst(JText::_('COM_PROJECTS_APPROVE_PROJECT_CONFIRM')); ?></label>
	 </div>
	<?php } ?>
	
	<div id="newadmincomment">
		<h4><?php echo ucfirst(JText::_('COM_PROJECTS_ADD_ADMIN_COMMENT')); ?> <span class="optional"><?php echo JText::_('OPTIONAL'); ?></span></h4>
		<label>
			<textarea name="comment" rows="4" cols="40"></textarea>
		</label>
		<?php if($this->reviewer == 'sponsored' && !$approved) { ?>
		 <label><input class="option" name="notify" type="checkbox" value="1" /> <?php echo ucfirst(JText::_('COM_PROJECTS_REVIEWERS_ADD_ACTIVITY')); ?></label>
		<?php } ?>
	</div>
	<p class="submitarea">
		<input type="submit" value="<?php echo $b_action; ?>" class="btn" />
		<input type="reset" id="cancel-action" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
	</p>
	<div id="admincommentbox">
	<h4><?php echo ucfirst(JText::_('COM_PROJECTS_REVIEWER_COMMENTS')); ?> <span class="hint"> <?php echo ucfirst(JText::_('COM_PROJECTS_REVIEWER_COMMENTS_LATEST_FIRST')); ?></span></h4>
	<?php if($notes)  { ?>
		<?php echo $notes; ?>
	<?php } else {  ?>
		<p class="noresults">No comments found</p>
	<?php } ?>	
	</div>	
</form>
<?php } ?>
</div>