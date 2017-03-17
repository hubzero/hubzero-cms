<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Push some styles to the template
Hubzero\Document\Assets::addPluginStylesheet('projects', 'files', 'diskspace.css');
Hubzero\Document\Assets::addPluginScript('projects', 'files', 'diskspace.js');
Hubzero\Document\Assets::addPluginScript('projects', 'files');

// Connections enabled?
$p_params = Plugin::params( 'projects', 'files' );

$service = 'google';
$cEnabled = $p_params->get('enable_' . $service, 0);
$connected = $this->params->get($service . '_token');

Toolbar::title(Lang::txt('Projects') . ': ' . stripslashes($this->model->get('title')) . ' (' . $this->model->get('alias') . ', #' . $this->model->get('id') . ')', 'projects');

if (User::authorise('core.edit', $this->option))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

// Determine status & options
$status = '';

if ($this->model->isActive())
{
	$status   = '<span class="active">' . Lang::txt('COM_PROJECTS_ACTIVE') . '</span> ' . Lang::txt('COM_PROJECTS_SINCE') . ' ' . Date::of($this->model->get('created'))->toLocal();
}
elseif ($this->model->isDeleted())
{
	$status  = '<span class="deleted">' . Lang::txt('COM_PROJECTS_DELETED').'</span> ';
}
elseif ($this->model->inSetup())
{
	$status  = '<span class="setup">' . Lang::txt('Setup').'</span> ' . Lang::txt('in progress');
}
elseif ($this->model->isInactive())
{
	$text = $this->suspended ? Lang::txt('COM_PROJECTS_SUSPENDED') : Lang::txt('COM_PROJECTS_INACTIVE');
	$status = '<span class="inactive">' . $text . '</span> ';
	if ($this->suspended)
	{
		$status .= $this->suspended == 1
			? ' (' . Lang::txt('COM_PROJECTS_BY_ADMIN') . ')'
			: ' (' . Lang::txt('COM_PROJECTS_BY_PROJECT_MANAGER') . ')';
	}
}
elseif ($this->model->isPending())
{
	$status  = '<span class="inactive">' . Lang::txt('COM_PROJECTS_PENDING_APPROVAL') . '</span> ';
}

$sysgroup = $this->config->get('group_prefix', 'pr-') . $this->model->get('alias');
$quota    = $this->params->get('quota');
$quota    = $quota ? $quota : \Components\Projects\Helpers\Html::convertSize(floatval($this->config->get('defaultQuota', '1')), 'GB', 'b');

$pubQuota = $this->params->get('pubQuota');
$pubQuota = $pubQuota ? $pubQuota : \Components\Projects\Helpers\Html::convertSize(floatval($this->config->get('pubQuota', '1')), 'GB', 'b');

$this->css();

// Get groups project owner belongs to
$groups = \Hubzero\User\Helper::getGroups($this->model->get('owned_by_user'), 'members', 1);
if ($this->model->groupOwner())
{
	$groups[] = $this->model->groupOwner();
}

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if (pressbutton == 'delete') {
		form.admin_action.value = 'delete';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'suspend') {
		form.admin_action.value = 'suspend';
		submitform( 'save' );
		return;
	}

	if (pressbutton == 'reinstate') {
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
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PROJECTS_BASIC_INFO'); ?></span></legend>

				<div class="input-wrap">
					<label for="title"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?>:</label>
					<input type="text" name="title" id="title" size="60" maxlength="250" value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="alias"><?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?>:</label>
					<span><?php echo stripslashes($this->model->get('alias')); ?></span>
				</div>

				<div class="input-wrap">
					<label for="about"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?>:</label>
					<?php 
						echo $this->editor('about', $this->model->about('raw'), 35, 25, 'about');
					?>
				</div>

				<div class="input-wrap">
					<label for="tags"><?php echo Lang::txt('COM_PROJECTS_TAGS'); ?>:</label>
					<?php
					$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)) );

					if (count($tf) > 0) {
						echo $tf[0];
					} else { ?>
						<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->tags); ?>" />
					<?php } ?>
				</div>

				<?php if (Plugin::isEnabled('projects', 'tools') or $this->publishing) { ?>
					<div class="input-wrap">
						<?php echo Lang::txt('COM_PROJECTS_TYPE'); ?>
						<select name="type">
							<?php foreach ($this->types as $type) {
								if (($type->id == 3 && !$this->publishing) ||
								($type->id == 2 && !Plugin::isEnabled('projects', 'tools'))) {
									continue;
								}
								$selected = $type->id == $this->model->get('type') ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $type->id; ?>" <?php echo $selected; ?>><?php echo $type->type ?></option>
							<?php } ?>
						</select>
					</div>
				<?php } ?>

				<div class="input-wrap">
					<label for="owned_by_user">
						<?php echo Lang::txt('COM_PROJECTS_OWNER_LEAD'); ?>:
						<select name="owned_by_user" class="block">
							<?php foreach ($this->model->team($filters = array('status' => 1), true) as $member) {  ?>
								<option value="<?php echo $member->userid; ?>" <?php if ($member->userid == $this->model->get('owned_by_user')) { echo 'selected="selected"'; } ?>><?php echo $member->fullname; ?> <?php if ($member->userid == $this->model->get('owned_by_user')) { echo '(' . Lang::txt('PLG_PROJECTS_TEAM_CURRENT_OWNER') . ')'; } ?></option>
							<?php } ?>
						</select>
					</label>
				</div>
				<?php if (!empty($groups)) {
					$used = array();
					?>
					<div class="input-wrap">
						<label for="owned_by_group">
							<?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_CHOOSE_GROUP'); ?>:
							<select name="owned_by_group" class="block">
								<option value="0" <?php if (!$this->model->groupOwner()) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('PLG_PROJECTS_TEAM_NO_GROUP'); ?></option>
								<?php foreach ($groups as $g) {
									if (in_array($g->gidNumber, $used))
									{
										continue;
									}
									$used[] = $g->gidNumber; ?>
									<option value="<?php echo $g->gidNumber; ?>" <?php if ($g->gidNumber == $this->model->get('owned_by_group')) { echo 'selected="selected"'; } ?>><?php echo \Hubzero\Utility\String::truncate($g->description, 30) . ' (' . $g->cn . ')'; ?></option>
								<?php } ?>
							</select>
						</label>
					</div>
				<?php } ?>

				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PROJECTS_SYS_GROUP'); ?>:</label>
					<?php echo $sysgroup; ?>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_PROJECTS_PARAMETERS'); ?></legend>

				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PROJECTS_PRIVACY'); ?>:</label>
					<select name="private">
						<option value="0" <?php if ($this->model->isPublic()) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_PUBLIC'); ?></option>
						<option value="1" <?php if (!$this->model->isPublic()) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_PRIVATE'); ?></option>
					</select>
				</div>

				<div class="input-wrap">
					<input type="hidden"  name="params[team_public]" value="0" />
					<input type="checkbox" class="option" name="params[team_public]" value="1" <?php if ($this->params->get( 'team_public')) { echo ' checked="checked"'; } ?> />
					<label><?php echo Lang::txt('COM_PROJECTS_TEAM_PUBLIC'); ?></label>
				</div>
				<div class="input-wrap">
					<input type="hidden"  name="params[publications_public]" value="0" />
					<input type="checkbox" class="option" name="params[publications_public]" value="1" <?php if ($this->params->get( 'publications_public')) { echo ' checked="checked"'; } ?> />
					<label><?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS_PUBLIC'); ?></label>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_PROJECTS_LAYOUT'); ?>:</label>
					<select name="params[layout]">
						<option value="standard" <?php if ($this->params->get( 'layout', 'standard') == 'standard') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_LAYOUT_STANDARD'); ?></option>
						<option value="extended" <?php if ($this->params->get( 'layout') == 'extended') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_PROJECTS_LAYOUT_EXTENDED'); ?></option>
					</select>
				</div>

				<?php if ($this->config->get('restricted_data', 0)) { ?>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_SENSITIVE_DATA'); ?>:</label>
						<?php echo strtoupper($this->params->get( 'restricted_data', 'no')); ?>
						<?php if ($this->params->get( 'restricted_data') == 'yes') { ?> (
							<?php if ($this->params->get( 'hipaa_data')  == 'yes') { echo 'HIPAA'; } ?>
							<?php if ($this->params->get( 'ferpa_data')  == 'yes') { echo 'FERPA'; } ?>
							<?php if ($this->params->get( 'export_data') == 'yes') { echo 'Export Controlled'; } ?>
							<?php if ($this->params->get( 'irb_data') == 'yes') { echo 'IRB'; } ?>
							)
						<?php } ?>
					</div>
				<?php } ?>
				<?php if ($this->config->get('grantinfo', 0)) { ?>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_TERMS_GRANT_TITLE'); ?>:</label>
						<input name="params[grant_title]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_title'))); ?>" class="long" />
					</div>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_TERMS_GRANT_PI'); ?>:</label>
						<input name="params[grant_PI]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_PI'))); ?>" class="long" />
					</div>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_TERMS_GRANT_AGENCY'); ?>:</label>
						<input name="params[grant_agency]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_agency'))); ?>" class="long" />
					</div>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_TERMS_GRANT_BUDGET'); ?>:</label>
						<input name="params[grant_budget]" maxlength="250" type="text" value="<?php echo $this->escape(html_entity_decode($this->params->get( 'grant_budget'))); ?>" class="long" />
					</div>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_TERMS_GRANT_APPROVAL_CODE'); ?>:</label>
						<?php $approval = $this->escape(html_entity_decode($this->params->get( 'grant_approval'))); echo $approval ? $approval : Lang::txt('COM_PROJECTS_NA'); ?>
					</div>
				<?php } ?>
			</fieldset>

			<?php if (!$this->model->inSetup()) { ?>
				<fieldset class="adminform">
					<legend><?php echo Lang::txt('COM_PROJECTS_FILES'); ?></legend>

					<div class="input-wrap">
						<label><?php echo Lang::txt('Files Quota'); ?>: <?php echo ' (' . Lang::txt('COM_PROJECTS_FILES_GBYTES').')'; ?></label>
						<input name="params[quota]" maxlength="100" type="text" value="<?php echo \Components\Projects\Helpers\Html::convertSize($quota, 'b', 'GB', 2); ?>" class="short" />
					</div>

					<div class="input-wrap">
						<label><?php echo Lang::txt('Publications Quota'); ?>: <?php echo ' (' . Lang::txt('COM_PROJECTS_FILES_GBYTES').')'; ?></label>
						<input name="params[pubQuota]" maxlength="100" type="text" value="<?php echo \Components\Projects\Helpers\Html::convertSize($pubQuota, 'b', 'GB', 2); ?>" class="short" />
					</div>

					<?php if ($this->diskusage) { ?>
						<div class="input-wrap">
							<?php echo $this->diskusage; ?>
						</div>
					<?php } ?>
					<div class="input-wrap">
						<?php echo Lang::txt('Maintenance options:'); ?> &nbsp; <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=gitgc&id=' . $this->model->get('id')); ?>"><?php echo Lang::txt('git gc --aggressive'); ?></a> [<?php echo Lang::txt('Takes minutes to run'); ?>]
					</div>

					<?php if ($cEnabled) { ?>
						<div class="input-wrap">
							<?php echo Lang::txt('COM_PROJECTS_CONNECTIONS'); ?>: <strong><?php echo $connected ? $service : 'not connected'; ?></strong> &nbsp;
							<?php if ($connected) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=fixsync&id=' . $this->model->get('id')); ?>"><?php echo Lang::txt('download sync log'); ?></a> &nbsp; [<?php echo Lang::txt('Also fixes stalled sync'); ?>]
							<?php } ?>
						</div>
					<?php } ?>
				</fieldset>
			<?php } ?>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_CREATED'); ?>:</th>
						<td><?php echo $this->model->get('created'); ?> <?php echo Lang::txt('COM_PROJECTS_BY').' ' . $this->model->creator('name') . ' (' . $this->model->creator('username') . ')'; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_STATUS'); ?></th>
						<td><?php echo $status; ?></td>
					</tr>
				<?php if (isset($this->counts['files'])): ?>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_FILES'); ?>:</th>
						<td><?php echo $this->counts['files']; ?></td>
					</tr>
				<?php endif; ?>
				<?php if (isset($this->counts['publications'])): ?>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS'); ?>:</th>
						<td><?php echo $this->counts['publications']; ?></td>
					</tr>
				<?php endif; ?>
				<?php if (isset($this->counts['todo'])): ?>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_TODOS'); ?>:</th>
						<td><?php echo $this->counts['todo']; ?> <?php if ($this->counts['todos_completed'] > 0) { ?>( +<?php echo $this->counts['todos_completed']; ?> <?php echo Lang::txt('COM_PROJECTS_TODOS_COMPLETED'); ?>)<?php } ?></td>
					</tr>
				<?php endif; ?>
				<?php if (isset($this->counts['notes'])): ?>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_NOTES'); ?>:</th>
						<td><?php echo $this->counts['notes']; ?></td>
					</tr>
				<?php endif; ?>
				<?php if (isset($this->counts['activity'])): ?>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_ACTIVITIES_IN_FEED'); ?>:</th>
						<td><?php echo $this->counts['activity']; ?></td>
					</tr>
				<?php endif; ?>
					<tr>
						<th><?php echo Lang::txt('COM_PROJECTS_LAST_ACTIVITY'); ?>:</th>
						<td><?php if ($this->last_activity) {
							$activity = preg_replace('/said/', "posted an update", $this->last_activity->activity);
							$activity = preg_replace('/&#58;/', "", $activity);
							?>
							<?php echo $this->last_activity->recorded; ?> (<?php echo \Components\Projects\Helpers\Html::timeAgo($this->last_activity->recorded) . ' ' . Lang::txt('COM_PROJECTS_AGO'); ?>) <br /> <span class="actor"><?php echo $this->last_activity->name; ?></span> <?php echo $activity; ?>
							<?php } else { echo Lang::txt('COM_PROJECTS_NA'); }?>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_PROJECTS_STATUS'); ?></legend>

				<div class="input-wrap">
					<?php echo Lang::txt('COM_PROJECTS_MESSAGE'); ?>:
					<textarea name="message" id="message" rows="5" cols="50"></textarea>
				</div>

				<div class="input-wrap">
					<?php echo Lang::txt('COM_PROJECTS_OPTIONS'); ?>:<br />

						<input type="hidden" name="admin_action" value="" />
						<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_OPTION_SEND_MESSAGE'); ?>" class="btn" id="do-message" /> <span class="breaker"> | </span>
					<?php if ($this->model->isActive()) { ?>
						<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_OPTION_SUSPEND'); ?>" class="btn" id="do-suspend" onclick="javascript: submitbutton('suspend')" />
					<?php } else if ($this->model->isInactive() || $this->model->isDeleted()) { ?>
						<input type="submit" value="<?php echo $this->suspended ? Lang::txt('COM_PROJECTS_OPTION_REINSTATE') : Lang::txt('COM_PROJECTS_OPTION_ACTIVATE'); ?>" class="btn" id="do-reisnate" onclick="javascript: submitbutton('reinstate')" />
					<?php } ?>
					<?php if (!$this->model->isDeleted()) { ?>
						<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_OPTION_DELETE'); ?>" class="btn" id="do-delete" onclick="javascript: submitbutton('delete')" />
					<?php } ?>
					<?php if ($this->model->isArchived()) { ?>
						<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_OPTION_UNARCHIVE'); ?>" class="btn" id="do-unarchive" onclick="javascript: submitbutton('unarchive')" />
					<?php } else { ?>
						<input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_OPTION_ARCHIVE'); ?>" class="btn" id="do-unarchive" onclick="javascript: submitbutton('archive')" />
					<?php } ?>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_PROJECTS_TEAM') . ' (' . $this->counts['team'] . ')'; ?></legend>
				<table>
					<tbody>
						<tr>
							<th><?php echo Lang::txt('COM_PROJECTS_MANAGERS'); ?>:</th>
							<td><?php echo $this->managers ? $this->managers : Lang::txt('COM_PROJECTS_NA'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PROJECTS_COLLABORATORS'); ?>:</th>
							<td><?php echo $this->members ? $this->members : Lang::txt('COM_PROJECTS_NA'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PROJECTS_AUTHORS'); ?>:</th>
							<td><?php echo $this->authors ? $this->authors : Lang::txt('COM_PROJECTS_NA'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_PROJECTS_REVIEWERS'); ?>:</th>
							<td><?php echo $this->reviewers ? $this->reviewers : Lang::txt('COM_PROJECTS_NA'); ?></td>
						</tr>
					</tbody>
				</table>

				<fieldset>
					<legend><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER'); ?></legend>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER_USERNAME'); ?></label>
						<input type="text" name="newmember" id="newmember" value="" />
					</div>

					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE'); ?></label>
						<select name="role">
							<option value="1"><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE_MANAGER'); ?></option>
							<option value="0"><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE_COLLABORATOR'); ?></option>
						</select>
					</div>
				</fieldset>
			</fieldset>
		</div>
	</div>

	<div class="width-100">
		<p class="notice"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=erase&id=' . $this->model->get('id')); ?>"><?php echo Lang::txt('COM_PROJECTS_ERASE_PROJECT'); ?></a>. <?php echo Lang::txt('COM_PROJECTS_ERASE_NOTICE'); ?></p>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="apply" />

	<?php echo Html::input('token'); ?>
</form>
