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

JToolBarHelper::title( JText::_( 'Projects' ) . ': '.stripslashes($this->obj->title).' ('.$this->obj->alias.', #'.$this->obj->id.')', 'addedit.png' );
JToolBarHelper::spacer();
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;

// Get creator profile
$profile = \Hubzero\User\Profile::getInstance($this->obj->created_by_user);

// Determine status & options
$status = '';
$row = $this->obj;
if ($row->state == 1 && $row->setup_stage >= $setup_complete)
{
	$status   = '<span class="active">'.JText::_('COM_PROJECTS_ACTIVE').'</span> '.JText::_('COM_PROJECTS_SINCE').' '.JHTML::_('date', $row->created, $dateFormat, $tz);
}
elseif ($row->state == 2)
{
	$status  = '<span class="deleted">'.JText::_('COM_PROJECTS_DELETED').'</span> ';
}
elseif ($row->setup_stage < $setup_complete)
{
	$status  = '<span class="setup">'.JText::_('Setup').'</span> '.JText::_('in progress');
}
elseif ($row->state == 0)
{
	$text = $this->suspended ? JText::_('COM_PROJECTS_SUSPENDED') : JText::_('COM_PROJECTS_INACTIVE');
	$status = '<span class="inactive">'.$text.'</span> ';
	if ($this->suspended)
	{
		$status .= $this->suspended == 1
			? ' (' . JText::_('COM_PROJECTS_BY_ADMIN') .')'
			: ' (' . JText::_('COM_PROJECTS_BY_PROJECT_MANAGER').')';
	}
}
elseif ($row->state == 5)
{
	$status  = '<span class="inactive">'.JText::_('COM_PROJECTS_PENDING_APPROVAL').'</span> ';
}

$sysgroup 	= $this->config->get('group_prefix', 'pr-').$this->obj->alias;
$quota 		= $this->params->get('quota');
$quota 		= $quota ? $quota : ProjectsHtml::convertSize( floatval($this->config->get('defaultQuota', '1')), 'GB', 'b');

$pubQuota 	= $this->params->get('pubQuota');
$pubQuota 	= $pubQuota ? $pubQuota : ProjectsHtml::convertSize( floatval($this->config->get('pubQuota', '1')), 'GB', 'b');

JPluginHelper::importPlugin( 'hubzero' );
$dispatcher = JDispatcher::getInstance();

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
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_PROJECTS_BASIC_INFO'); ?></span></legend>

			<div class="input-wrap">
				<label for="title"><?php echo JText::_('COM_PROJECTS_TITLE'); ?>:</label>
				<input type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo $this->escape(stripslashes($this->obj->title)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="alias"><?php echo JText::_('COM_PROJECTS_ALIAS'); ?>:</label>
				<span><?php echo stripslashes($this->obj->alias); ?></span>
			</div>

			<div class="input-wrap">
				<label for="about"><?php echo JText::_('COM_PROJECTS_ABOUT'); ?>:</label>
				<textarea name="about" id="about" rows="10" cols="50"><?php echo $this->obj->about; ?></textarea>
			</div>

			<div class="input-wrap">
				<label for="tags"><?php echo JText::_('COM_PROJECTS_TAGS'); ?>:</label>
				<?php
				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)) );

				if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->tags); ?>" />
				<?php } ?>
			</div>

			<?php if(JPluginHelper::isEnabled('projects', 'apps') or $this->publishing) { ?>
				<div class="input-wrap">
					<?php echo JText::_('COM_PROJECTS_TYPE'); ?>
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
				</div>
			<?php } ?>

			<div class="input-wrap">
				<?php echo JText::_('COM_PROJECTS_OWNER'); ?>:
				<?php
				if ($this->obj->owned_by_group)
				{
					$group = \Hubzero\User\Group::getInstance( $this->obj->owned_by_group );
					if ($group)
					{
						$ownedby = '<span class="i_group">'.$group->get('cn').'</span>';
					}
					else
					{
						$ownedby = '<span class="pale">'.JText::_('COM_PROJECTS_INFO_DELETED_GROUP').'</span>';
					}
				}
				else
				{
					$profile = \Hubzero\User\Profile::getInstance($this->obj->owned_by_user);
					$ownedby = $profile->get('name') ? $profile->get('name') : JText::_('COM_PROJECTS_INFO_UNKNOWN_USER');
					$ownedby = '<span class="i_user">'.$ownedby.'</span>';
				}
				echo $ownedby;
				?>
			</div>

			<div class="input-wrap">
				<label><?php echo JText::_('COM_PROJECTS_SYS_GROUP'); ?>:</label>
				<?php echo $sysgroup; ?>
			</div>

			<!-- <div class="input-wrap">
				<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_EDITS'); ?>" />
			</div> -->
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROJECTS_PARAMETERS'); ?></legend>

			<div class="input-wrap">
				<label><?php echo JText::_('COM_PROJECTS_PRIVACY'); ?>:</label>
				<select name="private">
					<option value="0" <?php if($this->obj->private == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_PROJECTS_PUBLIC'); ?></option>
					<option value="1" <?php if($this->obj->private == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_PROJECTS_PRIVATE'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<input type="hidden"  name="params[team_public]" value="0" />
				<input type="checkbox" class="option" name="params[team_public]" value="1" <?php if ($this->params->get( 'team_public')) { echo ' checked="checked"'; } ?> />
				<label><?php echo JText::_('COM_PROJECTS_TEAM_PUBLIC'); ?></label>
			</div>

			<?php if($this->config->get('restricted_data', 0)) { ?>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_SENSITIVE_DATA'); ?></label>
					<?php echo strtoupper($this->params->get( 'restricted_data', 'no')); ?>
					<?php if($this->params->get( 'restricted_data') == 'yes') { ?> (
						<?php if($this->params->get( 'hipaa_data')  == 'yes') { echo 'HIPAA'; } ?>
						<?php if($this->params->get( 'ferpa_data')  == 'yes') { echo 'FERPA'; } ?>
						<?php if($this->params->get( 'export_data') == 'yes') { echo 'Export Controlled'; } ?>
						<?php if($this->params->get( 'irb_data') == 'yes') { echo 'IRB'; } ?>
						)
					<?php } ?>
				</div>
			<?php } ?>
			<?php if ($this->config->get('grantinfo', 0)) { ?>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_TITLE'); ?>:</label>
					<input name="params[grant_title]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_title'))); ?>" class="long" />
				</div>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_PI'); ?>:</label>
					<input name="params[grant_PI]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_PI'))); ?>" class="long" />
				</div>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_AGENCY'); ?>:</label>
					<input name="params[grant_agency]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_agency'))); ?>" class="long" />
				</div>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_BUDGET'); ?>:</label>
					<input name="params[grant_budget]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_budget'))); ?>" class="long" />
				</div>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_TERMS_GRANT_APPROVAL_CODE'); ?>:</label>
					<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_approval'))); ?>
				</div>
			<?php } ?>

			<!-- <div class="input-wrap">
				<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_EDITS'); ?>" />
			</div> -->
		</fieldset>

		<?php if ($row->setup_stage >= $setup_complete) { ?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PROJECTS_FILES'); ?></legend>

				<div class="input-wrap">
					<label><?php echo JText::_('Files Quota'); ?>: <?php echo ' ('.JText::_('COM_PROJECTS_FILES_GBYTES').')'; ?></label>
					<input name="params[quota]" maxlength="100" type="text" value="<?php echo ProjectsHtml::convertSize($quota, 'b', 'GB', 2); ?>" class="short" />
				</div>

				<div class="input-wrap">
					<label><?php echo JText::_('Publications Quota'); ?>: <?php echo ' ('.JText::_('COM_PROJECTS_FILES_GBYTES').')'; ?></label>
					<input name="params[pubQuota]" maxlength="100" type="text" value="<?php echo ProjectsHtml::convertSize($pubQuota, 'b', 'GB', 2); ?>" class="short" />
				</div>

				<?php if($this->diskusage) { ?>
					<div class="input-wrap">
						<?php echo $this->diskusage; ?>
					</div>
				<?php } ?>
					<!-- <tr class="division">
						<td colspan="3" class="centeralign"><input type="submit" value="<?php echo JText::_('COM_PROJECTS_CHANGE_QUOTA'); ?>" class="btn"  />
					</td>
					</tr> -->
				<div class="input-wrap">
					<?php echo JText::_('Maintenance options:'); ?> &nbsp; <a href="index.php?option=com_projects&amp;task=gitgc&amp;id=<?php echo $this->obj->id; ?>"><?php echo JText::_('git gc --aggressive'); ?></a> [<?php echo JText::_('Takes minutes to run'); ?>]
				</div>

				<?php if ($cEnabled) { ?>
					<div class="input-wrap">
						<?php echo JText::_('Connections'); ?>: <strong><?php echo $connected ? $service : 'not connected'; ?></strong> &nbsp;
						<?php if ($connected) { ?>
							<a href="index.php?option=com_projects&amp;task=fixsync&amp;id=<?php echo $this->obj->id; ?>"><?php echo JText::_('download sync log'); ?></a> &nbsp; [<?php echo JText::_('Also fixes stalled sync'); ?>]
						<?php } ?>
					</div>
				<?php } ?>
			</fieldset>
		<?php } ?>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_CREATED'); ?>:</th>
					<td><?php echo $this->obj->created; ?> <?php echo JText::_('COM_PROJECTS_BY').' '.$profile->get('name').' ('.$profile->get('username').')'; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_STATUS'); ?></th>
					<td><?php echo $status; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_FILES'); ?>:</th>
					<td><?php echo $this->counts['files']; ?></td>
				</tr>
				<?php if(JPluginHelper::isEnabled('projects', 'apps') && isset($this->counts['apps'])) { ?>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_APPS'); ?>:</th>
					<td><?php echo $this->counts['apps']; ?></td>
				</tr>
				<?php } ?>
				<?php if($this->publishing) { ?>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_PUBLICATIONS'); ?>:</th>
					<td><?php echo $this->counts['publications']; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_TODOS'); ?>:</th>
					<td><?php echo $this->counts['todo']; ?> <?php if($this->counts['todos_completed'] > 0) { ?>( +<?php echo $this->counts['todos_completed']; ?> <?php echo JText::_('COM_PROJECTS_TODOS_COMPLETED'); ?>)<?php } ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_NOTES'); ?>:</th>
					<td><?php echo $this->counts['notes']; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_ACTIVITIES_IN_FEED'); ?>:</th>
					<td><?php echo $this->counts['activity']; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_PROJECTS_LAST_ACTIVITY'); ?>:</th>
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

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROJECTS_STATUS'); ?></legend>

			<div class="input-wrap">
				<?php echo JText::_('COM_PROJECTS_MESSAGE'); ?>:
				<textarea name="message" id="message" rows="5" cols="50"></textarea>
			</div>

			<div class="input-wrap">
				<?php echo JText::_('COM_PROJECTS_OPTIONS'); ?>:<br />

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
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PROJECTS_TEAM').' ('.$this->counts['team'].')'; ?></legend>

			<table>
				<tbody>
					<tr>
						<th><?php echo JText::_('COM_PROJECTS_MANAGERS'); ?>:</th>
						<td><?php echo $this->managers ? $this->managers : JText::_('COM_PROJECTS_NA'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_PROJECTS_COLLABORATORS'); ?>:</th>
						<td><?php echo $this->members ? $this->members : JText::_('COM_PROJECTS_NA'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_PROJECTS_AUTHORS'); ?>:</th>
						<td><?php echo $this->authors ? $this->authors : JText::_('COM_PROJECTS_NA'); ?></td>
					</tr>
				</tbody>
			</table>

			<fieldset>
				<legend><?php echo JText::_('COM_PROJECTS_ADD_MEMBER'); ?></legend>
				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_ADD_MEMBER_USERNAME'); ?></label>
					<input type="text" name="newmember" id="newmember" value="" />
				</div>

				<div class="input-wrap">
					<label><?php echo JText::_('COM_PROJECTS_ADD_MEMBER_ROLE'); ?></label>
					<select name="role">
						<option value="1"><?php echo JText::_('COM_PROJECTS_ADD_MEMBER_ROLE_MANAGER'); ?></option>
						<option value="0"><?php echo JText::_('COM_PROJECTS_ADD_MEMBER_ROLE_COLLABORATOR'); ?></option>
					</select>
				</div>
			</fieldset>
		</fieldset>
	</div>
	<div class="clr"></div>

	<div class="width-100">
		<p class="notice"><a href="index.php?option=com_projects&amp;task=erase&amp;id=<?php echo $this->obj->id; ?>"><?php echo JText::_('COM_PROJECTS_ERASE_PROJECT'); ?></a>. <?php echo JText::_('COM_PROJECTS_ERASE_NOTICE'); ?></p>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->obj->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="apply" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
