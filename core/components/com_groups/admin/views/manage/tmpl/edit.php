<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->group->get('gidNumber') ? Lang::txt('COM_GROUPS_EDIT') : Lang::txt('COM_GROUPS_NEW'));

$canDo = \Components\Groups\Helpers\Permissions::getActions('group', $this->group->get('gidNumber'));

Toolbar::title(Lang::txt('COM_GROUPS').': ' . $text, 'groups');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('group');

Html::behavior('framework');
Html::behavior('switcher', 'submenu');

// are we using the email gateway for group forum
$params = Component::params('com_groups');
$allowEmailResponses = $params->get('email_comment_processing', 0);

$autoEmailResponses  = $this->group->get('discussion_email_autosubscribe');
if (is_null($autoEmailResponses))
{
	$autoEmailResponses = $params->get('email_member_groupsidcussionemail_autosignup', 0);
}
if ($this->group->get('discussion_email_autosubscribe', null) == 1
	|| ($this->group->get('discussion_email_autosubscribe', null) == null && $autoEmailResponses))
{
	$autoEmailResponses = 1;
}

// get groups params
$gparams              = new \Hubzero\Config\Registry($this->group->params);
$membership_control   = $gparams->get('membership_control', 1);
$display_system_users = $gparams->get('display_system_users', 'global');
$comments             = $gparams->get('page_comments', $params->get('page_comments', 0));
$author               = $gparams->get('page_author', $params->get('page_author', 0));
$trusted              = $gparams->get('page_trusted', $params->get('page_trusted', 0));

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php if ($this->getErrors()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<div id="item-form-wrap">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
		<nav role="navigation" class="sub-navigation">
			<div id="submenu-box">
				<div class="submenu-box">
					<div class="submenu-pad">
						<ul id="submenu" class="coursesection">
							<li><a href="#page-details" onclick="return false;" id="details" class="active"><?php echo Lang::txt('JDETAILS'); ?></a></li>
							<li><a href="#page-files" onclick="return false;" id="files"><?php echo Lang::txt('COM_GROUPS_MEDIA'); ?></a></li>
							<!-- <li><a href="#" onclick="return false;" id="pages"><?php echo Lang::txt('COM_COURSES_FIELDSET_PAGES'); ?></a></li> -->
						</ul>
						<div class="clr"></div>
					</div>
				</div>
				<div class="clr"></div>
			</div>
		</nav><!-- / .sub-navigation -->

		<div id="section-document">
			<div id="page-details" class="tab">

				<div class="grid">
					<div class="col span7">
						<fieldset class="adminform">
							<legend><span><?php echo Lang::txt('COM_GROUPS_DETAILS'); ?></span></legend>

							<input type="hidden" name="group[gidNumber]" value="<?php echo $this->group->gidNumber; ?>" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
							<input type="hidden" name="task" value="save" />

							<div class="input-wrap">
								<label for="field-type"><?php echo Lang::txt('COM_GROUPS_TYPE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
								<select name="group[type]" id="field-type" class="required">
									<option value="1"<?php echo ($this->group->type == '1') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_TYPE_HUB'); ?></option>
									<option value="3"<?php echo ($this->group->type == '3') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_TYPE_SUPER'); ?></option>
									<?php if ($canDo->get('core.admin')) { ?>
										<option value="0"<?php echo ($this->group->type == '0') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_TYPE_SYSTEM'); ?></option>
									<?php } ?>
									<option value="2"<?php echo ($this->group->type == '2') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_TYPE_PROJECT'); ?></option>
									<option value="4"<?php echo ($this->group->type == '4') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_GROUPS_TYPE_COURSE'); ?></option>
								</select>
							</div>

							<div class="grid">
								<div class="col span6">
									<div class="input-wrap">
										<label for="field-published"><?php echo Lang::txt('COM_GROUPS_PUBLISHED'); ?>:</label>
										<select name="group[published]" id="field-published">
											<option <?php if ($this->group->published == 0) { echo 'selected="selected"'; } ?> value="0"><?php echo Lang::txt('COM_GROUPS_UNPUBLISHED'); ?></option>
											<option <?php if ($this->group->published == 1) { echo 'selected="selected"'; } ?> value="1"><?php echo Lang::txt('COM_GROUPS_PUBLISHED'); ?></option>
											<option <?php if ($this->group->published == 2) { echo 'selected="selected"'; } ?> value="2"><?php echo Lang::txt('COM_GROUPS_ARCHIVED'); ?></option>
										</select>
									</div>
								</div>
								<div class="col span6">
									<div class="input-wrap">
										<label for="field-approved"><?php echo Lang::txt('COM_GROUPS_APPROVE'); ?>:</label>
										<select name="group[approved]" id="field-approved">
											<option <?php if ($this->group->approved == 0) { echo 'selected="selected"'; } ?> value="0"><?php echo Lang::txt('COM_GROUPS_UNAPPROVED'); ?></option>
											<option <?php if ($this->group->approved == 1) { echo 'selected="selected"'; } ?> value="1"><?php echo Lang::txt('COM_GROUPS_APPROVED'); ?></option>
										</select>
									</div>
								</div>
							</div>

							<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_CN_HINT'); ?>">
								<label for="field-cn"><?php echo Lang::txt('COM_GROUPS_CN'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
								<input type="text" name="group[cn]" id="field-cn" class="required" value="<?php echo $this->escape(stripslashes($this->group->cn)); ?>" />
								<span class="hint"><?php echo Lang::txt('COM_GROUPS_CN_HINT'); ?></span>
							</div>
							<div class="input-wrap">
								<label for="field-description"><?php echo Lang::txt('COM_GROUPS_TITLE'); ?>:</label><br />
								<input type="text" name="group[description]" id="field-description" value="<?php echo $this->escape(stripslashes($this->group->description)); ?>" />
							</div>
							<div class="input-wrap">
								<label for="field-logo"><?php echo Lang::txt('COM_GROUPS_LOGO'); ?>:</label><br />
								<input type="text" name="group[logo]" id="field-logo" value="<?php echo $this->escape($this->group->logo); ?>" />
							</div>

							<?php
							$this->js('customfields');

							$xml = Components\Groups\Models\Orm\Field::toXml($this->customFields);

							$formInfo = array('control' => 'customfields');

							$form = new Hubzero\Form\Form('application', $formInfo);
							$form->load($xml);
							$form->bind($this->customAnswers);

							foreach ($this->customFields as $field)
							{
								$formfield = $form->getField($field->get('name'));

								$hint = '';
								if ($formfield->description && strtolower($formfield->type) != 'paragraph')
								{
									$hint = trim($formfield->description);
								}

								echo '<div class="input-wrap"' . ($hint ? ' data-hint="' . $this->escape($hint) . '"' : '') . '>';

								if (strtolower($formfield->type) != 'paragraph')
								{
									echo $formfield->label;
								}

								if ($field->type == 'textarea')
								{
									$fieldName     = $field->get('name');
									$fieldValue    = isset($this->customAnswers[$fieldName]) ? $this->customAnswers[$fieldName] : $field->get('default_value', '');
									$fieldNameAttr = $formInfo['control'] . '[' . $fieldName . ']';
									$fieldIdAttr   = $formInfo['control'] . '_' . $fieldName;

									echo $this->editor($fieldNameAttr, $this->escape($fieldValue), 35, 8, $fieldIdAttr, array('class' => 'minimal no-footer images macros'));
								}
								else
								{
									echo $formfield->input;
								}

								if ($hint)
								{
									echo '<span class="hint">' . $hint . '</span>';
								}
								echo '</div>';
							}
							?>
						</fieldset>

						<fieldset class="adminform">
							<legend><span><?php echo Lang::txt('COM_GROUPS_PAGE_SETTINGS'); ?></span></legend>

							<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_TRUSTEDCONTENT_HINT'); ?>">
								<label for="param-page_trusted"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_TRUSTEDCONTENT'); ?>:</label>
								<select name="group[params][page_trusted]" id="param-page_trusted">
									<option <?php if ($trusted == 0) { echo 'selected="selected"'; } ?> value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_TRUSTEDCONTENT_NO'); ?></option>
									<option <?php if ($trusted == 1) { echo 'selected="selected"'; } ?> value="1"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_TRUSTEDCONTENT_YES'); ?></option>
								</select>
							</div>

							<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS_HINT'); ?>">
								<label for="param-page_comments"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS'); ?>:</label>
								<select name="group[params][page_comments]" id="param-page_comments">
									<option <?php if ($comments == 0) { echo 'selected="selected"'; } ?> value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO'); ?></option>
									<option <?php if ($comments == 1) { echo 'selected="selected"'; } ?> value="1"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES'); ?></option>
									<option <?php if ($comments == 2) { echo 'selected="selected"'; } ?> value="2"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK'); ?></option>
								</select>
							</div>

							<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_HINT'); ?>">
								<label for="param-page_author"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR'); ?>:</label>
								<select name="group[params][page_author]" id="param-page_author">
									<option <?php if ($author == 0) { echo 'selected="selected"'; } ?> value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO'); ?></option>
									<option <?php if ($author == 1) { echo 'selected="selected"'; } ?> value="1"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES'); ?></option>
								</select>
							</div>
						</fieldset>
					</div>
					<div class="col span5">
						<table class="meta">
							<tbody>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_GROUPS_ID'); ?></th>
									<td><?php echo $this->escape($this->group->gidNumber); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_GROUPS_PUBLISHED'); ?></th>
									<td><?php echo ($this->group->published) ? 'Yes' : 'No'; ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_GROUPS_APPROVED'); ?></th>
									<td><?php echo ($this->group->approved) ? 'Yes' : 'No'; ?></td>
								</tr>
								<?php if ($this->group->created) { ?>
									<tr>
										<th scope="row"><?php echo Lang::txt('COM_GROUPS_CREATED'); ?></th>
										<td><?php echo $this->escape(date("l F d, Y @ g:ia", strtotime($this->group->created))); ?></td>
									</tr>
								<?php } ?>
								<?php if ($this->group->created_by) { ?>
									<tr>
										<th scope="row"><?php echo Lang::txt('COM_GROUPS_CREATED_BY'); ?></th>
										<td><?php
										$creator = User::getInstance($this->group->created_by);
										echo $this->escape($creator->get('name')); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>

						<fieldset class="adminform">
							<legend><span><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP'); ?></span></legend>

							<div class="input-wrap">
								<input type="checkbox" name="group[params][membership_control]" id="field-membership_control" value="1" <?php if ($membership_control == 1) { ?>checked="checked"<?php } ?> />
								<label for="field-membership_control"><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_CONTROL'); ?></label>
							</div>
							<fieldset>
								<legend><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY'); ?>:</legend>
								<div class="input-wrap">
									<input type="radio" name="group[join_policy]" id="field-join_policy0" value="0"<?php if ($this->group->join_policy == 0) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy0"><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC') . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC_DESC'); ?></label><br />
									<input type="radio" name="group[join_policy]" id="field-join_policy1" value="1"<?php if ($this->group->join_policy == 1) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy1"><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED') . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED_DESC'); ?></label><br />
									<input type="radio" name="group[join_policy]" id="field-join_policy2" value="2"<?php if ($this->group->join_policy == 2) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy2"><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE') . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE_DESC'); ?></label><br />
									<input type="radio" name="group[join_policy]" id="field-join_policy3" value="3"<?php if ($this->group->join_policy == 3) { echo ' checked="checked"'; } ?> /> <label for="field-join_policy3"><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED') . ' &mdash; ' . Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED_DESC'); ?></label>
								</div>
							</fieldset>
							<div class="input-wrap">
								<label for="restrict_msg"><?php echo Lang::txt('COM_GROUPS_EDIT_CREDENTIALS'); ?>:</label><br />
								<?php echo $this->editor('group[restrict_msg]', $this->escape(stripslashes($this->group->restrict_msg)), 40, 10, 'restrict_msg', array('class' => 'minimal')); ?>
							</div>
						</fieldset>
						<fieldset class="adminform">
							<legend><span><?php echo Lang::txt('COM_GROUPS_ACCESS'); ?></span></legend>

							<fieldset>
								<legend><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY'); ?>:</legend>

								<div class="input-wrap">
									<input type="radio" name="group[discoverability]" id="field-discoverability0" value="0"<?php if ($this->group->discoverability == 0) { echo ' checked="checked"'; } ?> />
									<label for="field-discoverability0"><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_VISIBLE'); ?></label>
									<br />
									<input type="radio" name="group[discoverability]" id="field-discoverability1" value="1"<?php if ($this->group->discoverability == 1) { echo ' checked="checked"'; } ?> />
									<label for="field-discoverability1"><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_HIDDEN'); ?></label>
								</div>
							</fieldset>

							<div class="input-wrap">
								<label for="field-plugins"><?php echo Lang::txt('COM_GROUPS_PLUGIN_ACCESS'); ?>:</label><br />
								<textarea name="group[plugins]" id="field-plugins" rows="10" cols="50"><?php echo $this->escape($this->group->plugins); ?></textarea>
							</div>

							<div class="input-wrap">
								<label for="display_system_users"><?php echo Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS'); ?>:</label><br />
								<select name="group[params][display_system_users]" id="display_system_users">
									<option <?php if ($display_system_users == 'global') { echo 'selected="selected"'; } ?> value="global"><?php echo Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_GLOBAL'); ?></option>
									<option <?php if ($display_system_users == 'no') { echo 'selected="selected"'; } ?> value="no"><?php echo Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_NO'); ?></option>
									<option <?php if ($display_system_users == 'yes') { echo 'selected="selected"'; } ?> value="yes"><?php echo Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_YES'); ?></option>
								</select>
							</div>
						</fieldset>

						<?php if ($allowEmailResponses) : ?>
							<fieldset class="adminform">
								<legend><span><?php echo Lang::txt('COM_GROUPS_EMAIL_SETTINGS'); ?></span></legend>

								<fieldset>
									<legend><?php echo Lang::txt('COM_GROUPS_DISCUSSION_EMAILS'); ?>:</legend>

									<div class="input-wrap">
										<input type="hidden" name="group[discussion_email_autosubscribe]" value="0" />
										<input type="checkbox" name="group[discussion_email_autosubscribe]" id="field-membership_control" value="1" <?php if ($autoEmailResponses == 1) { ?>checked="checked"<?php } ?> />
										<label for="field-membership_control"><?php echo Lang::txt('COM_GROUPS_DISCUSSION_EMAIL_AUTOSUBSCRIBE'); ?></label>
									</div>
								</fieldset>
							</fieldset>
						<?php endif; ?>
					</div>
				</div><!-- / .grid -->
			</div>

			<div id="page-files" class="tab">
				<fieldset class="adminform">
					<?php if ($this->group->gidNumber) { ?>
						<legend><span><?php echo Lang::txt('COM_GROUPS_MEDIA_PATH', substr(PATH_APP, strlen(PATH_ROOT)) . DS . trim(Component::params('com_groups')->get('uploadpath', '/site/groups'), DS) . DS . $this->group->get('gidNumber')); ?></span></legend>
						<iframe width="100%" height="500" name="media" id="media" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=media&tmpl=component&gidNumber=' . $this->group->gidNumber . '&t=' . Date::toUnix()); ?>"></iframe>
					<?php } else { ?>
						<p class="warning"><?php echo Lang::txt('COM_GROUPS_MEDIA_FILES_WARNING'); ?></p>
					<?php } ?>
				</fieldset>
			</div>
		</div>

		<?php /*if ($canDo->get('core.admin')): ?>
		<div class="col span12">
			<fieldset class="panelform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_FIELDSET_RULES'); ?></span></legend>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
		<div class="clr"></div>
		<?php endif;*/ ?>

		<?php echo Html::input('token'); ?>
	</form>
</div>
