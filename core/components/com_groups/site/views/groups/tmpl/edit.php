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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js()
     ->js('jquery.cycle2', 'system');

//tag editor
$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->tags)));

//are we using the email gateway for group forum
$params =  Component::params('com_groups');
$allowEmailResponses = $params->get('email_comment_processing', 0);
$autoEmailResponses  = $params->get('email_member_groupsidcussionemail_autosignup', 0);

//default logo
$default_logo = DS.'core'.DS.'components'.DS.$this->option.DS.'site'.DS.'assets'.DS.'img'.DS.'group_default_logo.png';

//access levels
$levels = array(
	'anyone'     => Lang::txt('COM_GROUPS_PLUGIN_ANYONE'),
	'registered' => Lang::txt('COM_GROUPS_PLUGIN_REGISTERED'),
	'members'    => Lang::txt('COM_GROUPS_PLUGIN_MEMBERS'),
	'nobody'     => Lang::txt('COM_GROUPS_PLUGIN_DISABLED')
);

//build back link
$host = Request::getVar("HTTP_HOST", "", "SERVER");
$referrer = Request::getVar("HTTP_REFERER", "", "SERVER");

//check to make sure referrer is a valid url
//check to make sure the referrer is a link within the HUB
if (filter_var($referrer, FILTER_VALIDATE_URL) === false || $referrer == "" || strpos($referrer, $host) === false)
{
	$link = Route::url('index.php?option='.$this->option);
}
else
{
	$link = $referrer;
}

//if we are in edit mode we want to redirect back to group
if ($this->task == "edit")
{
	$link = Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn'));
	$title = Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP');
}
else
{
	$title = Lang::txt('COM_GROUPS_ACTION_BACK');
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="btn icon-group" href="<?php echo $link; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php
		foreach ($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

	<?php if ($this->task != 'new' && !$this->group->get('published')) : ?>
		<p class="warning">
			<?php echo Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING'); ?>
		</p>
	<?php endif; ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm" class="full stepper">
		<div class="grid">
			<div class="col span8">
				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_TITLE'); ?></legend>

					<?php if ($this->task != 'new') : ?>
						<input name="cn" type="hidden" value="<?php echo $this->group->get('cn'); ?>" />
					<?php else : ?>
						<label class="group_cn_label">
							<?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_CN'); ?> <span class="required"><?php echo Lang::txt('COM_GROUPS_REQUIRED'); ?></span>
							<input name="cn" id="group_cn_field" type="text" size="35" value="<?php echo $this->group->get('cn'); ?>" autocomplete="off" />
							<span class="hint"><?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_CN_HINT'); ?></span>
						</label>
					<?php endif; ?>
					<label>
						<?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_DESCRIPTION'); ?> <span class="required"><?php echo Lang::txt('COM_GROUPS_REQUIRED'); ?></span>
						<input type="text" name="description" size="35" value="<?php echo stripslashes($this->group->get('description')); ?>" />
					</label>
					<label>
						<?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_TAGS'); ?> <span class="optional"><?php echo Lang::txt('COM_GROUPS_OPTIONAL'); ?></span>
						<?php if (count($tf) > 0) {
							echo $tf[0];
						} else { ?>
							<input type="text" name="tags" value="<?php echo $this->tags; ?>" />
						<?php } ?>

						<span class="hint"><?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_TAGS_HINT'); ?></span>
					</label>

					<label for="public_desc">
						<?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_PUBLIC'); ?> <span class="optional"><?php echo Lang::txt('COM_GROUPS_OPTIONAL'); ?></span>

						<?php
							echo $this->editor('public_desc', $this->escape($this->group->getDescription('raw', 0, 'public')), 35, 8, 'public_desc', array('class' => 'minimal no-footer images macros'));
						?>
					</label>
					<label for="private_desc">
						<?php echo Lang::txt('COM_GROUPS_DETAILS_FIELD_PRIVATE'); ?> <span class="optional"><?php echo Lang::txt('COM_GROUPS_OPTIONAL'); ?></span>
						<?php
							echo $this->editor('private_desc', $this->escape($this->group->getDescription('raw', 0, 'private')), 35, 8, 'private_desc', array('class' => 'minimal no-footer images macros'));
						?>
					</label>
				</fieldset>

				<?php if ($this->task != 'new') : ?>
					<fieldset>
						<legend><?php echo Lang::txt('COM_GROUPS_LOGO_FIELD_TITLE'); ?></legend>
						<p><?php echo Lang::txt('COM_GROUPS_LOGO_FIELD_DESC'); ?></p>
						<?php if ($this->group->isSuperGroup()) : ?>
							<p class="info"><?php echo Lang::txt('COM_GROUPS_LOGO_FIELD_DESC_SUPER_GROUP'); ?></p>
						<?php endif; ?>
						<label id="group-logo-label">
							<select name="group[logo]" id="group_logo" rel="<?php echo $this->group->get('gidNumber'); ?>">
								<option value=""><?php echo Lang::txt('COM_GROUPS_LOGO_FIELD_OPTION_NULL'); ?></option>
								<?php foreach ($this->logos as $logo) { ?>
									<?php
										$remove = PATH_APP . DS . 'site' . DS . 'groups' . DS . $this->group->get('gidNumber') . DS . 'uploads' . DS;
										$sel = (str_replace($remove,"",$logo) == $this->group->get('logo')) ? 'selected' : '';
									?>
									<option <?php echo $sel; ?> value="<?php echo str_replace(JPATH_SITE,"",$logo); ?>"><?php echo str_replace($remove,"",$logo); ?></option>
								<?php } ?>
							</select>
						</label>
						<label>
							<div class="preview" id="logo">
								<div id="logo_picked">
									<?php if ($this->group->get('logo')) { ?>
										<?php $selectedPath = substr(PATH_APP . DS . 'site' . DS . 'groups' . DS . $this->group->get('gidNumber') . DS . 'uploads' . DS . $this->group->get('logo'), strlen(PATH_ROOT)); ?>
										<img src="<?php echo $selectedPath; ?>" alt="<?php echo $this->group->get('cn') ?>" />
									<?php } else { ?>
										<img src="<?php echo $default_logo; ?>" alt="<?php echo $this->group->get('cn') ?>" >
									<?php } ?>
								</div>
							</div>
						</label>
					</fieldset>
				<?php endif; ?>

				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_TITLE'); ?></legend>
					<p><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_DESC'); ?></p>
					<fieldset>
						<legend><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_LEGEND'); ?> <span class="required"><?php echo Lang::txt('COM_GROUPS_REQUIRED'); ?></span></legend>
						<label>
							<input type="radio" class="option" name="join_policy" value="0"<?php if ($this->group->get('join_policy') == 0) { echo ' checked="checked"'; } ?> />
							<strong><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_OPEN_SETTING'); ?></strong>
							<br /><span class="indent"><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_OPEN_SETTING_DESC'); ?></span>
						</label>
						<label>
							<input type="radio" class="option" name="join_policy" value="1"<?php if ($this->group->get('join_policy') == 1) { echo ' checked="checked"'; } ?> />
							<strong><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING'); ?></strong>
							<br /><span class="indent"><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_DESC'); ?></span>
						</label>
						<label class="indent">
							<strong><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_CREDENTIALS'); ?></strong>
							(<?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_CREDENTIALS_DESC'); ?>) <span class="optional"><?php echo Lang::txt('COM_GROUPS_OPTIONAL'); ?></span>
							<textarea name="restrict_msg" rows="5" cols="50"><?php echo htmlentities(stripslashes($this->group->get('restrict_msg'))); ?></textarea>
						</label>
						<label>
							<input type="radio" class="option" name="join_policy" value="2"<?php if ($this->group->get('join_policy') == 2) { echo ' checked="checked"'; } ?> />
							<strong><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_INVITE_SETTING'); ?></strong>
							<br /><span class="indent"><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_INVITE_SETTING_DESC'); ?></span>
						</label>
						<label>
							<input type="radio" class="option" name="join_policy" value="3"<?php if ($this->group->get('join_policy') == 3) { echo ' checked="checked"'; } ?> />
							<strong><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_CLOSED_SETTING'); ?></strong>
							<br /><span class="indent"><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_CLOSED_SETTING_DESC'); ?></span>
						</label>
					</fieldset>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PRIVACY_SETTINGS_TITLE'); ?></legend>
					<p><?php echo Lang::txt('COM_GROUPS_PRIVACY_SETTINGS_DESC'); ?></p>
					<fieldset>
						<legend><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_LEGEND'); ?> <span class="required"><?php echo Lang::txt('COM_GROUPS_REQUIRED'); ?></span></legend>
						<label>
							<input type="radio" class="option" name="discoverability" value="0"<?php if ($this->group->get('discoverability') == 0) { echo ' checked="checked"'; } ?> />
							<strong><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_VISIBLE_SETTING'); ?></strong>
							<br /><span class="indent"><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_VISIBLE_SETTING_DESC'); ?></span>
						</label>
						<label>
							<input type="radio" class="option" name="discoverability" value="1"<?php if ($this->group->get('discoverability') == 1) { echo ' checked="checked"'; } ?> />
							<strong><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_HIDDEN_SETTING'); ?></strong>
							<br /><span class="indent"><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_HIDDEN_SETTING_DESC'); ?></span>
						</label>
					</fieldset>

					<fieldset>
						<legend><?php echo Lang::txt('COM_GROUPS_ACCESS_SETTINGS_TITLE'); ?></legend>
						<p><?php echo Lang::txt('COM_GROUPS_ACCESS_SETTINGS_DESC'); ?></p>

						<fieldset class="preview">
							<legend><?php echo Lang::txt('COM_GROUPS_ACCESS_SETTINGS_DESC_DESC'); ?></legend>
							<ul id="access">
								<img src="<?php echo $default_logo; ?>" alt="<?php echo $this->group->get('cn') ?>" >
								<?php for ($i=0; $i<count($this->hub_group_plugins); $i++) { ?>
									<?php if ($this->hub_group_plugins[$i]['display_menu_tab']) { ?>
										<li class="group_access_control_<?php echo strtolower($this->hub_group_plugins[$i]['title']); ?>">
											<input type="hidden" name="group_plugin[<?php echo $i; ?>][name]" value="<?php echo $this->hub_group_plugins[$i]['name']; ?>">
											<span class="menu_item_title"><?php echo $this->hub_group_plugins[$i]['title']; ?></span>
											<select name="group_plugin[<?php echo $i; ?>][access]">
												<?php foreach ($levels as $level => $name) { ?>
													<?php $sel = ($this->group_plugin_access[$this->hub_group_plugins[$i]['name']] == $level) ? 'selected' : ''; ?>
													<?php if (($this->hub_group_plugins[$i]['name'] == 'overview' && $level != 'nobody') || $this->hub_group_plugins[$i]['name'] != 'overview') { ?>
														<option <?php echo $sel; ?> value="<?php echo $level; ?>"><?php echo $name; ?></option>
													<?php } ?>
												<?php } ?>
											</select>
										</li>
									<?php } ?>
								<?php } ?>
							</ul>
						</fieldset>
					</fieldset>
				</fieldset>

				<?php if ($allowEmailResponses) : ?>
					<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_EMAIL_SETTINGS_TITLE'); ?></legend>
					<p><?php echo Lang::txt('COM_GROUPS_EMAIL_SETTINGS_DESC'); ?></p>
						<fieldset>
							<legend><?php echo Lang::txt('COM_GROUPS_EMAIL_SETTING_FORUM_SECTION_LEGEND'); ?> <span class="optional"><?php echo Lang::txt('COM_GROUPS_OPTIONAL'); ?></span></legend>
							<label>
								<input type="checkbox" class="option" name="discussion_email_autosubscribe" value="1"
									<?php if ($this->group->get('discussion_email_autosubscribe', null) == 1
											|| ($this->group->get('discussion_email_autosubscribe', null) == null && $autoEmailResponses)) { echo ' checked="checked"'; } ?> />
								<strong><?php echo Lang::txt('COM_GROUPS_EMAIL_SETTING_FORUM_AUTO_SUBSCRIBE'); ?></strong> <br />
								<span class="indent">
									<?php echo Lang::txt('COM_GROUPS_EMAIL_SETTINGS_FORUM_AUTO_SUBSCRIBE_NOTE'); ?>
								</span>
							</label>
						</fieldset>
					</fieldset>
				<?php endif; ?>

				<fieldset id="page-settings">
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_SETTINGS_TITLE'); ?></legend>
					<p><?php echo Lang::txt('COM_GROUPS_PAGES_SETTINGS_DESC'); ?></p>
					<?php
						$params   = new \Hubzero\Config\Registry($this->group->get('params'));
						$comments = $params->get('page_comments', $this->config->get('page_comments', 0));
						$author   = $params->get('page_author', $this->config->get('page_author', 0));
					?>
					<div class="grid">
						<div class="col span6">
							<label><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS'); ?>:
								<select name="params[page_comments]">
									<option <?php if ($comments == 0) { echo 'selected="selected"'; } ?> value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO'); ?></option>
									<option <?php if ($comments == 1) { echo 'selected="selected"'; } ?> value="1"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES'); ?></option>
									<option <?php if ($comments == 2) { echo 'selected="selected"'; } ?> value="2"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK'); ?></option>
								</select>
								<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS_HINT'); ?></span>
							</label>
						</div>
						<div class="col span6 omega">
							<label><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR'); ?>:
								<select name="params[page_author]">
									<option <?php if ($author == 0) { echo 'selected="selected"'; } ?> value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO'); ?></option>
									<option <?php if ($author == 1) { echo 'selected="selected"'; } ?> value="1"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES'); ?></option>
								</select>
								<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_HINT'); ?></span>
							</label>
						</div>
					</div>
				</fieldset>
			</div>

			<div class="col span4 omega floating-iframe-col">
				<?php if ($this->group->get('gidNumber')) : ?>
					<div class="floating-iframe-container">
						<iframe class="floating-iframe" src="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('gidNumber').'&controller=media&task=filebrowser&tmpl=component'); ?>"></iframe>
					</div>
				<?php else : ?>
					<p><em><?php echo Lang::txt('COM_GROUPS_EDIT_MUST_SAVE_TO_UPLOAD_IMAGES'); ?></em></p>
				<?php endif; ?>
			</div>
		</div>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_GROUPS_EDIT_SUBMIT_BTN_TEXT'); ?>" />
		</p>

		<?php echo Html::input('token'); ?>

		<input type="hidden" name="published" value="<?php echo $this->group->get('published'); ?>" />
        <input type="hidden" name="gidNumber" value="<?php echo ($this->group->get('gidNumber') != '' ? $this->group->get('gidNumber') : 0); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />
	</form>
</section><!-- / .section -->
