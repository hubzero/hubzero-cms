<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b. %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M. Y';
	$tz = false;
}

// Connections enabled?
$plugin = JPluginHelper::getPlugin( 'projects', 'files' );
$p_params = new JParameter($plugin->params);

$service = 'google';
$cEnabled = $p_params->get('enable_' . $service, 0);
$connected = $this->params->get($service . '_token');

JToolBarHelper::title( '<a href="index.php?option=com_projects">'.JText::_( 'Projects' ).'</a>: '.stripslashes($this->obj->title).' <small><small>('.$this->obj->alias.', #'.$this->obj->id.')</small></small>', 'addedit.png' );
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::cancel();

$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;

// Get creator profile
$profile =& Hubzero_Factory::getProfile();
$profile->load( $this->obj->created_by_user );

// Determine status & options
$status = '';
$row = $this->obj;
if($row->state == 1 && $row->setup_stage >= $setup_complete) {
	$status   = '<span class="active">'.JText::_('COM_PROJECTS_ACTIVE').'</span> '.JText::_('COM_PROJECTS_SINCE').' '.JHTML::_('date', $row->created, $dateFormat, $tz);
}
else if($row->state == 2) {
	$status  = '<span class="deleted">'.JText::_('COM_PROJECTS_DELETED').'</span> ';
}
else if ($row->setup_stage < $setup_complete) {
	$status  = '<span class="setup">'.JText::_('Setup').'</span> '.JText::_('in progress');
}
else if($row->state == 0) {
	$text = $this->suspended ? JText::_('COM_PROJECTS_SUSPENDED') : JText::_('COM_PROJECTS_INACTIVE');
	$status = '<span class="inactive">'.$text.'</span> ';
	if($this->suspended) {
		$status .= $this->suspended == 1 ? ' ('.JText::_('COM_PROJECTS_BY_ADMIN').')' : ' ('.JText::_('COM_PROJECTS_BY_PROJECT_MANAGER').')';
	}
}
else if($row->state == 5) {
	$status  = '<span class="inactive">'.JText::_('COM_PROJECTS_PENDING_APPROVAL').'</span> ';
}

$sysgroup = $this->config->get('group_prefix', 'pr-').$this->obj->alias;
$quota = $this->params->get('quota');
$quota = $quota ? $quota : ProjectsHtml::convertSize( floatval($this->config->get('defaultQuota', '1')), 'GB', 'b');

$pubQuota = $this->params->get('pubQuota');
$pubQuota = $pubQuota ? $pubQuota : ProjectsHtml::convertSize( floatval($this->config->get('pubQuota', '1')), 'GB', 'b');

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher =& JDispatcher::getInstance();

?>
<script type="text/javascript">

function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	if(pressbutton == 'delete') {
		form.admin_action.value = 'delete';
		submitform( 'save' );
		return;
	}
	
	if(pressbutton == 'suspend') {
		form.admin_action.value = 'suspend';
		submitform( 'save' );
		return;
	}
	
	if(pressbutton == 'reinstate') {
		form.admin_action.value = 'reinstate';
		submitform( 'save' );
		return;
	}

	// do field validation
	if (form.title.value == ''){
		alert( 'Project must have a title' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="projectForm" class="editform">
	<table>
	 <tr>
	  <td class="holdtbl">
		<table class="statustable">
			<caption><?php echo JText::_('COM_PROJECTS_BASIC_INFO'); ?></caption>
			<tbody>
				<tr>
					<td class="key"><label for="title"><?php echo JText::_('COM_PROJECTS_TITLE'); ?>:</label></td>
					<td><input type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo htmlentities(stripslashes($this->obj->title), ENT_COMPAT, 'UTF-8', ENT_QUOTES); ?>" /></td>
				</tr>
				<tr>
					<td class="key"><label for="alias"><?php echo JText::_('COM_PROJECTS_ALIAS'); ?>:</label></td>
					<td><span><?php echo stripslashes($this->obj->alias); ?></span></td>
				</tr>
				<tr>
					<td class="key"><label for="about"><?php echo JText::_('COM_PROJECTS_ABOUT'); ?>:</label></td>
					<td><textarea name="about" id="about" rows="10" cols="50"><?php echo $this->obj->about; ?></textarea></td>
				</tr>
				<tr>
					<td class="key"><label for="tags"><?php echo JText::_('COM_PROJECTS_TAGS'); ?>:</label></td>
					<td>
							<?php 
							$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)) );

							if (count($tf) > 0) {
								echo $tf[0];
							} else { ?>
								<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->tags); ?>" />
							<?php } ?>	
					</td>
				</tr>
				<?php if(JPluginHelper::isEnabled('projects', 'apps') or $this->publishing) { ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_TYPE'); ?></td>
					<td>	
						<select name="type">
							<?php foreach($this->types as $type) { 
								if(($type->id == 3 && !$this->publishing) || 
								($type->id == 2 && !JPluginHelper::isEnabled('projects', 'apps'))) {
									continue;
								}
								$selected = $type->id == $this->obj->type ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $type->id; ?>" <?php echo $selected; ?>><?php echo $type->type ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_OWNER'); ?>:</td>
					<td>	
						<?php 	if($this->obj->owned_by_group) {	
								$group = Hubzero_Group::getInstance( $this->obj->owned_by_group );
								if($group) {
									$ownedby = '<span class="i_group">'.$group->get('cn').'</span>';	
								}	
								else {
									$ownedby = '<span class="pale">'.JText::_('COM_PROJECTS_INFO_DELETED_GROUP').'</span>';
								}		
							}
							else {
								$profile = Hubzero_User_Profile::getInstance($this->obj->owned_by_user);
								$ownedby = $profile->get('name') ? $profile->get('name') : JText::_('COM_PROJECTS_INFO_UNKNOWN_USER'); 
								$ownedby = '<span class="i_user">'.$ownedby.'</span>';		

						} echo $ownedby; ?>
						</td>
				</tr>
				<tr>
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_SYS_GROUP'); ?>:</label></td>
					<td><?php echo $sysgroup; ?></td>
				</tr>
				<tr class="division">
					<td colspan="2" class="centeralign"><input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_EDITS'); ?>" class="btn"  /></td>
				</tr>
			</tbody>
		</table>
		
		<table class="statustable">
			<caption><?php echo JText::_('COM_PROJECTS_PARAMETERS'); ?></caption>
			<tbody>
				<tr>
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_PRIVACY'); ?>:</label></td>
					<td colspan="2">
						<select name="private">
							<option value="0" <?php if($this->obj->private == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_PROJECTS_PUBLIC'); ?></option>
							<option value="1" <?php if($this->obj->private == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_PROJECTS_PRIVATE'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"></td>
					<td class="checkoption" ><input type="hidden"  name="params[team_public]" value="0" />
						<input type="checkbox" name="params[team_public]" value="1" <?php if ($this->params->get( 'team_public')) { echo ' checked="checked"'; } ?> /></td>
					<td><?php echo JText::_('COM_PROJECTS_TEAM_PUBLIC'); ?></td>
				</tr>
				<?php if($this->config->get('restricted_data', 0)) { ?>
				<tr class="division">
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_SENSITIVE_DATA'); ?></label></td>
					<td colspan="2"><?php echo strtoupper($this->params->get( 'restricted_data', 'no')); ?>
					<?php if($this->params->get( 'restricted_data') == 'yes') { ?> (
						<?php if($this->params->get( 'hipaa_data')  == 'yes') { echo 'HIPAA'; } ?>
						<?php if($this->params->get( 'ferpa_data')  == 'yes') { echo 'FERPA'; } ?>
						<?php if($this->params->get( 'export_data') == 'yes') { echo 'Export Controlled'; } ?>
						<?php if($this->params->get( 'irb_data') == 'yes') { echo 'IRB'; } ?>
						)
					<?php } ?></td>
				</tr>	
				<?php } ?>
				<?php if($this->config->get('grantinfo', 0)) { ?>
				<tr>
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_TITLE'); ?>:</label></td>
					<td colspan="2"><input name="params[grant_title]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_title'))); ?>" class="long" /></td>
				</tr>
				<tr>
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_PI'); ?>:</label></td>
					<td colspan="2"><input name="params[grant_PI]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_PI'))); ?>" class="long" /></td>
				</tr>
				<tr class="division">
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_AGENCY'); ?>:</label></td>
					<td colspan="2"><input name="params[grant_agency]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_agency'))); ?>" class="long" /></td>
				</tr>
				<tr class="division">
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_BUDGET'); ?>:</label></td>
					<td colspan="2"><input name="params[grant_budget]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->params->get( 'grant_budget'))); ?>" class="long" /></td>
				</tr>
				<tr class="division">
					<td class="key"><label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_APPROVAL_CODE'); ?>:</label></td>
					<td colspan="2"><?php echo htmlentities(html_entity_decode($this->params->get( 'grant_approval'))); ?></td>
				</tr>
				<?php } ?>
				<tr class="division">
					<td colspan="3" class="centeralign"><input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_EDITS'); ?>" class="btn"  /></td>
				</tr>
			</tbody>
		</table>
		
		<table class="statustable">
			<caption><?php echo JText::_('COM_PROJECTS_FILES'); ?></caption>
			<tbody>
				<tr>
					<td class="key"><label><?php echo JText::_('Files Quota'); ?>:</label></td>
					<td><input name="params[quota]" maxlength="100" type="text" value="<?php echo ProjectsHtml::convertSize($quota, 'b', 'GB', 2); ?>" class="short" /> <?php echo ' ('.JText::_('COM_PROJECTS_FILES_GBYTES').')'; ?></td>
					<td></td>
				</tr>
				<tr>
					<td class="key"><label><?php echo JText::_('Publications Quota'); ?>:</label></td>
					<td><input name="params[pubQuota]" maxlength="100" type="text" value="<?php echo ProjectsHtml::convertSize($pubQuota, 'b', 'GB', 2); ?>" class="short" /> <?php echo ' ('.JText::_('COM_PROJECTS_FILES_GBYTES').')'; ?></td>
					<td></td>
				</tr>
				<?php if($this->diskusage) { ?>
					<tr><td colspan="3" style="width:100%;"><?php echo $this->diskusage; ?></td></tr>
				<?php } ?>
				<tr class="division">
					<td colspan="3" class="centeralign"><input type="submit" value="<?php echo JText::_('COM_PROJECTS_CHANGE_QUOTA'); ?>" class="btn"  />
				</td>
				</tr>
				<tr>
					<td colspan="3"><?php echo JText::_('Maintenance options:'); ?> &nbsp; <a href="index.php?option=com_projects&amp;task=gitgc&amp;id=<?php echo $this->obj->id; ?>"><?php echo JText::_('git gc --aggressive'); ?></a> [<?php echo JText::_('Takes minutes to run'); ?>]</td>
				</tr>
				<?php if($cEnabled) { ?>
				<tr>
					<td colspan="3"><?php echo JText::_('Connections'); ?>: <span style="font-weight:bold;"><?php echo $connected ? $service : 'not connected'; ?></span> &nbsp; <?php if ($connected) { ?><a href="index.php?option=com_projects&amp;task=fixsync&amp;id=<?php echo $this->obj->id; ?>"><?php echo JText::_('download sync log'); ?></a> &nbsp; [<?php echo JText::_('Also fixes stalled sync'); ?>] <?php } ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	  </td>
	  <td class="holdtbl">
		<table class="statustable">
			<caption><?php echo JText::_('COM_PROJECTS_STATUS'); ?></caption>
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_CREATED'); ?>:</td>
					<td><?php echo $this->obj->created; ?> <?php echo JText::_('COM_PROJECTS_BY').' '.$profile->get('name').' ('.$profile->get('username').')'; ?></td>
				</tr>
				<tr>
					<td class="key"></td>
					<td><?php echo $status; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_MESSAGE'); ?>:</td>
					<td>
						<textarea name="message" id="message" rows="5" cols="50"></textarea>				
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_OPTIONS'); ?>:</td>
					<td>
						<input type="hidden" name="admin_action" value="" />
						<input type="submit" value="<?php echo JText::_('COM_PROJECTS_OPTION_SEND_MESSAGE'); ?>" class="btn" id="do-message" /> <span class="breaker"> | </span> 
					<?php if($row->state == 1 && $row->setup_stage >= $setup_complete) { ?>
						<input type="submit" value="<?php echo JText::_('COM_PROJECTS_OPTION_SUSPEND'); ?>" class="btn" id="do-suspend" onclick="javascript: submitbutton('suspend')" />
					<?php } else if($row->state == 2 || ($row->state == 0 && $row->setup_stage >= $setup_complete)) { ?>
						<input type="submit" value="<?php echo $this->suspended ? JText::_('COM_PROJECTS_OPTION_REINSTATE') : JText::_('COM_PROJECTS_OPTION_ACTIVATE'); ?>" class="btn" id="do-reisnate" onclick="javascript: submitbutton('reinstate')" />
					<?php } ?>
					<?php if($row->state != 2) { ?>
						<input type="submit" value="<?php echo JText::_('COM_PROJECTS_OPTION_DELETE'); ?>" class="btn" id="do-delete" onclick="javascript: submitbutton('delete')" />
					<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
		
		<table class="statustable">
			<caption><?php echo JText::_('COM_PROJECTS_TEAM').' ('.$this->counts['team'].')'; ?></caption>
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_MANAGERS'); ?>:</td>
					<td><?php echo $this->managers ? $this->managers : JText::_('COM_PROJECTS_NA'); ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_COLLABORATORS'); ?>:</td>
					<td><?php echo $this->members ? $this->members : JText::_('COM_PROJECTS_NA'); ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_AUTHORS'); ?>:</td>
					<td><?php echo $this->authors ? $this->authors : JText::_('COM_PROJECTS_NA'); ?></td>
				</tr>
			</tbody>
		</table>
		
		<table class="statustable">
			<caption><?php echo JText::_('COM_PROJECTS_STATS'); ?></caption>
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_FILES'); ?>:</td>
					<td><?php echo $this->counts['files']; ?></td>
				</tr>
				<?php if(JPluginHelper::isEnabled('projects', 'apps') && isset($this->counts['apps'])) { ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_APPS'); ?>:</td>
					<td><?php echo $this->counts['apps']; ?></td>
				</tr>
				<?php } ?>
				<?php if($this->publishing) { ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_PUBLICATIONS'); ?>:</td>
					<td><?php echo $this->counts['publications']; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_TODOS'); ?>:</td>
					<td><?php echo $this->counts['todo']; ?> <?php if($this->counts['todos_completed'] > 0) { ?>( +<?php echo $this->counts['todos_completed']; ?> <?php echo JText::_('COM_PROJECTS_TODOS_COMPLETED'); ?>)<?php } ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_NOTES'); ?>:</td>
					<td><?php echo $this->counts['notes']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_ACTIVITIES_IN_FEED'); ?>:</td>
					<td><?php echo $this->counts['activity']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_PROJECTS_LAST_ACTIVITY'); ?>:</td>
					<td><?php if($this->last_activity) { 
						$activity = preg_replace('/said/', "posted an update", $this->last_activity->activity);
						$activity = preg_replace('/&#58;/', "", $activity);
						?>
						<?php echo $this->last_activity->recorded; ?> (<?php echo ProjectsHtml::timeAgo($this->last_activity->recorded).' '.JText::_('COM_PROJECTS_AGO'); ?>) <br /> <span class="actor"><?php echo $this->last_activity->name; ?></span> <?php echo $activity; ?>
						<?php } else { echo JText::_('COM_PROJECTS_NA'); }?>
					</td>
				</tr>
			</tbody>
		</table>
	  </td>
	 </tr>
	</table>
	<div class="division">
		<p class="notice"><a href="index.php?option=com_projects&amp;task=erase&amp;id=<?php echo $this->obj->id; ?>"><?php echo JText::_('COM_PROJECTS_ERASE_PROJECT'); ?></a>. <?php echo JText::_('COM_PROJECTS_ERASE_NOTICE'); ?></p>
	</div>
		<input type="hidden" name="id" value="<?php echo $this->obj->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />

		<div class="clr"></div>
		<?php echo JHTML::_( 'form.token' ); ?>		
</form>
