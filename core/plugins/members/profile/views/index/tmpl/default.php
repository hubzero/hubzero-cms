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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die( 'Restricted access');

$this->css()
     ->js()
     ->js('jquery.fileuploader.js', 'system');

//flags for not logged in and not user
$loggedin = false;
$isUser = false;

$database = App::get('db');

//if we are logged in set logged in flag
if (!User::isGuest())
{
	$loggedin = true;
}

//if we are this user set user flag
if (User::get('id') == $this->profile->get("uidNumber"))
{
	$isUser = true;
}

//registration update
$update_missing = array();
if (isset($this->registration_update))
{
	$update_missing = $this->registration_update->_missing;
}

$invalid = array();
if (isset($this->registration_update))
{
	$invalid = $this->registration_update->_invalid;
}

//incremental registration
require_once PATH_CORE . '/components/com_members/tables/incremental/awards.php';
require_once PATH_CORE . '/components/com_members/tables/incremental/groups.php';
require_once PATH_CORE . '/components/com_members/tables/incremental/options.php';

$uid = (int)$this->profile->get('uidNumber');
$incrOpts = new ModIncrementalRegistrationOptions;
$isIncrementalEnabled = $incrOpts->isEnabled($uid);
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<div id="profile-page-content" data-url="<?php echo Route::url('index.php?option=com_members&id=' . $this->profile->get('uidNumber') . '&active=profile'); ?>">
	<h3 class="section-header">
		<?php echo Lang::txt('PLG_MEMBERS_PROFILE'); ?>
	</h3>

	<?php if (count($invalid) > 0) : ?>
		<div class="error member-update-missing">
			<strong><?php echo Lang::txt('PLG_MEMBERS_PROFILE_USER_INVALID'); ?></strong>
			<ul>
				<?php foreach ($invalid as $i) : ?>
					<li><?php echo $i; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php elseif (count($update_missing) > 0) : ?>
		<?php if (count($update_missing) == 1 && in_array("usageAgreement",array_keys($update_missing))) : ?>
		<?php else: ?>
			<div class="error member-update-missing">
				<strong><?php echo Lang::txt('PLG_MEMBERS_PROFILE_UPDATE_BEFORE_CONTINUING'); ?></strong>
				<ul>
					<?php foreach ($update_missing as $um) : ?>
						<li><?php echo $um; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($isUser) : ?>
	<ul>
		<li id="member-profile-completeness" class="hide">
			<?php echo Lang::txt('PLG_MEMBERS_PROFILE_COMPLETENESS'); ?>
			<div id="meter">
				<span id="meter-percent" data-percent="<?php echo $this->completeness; ?>" data-percent-level="<?php echo @$this->completeness_level; ?>" style="width:0%"></span>
			</div>
			<?php if ($isUser && $isIncrementalEnabled) : ?>
				<span id="completeness-info"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_COMPLETENESS_MEANS'); ?></span>
			<?php endif; ?>
		</li>
	</ul>
	<?php endif; ?>

	<?php
		if ($isUser && $isIncrementalEnabled)
		{
			$awards = new ModIncrementalRegistrationAwards($this->profile);
			$awards = $awards->award();

			$increm  = '<div id="award-info">';
			$increm .= '<p>' . Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_OFFERING_POINTS', Route::url('index.php?option=com_store')) . '</p>';

			if ($awards['prior'])
			{
				$increm .= '<p>' . Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_AWARDED_POINTS', $awards['prior']) . '</p>';
			}

			if ($awards['new'])
			{
				$increm .= '<p>' . Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_EARNED_POINTS', $awards['new']) . '</p>';
			}

			$increm .= '<p>' . Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_EARN_MORE_POINTS', $incrOpts->getAwardPerField(), Route::url('index.php?option=com_store'), Route::url('index.php?option=com_answers'), Route::url('index.php?option=com_wishlist')) .'</p>';

			$increm .= '</div>';
			$increm .= '<div id="wallet"><span>'.($awards['prior'] + $awards['new']) . '</span></div>';
			$increm .= '<script type="text/javascript">
							window.bonus_eligible_fields = '.json_encode($awards['eligible']).';
							window.bonus_amount = '.$incrOpts->getAwardPerField().';
						</script>';
			echo $increm;

			$this->js('incremental', 'com_members');
		}
	?>

	<?php if (isset($update_missing) && in_array("usageAgreement",array_keys($update_missing))) : ?>
		<div id="usage-agreement-popup">
			<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" data-section-registration="usageAgreement" data-section-profile="usageAgreement">
				<h2><?php echo Lang::txt('PLG_MEMBERS_PROFILE_NEW_TERMS_OF_USE'); ?></h2>
				<div id="usage-agreement-box">
					<?php /*<iframe id="usage-agreement" src="<?php echo Request::base(true); ?>/legal/terms?tmpl=component"></iframe>*/ ?>
					<div id="usage-agreement">
						<?php
						$db = App::get('db');
						$db->setQuery("SELECT * FROM `#__content` WHERE `alias`=" . $db->quote('terms'));
						$page = $db->loadObject();
						if ($page && $page->id)
						{
							$params = new \Hubzero\Config\Registry($page->attribs);
							$results = Event::trigger('content.onContentPrepare', array ('com_content.article', &$page, &$params, 0));
							echo $page->introtext;
						}
						?>
					</div>
					<div id="usage-agreement-last-chance">
						<h3><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ARE_YOU_SURE'); ?></h3>
						<p><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ARE_YOU_SURE_EXPLANATION'); ?></p>
					</div>
				</div>
				<div id="usage-agreement-buttons">
					<button class="section-edit-cancel usage-agreement-do-not-agree"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_TERMS_NOT_AGREE'); ?></button>
					<button class="section-edit-submit"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_TERMS_AGREE'); ?></button>
				</div>
				<div id="usage-agreement-last-chance-buttons">
					<button class="section-edit-cancel usage-agreement-back-to-agree"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_TERMS_GO_BACK'); ?></button>
					<button class="section-edit-cancel usage-agreement-dont-accept"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_TERMS_I_DO_NOT_AGREE'); ?></button>
				</div>
				<input type="hidden" name="declinetou" value="0" />
				<input type="hidden" name="usageAgreement" value="1" />
				<input type="hidden" name="field_to_check[]" value="usageAgreement" />
				<input type="hidden" name="option" value="com_members" />
				<input type="hidden" name="controller" value="profiles" />
				<input type="hidden" name="id" value="<?php echo User::get("id"); ?>" />
				<input type="hidden" name="task" value="save" />
				<?php echo Html::input('token'); ?>
			</form>
		</div>
	<?php endif; ?>

	<ul id="profile">
		<?php if ($isUser) : ?>
			<li class="profile-name section hidden">
				<div class="section-content">
					<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_NAME'); ?></div>
					<div class="value"><?php echo $this->escape($this->profile->get('name')); ?></div>
					<br class="clear" />
					<?php
						$name  = '<label class="side-by-side three">' . Lang::txt('PLG_MEMBERS_PROFILE_FIRST_NAME') . ' <input type="text" name="name[first]" id="" class="input-text" value="'.$this->escape($this->profile->get("givenName")).'" /></label>';
						$name .= '<label class="side-by-side three">' . Lang::txt('PLG_MEMBERS_PROFILE_MIDDLE_NAME') . ' <input type="text" name="name[middle]" id="" class="input-text" value="'.$this->escape($this->profile->get("middleName")).'" /></label>';
						$name .= '<label class="side-by-side three no-padding-right">' . Lang::txt('PLG_MEMBERS_PROFILE_LAST_NAME') . ' <input type="text" name="name[last]" id="" class="input-text" value="'.$this->escape($this->profile->get("surname")).'" /></label>';

						$this->view('default', 'edit')
						     ->set('registration_field', 'name')
						     ->set('profile_field', 'name')
						     ->set('registration', $this->registration->Fullname)
						     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_NAME'))
						     ->set('profile', $this->profile)
						     ->set('isUser', $isUser)
						     ->set('inputs', $name)
						     ->set('access', '')
						     ->display();
					?>
				</div>
				<div class="section-edit">
					<a class="edit-profile-section" href="#">
						<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
					</a>
				</div>
			</li>
		<?php endif; ?>

		<?php if (!Plugin::isEnabled('members', 'account')) : ?>
			<?php if ($isUser) : ?>
				<li class="profile-password section hidden">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD'); ?></div>
						<div class="value">***************</div>
						<br class="clear" />
						<div class="section-edit-container">
							<!--
							<div class="edit-profile-title"><h2>Change Password</h2></div>
							<a href="#" class="edit-profile-close">&times;</a>
							-->
							<div class="section-edit-content">
								<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" data-section-registation="password" data-section-profile="password">
									<span class="section-edit-errors"></span>
									<label for="password">
										<?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_CURRENT'); ?>
										<input type="password" name="oldpass" id="password" class="input-text" />
									</label>
									<label for="newpass" class="side-by-side">
										<?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_NEW'); ?>
										<input type="password" name="newpass" id="newpass" class="input-text" />
									</label>
									<label for="newpass2" class="side-by-side no-padding-right">
										<?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_CONFIRM'); ?>
										<input type="password" name="newpass2" id="newpass2" class="input-text" />
									</label>
									<input type="hidden" name="change" value="1" />
									<input type="submit" class="section-edit-submit" value="Save" />
									<input type="reset" class="section-edit-cancel" value="Cancel" />
									<input type="hidden" name="option" value="com_members" />
									<input type="hidden" name="controller" value="profiles" />
									<input type="hidden" name="id" value="<?php echo $this->profile->get('uidNumber'); ?>" />
									<input type="hidden" name="task" value="changepassword" />
									<input type="hidden" name="no_html" value="1" />
									<?php echo Html::input('token'); ?>
								</form>
							</div>
						</div>
					</div>
					<div class="section-edit">
						<a class="edit-profile-section" href="#">
							<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
					</div>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Organization != REG_HIDE) : ?>
			<?php if ($this->params->get('access_org') == 0
					|| ($this->params->get('access_org') == 1 && $loggedin)
					|| ($this->params->get('access_org') == 2 && $isUser)
					) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_org') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get('organization') == '' || is_null($this->profile->get('organization')))
						{
							$cls .= ($isUser) ? ' hidden' : ' hide';
						}
					?>
				<li class="profile-org section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ORGANIZATION'); ?></div>
						<div class="value">
							<?php
								$org = $this->escape(stripslashes($this->profile->get('organization')));
								echo ($org) ? $org : Lang::txt('PLG_MEMBERS_PROFILE_ENTER_ORG');
							?>
						</div>
						<br class="clear" />
						<?php
							//get list of organizations from db
							include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organization.php');

							$xo = new \Components\Members\Tables\Organization($database);
							$orgs = $xo->find('list');

							$organization_alt = '';

							//create select for organizations and optional text input
							$organizations  = '<select name="org" class="input-select">';
							$organizations .= '<option value="">' . Lang::txt('PLG_MEMBERS_PROFILE_SELECT_OR_ENTER_BELOW') . '</option>';
							foreach ($orgs as $o)
							{
								$sel = ($o->organization == $this->profile->get("organization")) ? "selected=\"selected\"" : "";
								if ($o->organization == $this->profile->get("organization"))
								{
									$sel = 'selected="selected"';
									$organization_alt = $o->organization;
								}
								$organizations .= '<option ' . $sel . ' value="' . $o->organization . '">' . $o->organization . '</option>';
							}
							$organizations .= '</select>';
							$organization_alt = ($organization_alt ?: $this->escape($this->profile->get('organization')));
							$organizations_text = "<input type=\"text\" name=\"orgtext\" class=\"input-text\" value=\"{$organization_alt}\" />";

							$this->view('default', 'edit')
							     ->set('registration_field', 'org')
							     ->set('profile_field', 'organization')
							     ->set('registration', $this->registration->Organization)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_ORGANIZATION'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_ORGANIZATION') . $organizations . '</label><label>' . Lang::txt('PLG_MEMBERS_PROFILE_ENTER_ORG_BELOW') . $organizations_text . '</label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[org]', $this->params->get('access_org'), 'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Employment != REG_HIDE) : ?>
			<?php if ($this->params->get('access_orgtype') == 0
					|| ($this->params->get('access_orgtype') == 1 && $loggedin)
					|| ($this->params->get('access_orgtype') == 2 && $isUser)
					) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_orgtype') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get("orgtype") == "" || is_null($this->profile->get("orgtype")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
					?>
				<li class="profile-orgtype section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_EMPLOYMENT_TYPE'); ?></div>
						<?php
							//get organization types from db
							include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'organizationtype.php');

							$xot = new \Components\Members\Tables\OrganizationType($database);
							$orgtypes = $xot->find('list');

							//output value
							$orgtype = $this->escape($this->profile->get('orgtype'));
							foreach ($orgtypes as $ot)
							{
								$orgtype = ($ot->type == $this->profile->get('orgtype') ? $this->escape($ot->title) : $orgtype);
							}
						?>
						<div class="value">
							<?php echo ($orgtype) ? $orgtype : Lang::txt('PLG_MEMBERS_PROFILE_ENTER_EMPLOYMENT_TYPE'); ?>
						</div>
						<br class="clear" />
						<?php
							//build select of org types
							$organization_types  = '<select name="orgtype" class="input-select">';
							foreach ($orgtypes as $orgtype)
							{
								$sel = ($orgtype->type == $this->profile->get('orgtype')) ? ' selected="selected"' : '';
								$organization_types .= '<option' . $sel . ' value="' . $this->escape($orgtype->type) . '">' . $this->escape($orgtype->title) . '</option>';
							}
							$organization_types .= "</select>";

							$this->view('default', 'edit')
							     ->set('registration_field', 'orgtype')
							     ->set('profile_field', 'orgtype')
							     ->set('registration', $this->registration->Employment)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_EMPLOYMENT_TYPE'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_EMPLOYMENT_TYPE') . $organization_types . '</label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[orgtype]',$this->params->get('access_orgtype'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->profile->get('email')) : ?>
			<?php if ($this->params->get('access_email', 2) == 0
					|| ($this->params->get('access_email', 2) == 1 && $loggedin)
					|| ($this->params->get('access_email', 2) == 2 && $isUser)
					) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_email', 2) == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get("email") == "" || is_null($this->profile->get("email")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
					?>
				<li class="profile-email section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_EMAIL'); ?></div>
						<div class="value">
							<a class="email" href="mailto:<?php echo \Components\Members\Helpers\Html::obfuscate($this->profile->get('email')); ?>" rel="nofollow">
								<?php echo \Components\Members\Helpers\Html::obfuscate($this->profile->get('email')); ?>
							</a>
						</div>
						<br class="clear" />
						<?php
							$this->view('default', 'edit')
							     ->set('registration_field', 'email')
							     ->set('profile_field', 'email')
							     ->set('registration', $this->registration->Email)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_EMAIL'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label class="side-by-side">' . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_VALID') . ' <input type="text" class="input-text" name="email" id="profile-email" value="' . $this->escape($this->profile->get('email')) . '" /></label>'
												. '<label class="side-by-side no-padding-right">' . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_CONFIRM') . ' <input type="text" class="input-text" name="email2" id="profile-email2" value="' . $this->escape($this->profile->get('email')) . '" /></label>'
												. '<br class="clear" /><p class="warning no-margin-top">' . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_WARNING') . '</p>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[email]', $this->params->get('access_email'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->ORCID != REG_HIDE) : ?>
			<?php if ($this->params->get('access_orcid') == 0
					|| ($this->params->get('access_orcid') == 1 && $loggedin)
					|| ($this->params->get('access_orcid') == 2 && $isUser)
				) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_orcid') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get('orcid') == '' || is_null($this->profile->get('orcid')))
						{
							$cls .= ($isUser) ? ' hidden' : ' hide';
						}
						if (isset($update_missing) && in_array('orcid', array_keys($update_missing)))
						{
							$cls = str_replace(' hide', '', $cls);
							$cls .= ' missing';
						}
					?>
				<li class="profile-web section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_ORCID'); ?></div>
						<?php
							$url = ($this->profile->get('orcid')) ? '<a class="orcid" rel="external" href="http://orcid.org/' . $this->profile->get('orcid') . '">' . $this->profile->get('orcid') . '</a>' : Lang::txt('PLG_MEMBERS_PROFILE_ORCID_ENTER');
						?>
						<div class="value"><?php echo $url; ?></div>
						<br class="clear" />
						<?php
							$this->view('default', 'edit')
							     ->set('registration_field', 'orcid')
							     ->set('profile_field', 'orcid')
							     ->set('registration', $this->registration->ORCID)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_ORCID'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<div class="grid">
				<div class="col span9">
					<label>
						' . Lang::txt('PLG_MEMBERS_PROFILE_ORCID') . '
						<input type="text" class="input-text" name="orcid" id="orcid" value="'. $this->escape($this->profile->get('orcid')) .'" />
					</label>
					<input type="hidden" name="base_uri" id="base_uri" value="' . rtrim(Request::base(true), '/') . '" />
				</div>
				<div class="col span3 omega">
					<a class="btn icon-search" id="orcid-fetch" href="' . Route::url('index.php?option=com_members&controller=orcid') . '">' . Lang::txt('PLG_MEMBERS_PROFILE_ORCID_FIND') . '</a>
				</div>
			</div>
			<p><img src="' . $this->img('orcid-logo.png', 'com_members') . '" width="80" alt="ORCID" /> ' . Lang::txt('PLG_MEMBERS_PROFILE_ORCID_ABOUT') . '</p>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[orcid]', $this->params->get('access_orcid'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->URL != REG_HIDE) : ?>
			<?php if ($this->params->get('access_url') == 0
					|| ($this->params->get('access_url') == 1 && $loggedin)
					|| ($this->params->get('access_url') == 2 && $isUser)
				) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_url') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get("url") == "" || is_null($this->profile->get("url")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("web", array_keys($update_missing)))
						{
							$cls = str_replace(' hide', '', $cls);
							$cls .= ' missing';
						}
					?>
				<li class="profile-web section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_WEBSITE'); ?></div>
						<?php
							$url = stripslashes($this->profile->get('url'));
							if ($url)
							{
								$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)";
								if (!preg_match("/$UrlPtn/", $url))
								{
									$url = 'http://' . $url;
								}
							}
							$title = Lang::txt('PLG_MEMBERS_PROFILE_WEBSITE_MEMBERS', $this->profile->get('name'));
							$url = ($url) ? '<a class="url" rel="external" title="' . $title . '" href="' . $url . '">' . $url . '</a>' : Lang::txt('PLG_MEMBERS_PROFILE_WEBSITE_ENTER');
						?>
						<div class="value"><?php echo $url; ?></div>
						<br class="clear" />
						<?php
							$this->view('default', 'edit')
							     ->set('registration_field', 'web')
							     ->set('profile_field', 'url')
							     ->set('registration', $this->registration->URL)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_WEBSITE'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_WEBSITE') . '<input type="text" class="input-text" name="web" id="profile-url" value="' . $this->escape($this->profile->get('url')) . '" /></label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[url]', $this->params->get('access_url'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Phone != REG_HIDE) : ?>
			<?php if ($this->params->get('access_phone') == 0
					|| ($this->params->get('access_phone') == 1 && $loggedin)
					|| ($this->params->get('access_phone') == 2 && $isUser)
				) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_phone') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get("phone") == '' || is_null($this->profile->get("phone")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("phone",array_keys($update_missing)))
						{
							$cls = str_replace(" hide", '', $cls);
							$cls .= ' missing';
						}
					?>
				<li class="profile-phone section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_TELEPHONE'); ?></div>
						<?php
							$tel = $this->escape($this->profile->get('phone'));
							//$tel = str_replace(".","-",$tel);
							$tel = str_replace(' ', '-', $tel);
							//$tel = ($tel) ? "<a class=\"phone\" href=\"tel:{$tel}\">{$tel}</a>" : Lang::txt('Enter your Phone Number');
							$tel = ($tel) ? $tel : Lang::txt('PLG_MEMBERS_PROFILE_TELEPHONE_ENTER');
						?>
						<div class="value"><?php echo $tel; ?></div>
						<br class="clear" />
						<?php
							$this->view('default', 'edit')
							     ->set('registration_field', 'phone')
							     ->set('profile_field', 'phone')
							     ->set('registration', $this->registration->Phone)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_TELEPHONE'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_TELEPHONE') . ' <input type="text" class="input-text" name="phone" id="profile-phone" value="'.$this->escape($this->profile->get('phone')) .'" /></label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[phone]',$this->params->get('access_phone'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->address != REG_HIDE) : ?>
			<?php if ($this->params->get('access_address') == 0
					|| ($this->params->get('access_address') == 1 && $loggedin)
					|| ($this->params->get('access_address') == 2 && $isUser)
				) : ?>
				<?php
					// Get member addresses
					$db = App::get('db');
					$membersAddress = new \Components\Members\Tables\Address($db);
					$addresses = $membersAddress->getAddressesForMember($this->profile->get("uidNumber"));

					$cls = '';
					if ($this->params->get('access_address') == 2)
					{
						$cls .= 'private';
					}
					if (count($addresses) < 1)
					{
						$cls .= ($isUser) ? ' hidden' : ' hide';
					}
					if (isset($update_missing) && in_array('address', array_keys($update_missing)))
					{
						$cls  = str_replace(' hide', '', $cls);
						$cls .= ' missing';
					}
				?>
				<li class="profile-address section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key">
							<?php echo Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS'); ?>
						</div>
						<div class="value">
							<?php
							$this->view('default', 'address')
							     ->set('addresses', $addresses)
							     ->set('displayEditLinks', $isUser)
							     ->set('profile', $this->profile)
							     ->display();
							?>
						</div>
						<br class="clear" />
						<?php
							$addAddressLink = '<a class="btn add add-address" href="' . Route::url($this->profile->getLink() . '&active=profile&action=addaddress') . '">' . Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_ADD') . '</a>';

							$this->view('default', 'edit')
							     ->set('registration_field', 'address')
							     ->set('profile_field', 'address')
							     ->set('registration', $this->registration->address)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_TITLE'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS') . '<br />' . $addAddressLink . '</label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[address]', $this->params->get('access_address'), 'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->params->get('access_bio') == 0
				|| ($this->params->get('access_bio') == 1 && $loggedin)
				|| ($this->params->get('access_bio') == 2 && $isUser)
			) : ?>
				<?php
					$cls = '';
					if ($this->params->get('access_bio') == 2)
					{
						$cls .= 'private';
					}
					if ($this->profile->get("bio") == "" || is_null($this->profile->get("bio")))
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("bio",array_keys($update_missing)))
					{
						$cls = str_replace(' hide', '', $cls);
						$cls .= ' missing';
					}
				?>
			<li class="profile-bio section <?php echo $cls; ?>">
				<div class="section-content">
					<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_BIOGRAPHY'); ?></div>
					<?php
						if ($this->profile->get('bio'))
						{
							$bio = $this->profile->getBio('parsed');
						}
						else
						{
							$bio = Lang::txt('PLG_MEMBERS_PROFILE_BIOGRAPHY_ENTER');
						}
					?>
					<div class="value"><?php echo $bio; ?></div>
					<br class="clear" />
					<?php
						$bio = $this->editor('profile[bio]', $this->escape(stripslashes($this->profile->getBio('raw'))), 100, 15, 'profile_bio',array('class' => 'minimal no-footer'));

						$this->view('default', 'edit')
						     ->set('registration_field', 'bio')
						     ->set('profile_field', 'bio')
						     ->set('registration', $this->registration->Bio)
						     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_BIOGRAPHY'))
						     ->set('profile', $this->profile)
						     ->set('isUser', $isUser)
						     ->set('inputs', '<label for="profile_bio">' . Lang::txt('PLG_MEMBERS_PROFILE_BIOGRAPHY') . $bio . '</label>')
						     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[bio]',$this->params->get('access_bio'),'input-select') . '</label>')
						     ->display();
					?>
				</div>
				<?php if ($isUser) : ?>
					<div class="section-edit">
						<a class="edit-profile-section" href="#">
							<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
					</div>
				<?php endif; ?>
			</li>
		<?php endif; ?>

		<?php if ($this->registration->Interests != REG_HIDE) : ?>
			<?php if ($this->params->get('access_tags') == 0
					|| ($this->params->get('access_tags') == 1 && $loggedin)
					|| ($this->params->get('access_tags') == 2 && $isUser)
				) : ?>
				<?php
					$cls = '';

					$mt = new \Components\Members\Models\Tags($this->profile->get('uidNumber'));
					$tags = $mt->render();
					$tag_string = $mt->render('string');

					if ($this->params->get('access_tags') == 2)
					{
						$cls .= 'private';
					}
					if ($tag_string == "")
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("interests",array_keys($update_missing)))
					{
						$cls = str_replace(' hide', '', $cls);
						$cls .= ' missing';
					}
				?>
				<li class="profile-interests section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_INTERESTS'); ?></div>
						<div class="value">
							<?php echo ($tags) ? $tags : Lang::txt('PLG_MEMBERS_PROFILE_INTERESTS_ENTER'); ?>
						</div>
						<br class="clear" />
						<?php
							$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','',stripslashes($tag_string))));

							if (count($tf) > 0)
							{
								$interests = $tf[0];
							} else
							{
								$interests = "\t\t\t" . '<input type="text" name="tags" value="'. $this->escape($tag_string) .'" />' . "\n";
							}

							$this->view('default', 'edit')
							     ->set('registration_field', 'interests')
							     ->set('profile_field', 'interests')
							     ->set('registration', $this->registration->Interests)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_INTERESTS'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_INTERESTS') . $interests . '</label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[tags]',$this->params->get('access_tags'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Citizenship != REG_HIDE) : ?>
			<?php if ($this->params->get('access_countryorigin') == 0
					|| ($this->params->get('access_countryorigin') == 1 && $loggedin)
					|| ($this->params->get('access_countryorigin') == 2 && $isUser)
				) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_countryorigin') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get("countryorigin") == '' || is_null($this->profile->get("countryorigin")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("countryorigin",array_keys($update_missing)))
						{
							$cls = str_replace(" hide", '', $cls);
							$cls .= ' missing';
						}

						// get countries list
						$co = \Hubzero\Geocode\Geocode::countries();
					?>
				<li class="profile-countryorigin section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_CITIZENSHIP'); ?></div>
						<?php
							$img = '';
							$citizenship = '';
							if (is_file(PATH_CORE . DS . 'components' . DS . $this->option . DS . 'site' . DS . 'assets' . DS . 'img' . DS . 'flags' . DS . strtolower($this->profile->get('countryorigin')) . '.gif'))
							{
								$img = '<img src="' . rtrim(Request::base(true), '/') . '/core/components/' . $this->option . '/site/assets/img/flags/' . strtolower($this->profile->get('countryorigin')) . '.gif" alt="' . $this->escape($this->profile->get('countryorigin')) . ' ' . Lang::txt('PLG_MEMBERS_PROFILE_FLAG') . '" /> ';
							}

							// get the country name
							foreach ($co as $c)
							{
								if (strtoupper($c->code) == strtoupper($this->profile->get('countryorigin')))
								{
									$citizenship = $c->name;
								}
							}
							// prepend image if we have them
							$citizenship = $img . $citizenship;
						?>
						<div class="value">
							<?php echo ($citizenship) ? $citizenship : Lang::txt('PLG_MEMBERS_PROFILE_CITIZENSHIP_ENTER'); ?>
						</div>
						<br class="clear" />

						<?php
							$countries  = '<select name="corigin" id="corigin" class="input-select">';
							$countries .= '<option value="">' . Lang::txt('PLG_MEMBERS_PROFILE_SELECT') . '</option>';
							foreach ($co as $c)
							{
								$countries .= '<option value="' . $c->code . '"';
								if (strtoupper($this->profile->get('countryorigin')) == strtoupper($c->code))
								{
									$countries .= ' selected="selected"';
								}
								$countries .= '>' . $this->escape($c->name) . '</option>';
							}
							$countries .= '</select>';

							$yes = ""; $no = "";
							if (strcasecmp($this->profile->get('countryorigin'),'US') == 0)
							{
								$yes = 'checked="checked"';
							}
							elseif ($this->profile->get('countryorigin') != "" && (strcasecmp($this->profile->get('countryorigin'),'US') != 0) )
							{
								$no = 'checked="checked"';
							}

							$citizenship  = '<br /><label><input type="radio" name="corigin_us" id="corigin_usyes" value="yes" '.$yes.' /> ' . Lang::txt('PLG_MEMBERS_PROFILE_YES') . '</label> &nbsp;&nbsp;&nbsp;';
							$citizenship .= '<label><input type="radio" name="corigin_us" id="corigin_usno" value="no" '.$no.' /> ' . Lang::txt('PLG_MEMBERS_PROFILE_NO') . '</label> ';

							$this->view('default', 'edit')
							     ->set('registration_field', 'countryorigin')
							     ->set('profile_field', 'countryorigin')
							     ->set('registration', $this->registration->Citizenship)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_CITIZENSHIP'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<div class="side-by-side">' . Lang::txt('PLG_MEMBERS_PROFILE_CITIZEN_OF_USA') . $citizenship . '</div>'
												. '<div class="side-by-side"><label for="corigin">' . Lang::txt('PLG_MEMBERS_PROFILE_CITIZEN_OR_RESIDENT') . $countries . '</label></div>')
							     ->set('access', '<div class="block"><label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[countryorigin]', $this->params->get('access_countryorigin'), 'input-select') . '</label></div>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Residency != REG_HIDE) : ?>
			<?php if ($this->params->get('access_countryresident') == 0
					|| ($this->params->get('access_countryresident') == 1 && $loggedin)
					|| ($this->params->get('access_countryresident') == 2 && $isUser)
				) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_countryresident') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get("countryresident") == "" || is_null($this->profile->get("countryresident")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("countryresident", array_keys($update_missing)))
						{
							$cls = str_replace(' hide', '', $cls);
							$cls .= ' missing';
						}
						// get countries list
						$co = \Hubzero\Geocode\Geocode::countries();
					?>
				<li class="profile-countryresident section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_RESIDENCE'); ?></div>
						<?php
							$img = '';
							$residence = '';
							if (is_file(PATH_CORE . DS . 'components' . DS . $this->option . DS . 'site' . DS . 'assets' . DS . 'img' . DS . 'flags' . DS . strtolower($this->profile->get('countryresident')) . '.gif'))
							{
								$img = '<img src="' . rtrim(Request::base(true), '/') . '/core/components/' . $this->option . '/site/assets/img/flags/' . strtolower($this->profile->get('countryresident')) . '.gif" alt="' . $this->escape($this->profile->get('countryresident')) . ' ' . Lang::txt('PLG_MEMBERS_PROFILE_FLAG') . '" /> ';
							}

							// get the country name
							foreach ($co as $c)
							{
								if (strtoupper($c->code) == strtoupper($this->profile->get('countryresident')))
								{
									$residence = $c->name;
								}
							}
							// prepend image if we have them
							$residence = $img . $residence;
						?>
						<div class="value">
							<?php echo ($residence) ? $residence : Lang::txt('PLG_MEMBERS_PROFILE_RESIDENCE_ENTER'); ?>
						</div>
						<br class="clear" />
						<?php
							$countries = '<select name="cresident" id="cresident" class="input-select">';
							$countries .= '<option value="">' . Lang::txt('PLG_MEMBERS_PROFILE_SELECT') . '</option>';
							foreach ($co as $c)
							{
								$countries .= '<option value="' . $c->code . '"';
								if (strtoupper($this->profile->get('countryresident')) == strtoupper($c->code))
								{
									$countries .= ' selected="selected"';
								}
								$countries .= '>' . $this->escape($c->name) . '</option>';
							}
							$countries .= '</select>';

							$yes = '';
							$no  = '';
							if (strcasecmp($this->profile->get('countryresident'), 'US') == 0)
							{
								$yes = 'checked="checked"';
							}
							elseif ($this->profile->get('countryresident') != '' && (strcasecmp($this->profile->get('countryresident'), 'US') != 0))
							{
								$no = 'checked="checked"';
							}

							$citizenship  = '<br /><label><input type="radio" name="cresident_us" id="cresident_usyes" value="yes" ' . $yes . ' /> ' . Lang::txt('PLG_MEMBERS_PROFILE_YES') . '</label> &nbsp;&nbsp;&nbsp;';
							$citizenship .= '<label><input type="radio" name="cresident_us" id="cresident_usno" value="no" ' . $no . ' /> ' . Lang::txt('PLG_MEMBERS_PROFILE_NO') . '</label> ';

							$this->view('default', 'edit')
							     ->set('registration_field', 'countryresident')
							     ->set('profile_field', 'countryresident')
							     ->set('registration', $this->registration->Residency)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_RESIDENCE'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<div class="side-by-side">' . Lang::txt('PLG_MEMBERS_PROFILE_RESIDENCE_CURRENTLY_IN_USA') . $citizenship . '</div>'
												. '<div class="side-by-side"><label for="cresident">' . Lang::txt('PLG_MEMBERS_PROFILE_RESIDENCE_CURRENTLY_LIVING_IN') . $countries . '</label></div>')
							     ->set('access', '<div class="block"><label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[countryresident]', $this->params->get('access_countryresident'), 'input-select') . '</label></div>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Sex != REG_HIDE) : ?>
			<?php if ($this->params->get('access_gender') == 0
					|| ($this->params->get('access_gender') == 1 && $loggedin)
					|| ($this->params->get('access_gender') == 2 && $isUser)
				) : ?>
					<?php
						$cls = '';
						if ($this->params->get('access_gender') == 2)
						{
							$cls .= 'private';
						}
						if ($this->profile->get("gender") == "" || is_null($this->profile->get("gender")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}
						if (isset($update_missing) && in_array("sex",array_keys($update_missing)))
						{
							$cls = str_replace(' hide', '', $cls);
							$cls .= ' missing';
						}
					?>
				<li class="profile-sex section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_GENDER'); ?></div>
						<div class="value">
							<?php
								$gender = \Components\Members\Helpers\Html::propercase_singleresponse($this->profile->get('gender'));
								echo ($gender != 'n/a') ? $gender : Lang::txt('PLG_MEMBERS_PROFILE_GENDER_ENTER');
							?>
						</div>
						<br class="clear" />
						<?php
							$sexes = array(
								'male'    => Lang::txt('PLG_MEMBERS_PROFILE_GENDER_OPT_MALE'),
								'female'  => Lang::txt('PLG_MEMBERS_PROFILE_GENDER_OPT_FEMALE'),
								'refused' => Lang::txt('PLG_MEMBERS_PROFILE_OPT_REFUSED')
							);

							$sex = '<select name="sex" class="input-select">';
							//$sex .= '<option value="unspecified">Unspecified</option>';
							foreach ($sexes as $k=>$v)
							{
								$sel = ($k == $this->profile->get('gender')) ? 'selected="selected"' : '';
								$sex .= '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
							}
							$sex .= '</select>';

							$this->view('default', 'edit')
							     ->set('registration_field', 'sex')
							     ->set('profile_field', 'gender')
							     ->set('registration', $this->registration->Sex)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_GENDER'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_GENDER') . $sex . '</label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[gender]',$this->params->get('access_gender'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Disability != REG_HIDE) : ?>
			<?php if ($this->params->get('access_disability') == 0
					|| ($this->params->get('access_disability') == 1 && $loggedin)
					|| ($this->params->get('access_disability') == 2 && $isUser)
				) : ?>
				<?php
					$cls = '';
					if ($this->params->get('access_disability') == 2)
					{
						$cls .= 'private';
					}
					if ($this->profile->get("disability") == "" || is_null($this->profile->get("disability")) || count($this->profile->get("disability")) < 1)
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("disability",array_keys($update_missing)))
					{
						$cls = str_replace(' hide', '', $cls);
						$cls .= ' missing';
					}
					//dont show meant for stats only
					$cls .= (!$isUser) ? " hide" : "" ;
				?>
				<li class="profile-disability section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY'); ?></div>
						<div class="value">
							<?php
								$disability = \Components\Members\Helpers\Html::propercase_multiresponse($this->profile->get('disability'));
								echo ($disability != 'n/a') ? $disability : Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY_ENTER');
							?>
						</div>
						<br class="clear" />
						<?php
							$disabilities = $this->profile->get('disability');
							if (!is_array($disabilities)) {
								$disabilities = array();
							}

							$disabilityyes = false;
							$disabilityother = '';
							foreach ($disabilities as $disabilityitem)
							{
								if ($disabilityitem != 'no'
								 && $disabilityitem != 'refused') {
									if (!$disabilityyes) {
										$disabilityyes = true;
									}

									if ($disabilityitem != 'blind'
									 && $disabilityitem != 'deaf'
									 && $disabilityitem != 'physical'
									 && $disabilityitem != 'learning'
									 && $disabilityitem != 'vocal'
									 && $disabilityitem != 'yes') {
										$disabilityother = $disabilityitem;
									}
								}
							}

							$disability_html = "";

							$disability_html .= "\t\t" . '<fieldset class="sub">' . "\n";
							$disability_html .= "\t\t\t\t" . '<label><input type="radio" class="option" name="disability" id="disabilityyes" value="yes"';
							if ($disabilityyes)
							{
								$disability_html .= ' checked="checked"';
							}
							$disability_html .= ' /> ' . Lang::txt('PLG_MEMBERS_PROFILE_YES') . '</label>' . "\n";
							$disability_html .= "\t\t\t" . '<fieldset class="sub-sub">' . "\n";
							$disability_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" ';
							if (in_array('blind', $disabilities))
							{
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY_OPT_VISUAL') . '</label>' . "\n";
							$disability_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" ';
							if (in_array('deaf', $disabilities))
							{
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY_OPT_HEARING') . '</label>' . "\n";
							$disability_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" ';
							if (in_array('physical', $disabilities))
							{
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY_OPT_PHYSICAL') . '</label>' . "\n";
							$disability_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" ';
							if (in_array('learning', $disabilities))
							{
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY_OPT_COGNITIVE') . '</label>' . "\n";
							$disability_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" ';
							if (in_array('vocal', $disabilities))
							{
								$disability_html .= 'checked="checked" ';
							}
							$disability_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY_OPT_VOCAL') . '</label>' . "\n";
							$disability_html .= "\t\t\t\t" . '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_OPT_OTHER').':' . "\n";
							$disability_html .= "\t\t\t\t" . '<input name="disabilityother" class="input-text" id="disabilityother" type="text" value="'. $this->escape($disabilityother) .'" /></label>' . "\n";
							$disability_html .= "\t\t\t" . '</fieldset>' . "\n";
							$disability_html .= "\t\t\t" . '<label><input type="radio" class="option" name="disability" id="disabilityno" value="no"';
							if (in_array('no', $disabilities))
							{
								$disability_html .= ' checked="checked"';
							}
							$disability_html .= '> ' . Lang::txt('PLG_MEMBERS_PROFILE_NO_NONE') . '</label>' . "\n";
							$disability_html .= "\t\t\t" . '<label><input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"';
							if (in_array('refused', $disabilities))
							{
								$disability_html .= ' checked="checked"';
							}
							$disability_html .= '> ' . Lang::txt('PLG_MEMBERS_PROFILE_OPT_REFUSED') . '</label>' . "\n";
							$disability_html .= "\t\t" . '</fieldset>' . "\n";

							$this->view('default', 'edit')
							     ->set('registration_field', 'disability')
							     ->set('profile_field', 'disability')
							     ->set('registration', $this->registration->Disability)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label for="disability-input">' . Lang::txt('PLG_MEMBERS_PROFILE_DISABILITY') . $disability_html . '</label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[disability]',$this->params->get('access_disability'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
								<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Hispanic != REG_HIDE) : ?>
			<?php if ($this->params->get('access_hispanic') == 0
					|| ($this->params->get('access_hispanic') == 1 && $loggedin)
					|| ($this->params->get('access_hispanic') == 2 && $isUser)
				) : ?>
				<?php
					$cls = '';
					if ($this->params->get('access_hispanic') == 2)
					{
						$cls .= 'private';
					}
					if ($this->profile->get("hispanic") == "" || is_null($this->profile->get("hispanic")) || count($this->profile->get("hispanic")) < 1)
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("hispanic",array_keys($update_missing)))
					{
						$cls = str_replace(' hide', '', $cls);
						$cls .= ' missing';
					}
					//dont show meant for stats only
					$cls .= (!$isUser) ? " hide" : "" ;
				?>
				<li class="profile-hispanic section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC'); ?></div>
						<div class="value">
							<?php
								$hispanic = \Components\Members\Helpers\Html::propercase_multiresponse($this->profile->get('hispanic'));
								echo ($hispanic != 'n/a') ? $hispanic : Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC_ENTER');
							?>
						</div>
						<br class="clear" />
						<?php
							$hispanic = $this->profile->get('hispanic');
							if (!is_array($hispanic))
							{
								$hispanic = array();
							}

							$hispanicyes = false;
							$hispanicother = '';
							foreach ($hispanic as $hispanicitem)
							{
								if ($hispanicitem != 'no'
								 && $hispanicitem != 'refused')
								{
									if (!$hispanicyes)
									{
										$hispanicyes = true;
									}

									if ($hispanicitem != 'cuban'
									 && $hispanicitem != 'mexican'
									 && $hispanicitem != 'puertorican')
									{
										$hispanicother = $hispanicitem;
									}
								}
							}

							$hispanic_html  = '';
							$hispanic_html .= "\t\t" . '<fieldset class="sub">' . "\n";
							$hispanic_html .= "\t\t\t\t" . '<label><input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" ';
							if ($hispanicyes)
							{
								$hispanic_html .= 'checked="checked"';
							}
							$hispanic_html .= ' /> ' . Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC_OPT_YES') . '</label>' . "\n";
							$hispanic_html .= "\t\t\t" . '<fieldset class="sub-sub">' . "\n";
							$hispanic_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" ';
							if (in_array('cuban', $hispanic))
							{
								$hispanic_html .= 'checked="checked" ';
							}
							$hispanic_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC_OPT_CUBAN') . '</label>' . "\n";
							$hispanic_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" ';
							if (in_array('mexican', $hispanic))
							{
								$hispanic_html .= 'checked="checked" ';
							}
							$hispanic_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC_OPT_MEXICAN') . '</label>' . "\n";
							$hispanic_html .= "\t\t\t\t" . '<label><input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" ';
							if (in_array('puertorican', $hispanic))
							{
								$hispanic_html .= 'checked="checked" ';
							}
							$hispanic_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC_OPT_PEURTORICAN') . '</label>' . "\n";
							$hispanic_html .= "\t\t\t\t" . '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC_OPT_OTHER')."\n";
							$hispanic_html .= "\t\t\t\t" . '<input name="hispanicother" class="input-text" id="hispanicother" type="text" value="'. $this->escape($hispanicother) .'" /></label>' . "\n";
							$hispanic_html .= "\t\t\t" . '</fieldset>' . "\n";
							$hispanic_html .= "\t\t\t" . '<label><input type="radio" class="option" name="hispanic" id="hispanicno" value="no"';
							if (in_array('no', $hispanic))
							{
								$hispanic_html .= ' checked="checked"';
							}
							$hispanic_html .= '> ' . Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC_OPT_NO') . '</label>' . "\n";
							$hispanic_html .= "\t\t\t" . '<label><input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"';
							if (in_array('refused', $hispanic))
							{
								$hispanic_html .= ' checked="checked"';
							}
							$hispanic_html .= '> ' . Lang::txt('PLG_MEMBERS_PROFILE_OPT_REFUSED') . '</label>' . "\n";
							$hispanic_html .= "\t\t" . '</fieldset>' . "\n";

							$this->view('default', 'edit')
							     ->set('registration_field', 'hispanic')
							     ->set('profile_field', 'hispanic')
							     ->set('registration', $this->registration->Hispanic)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<label for="hispanic-input">' . Lang::txt('PLG_MEMBERS_PROFILE_HISPANIC') . $hispanic_html . '</label>')
							     ->set('access', '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[hispanic]',$this->params->get('access_hispanic'),'input-select') . '</label>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
							<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->Race != REG_HIDE) : ?>
			<?php if ($this->params->get('access_race') == 0
					|| ($this->params->get('access_race') == 1 && $loggedin)
					|| ($this->params->get('access_race') == 2 && $isUser)
				) : ?>
				<?php
					$cls = '';
					if ($this->params->get('access_race') == 2)
					{
						$cls .= 'private';
					}
					if ($this->profile->get("race") == "" || is_null($this->profile->get("race")) || count($this->profile->get("race")) < 1)
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("race",array_keys($update_missing)))
					{
						$cls = str_replace(" hide", '', $cls);
						$cls .= ' missing';
					}
					//dont show meant for stats only
					$cls .= (!$isUser) ? " hide" : '' ;
				?>
				<li class="profile-race section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_RACE'); ?></div>
						<div class="value">
							<?php
								$race = \Components\Members\Helpers\Html::propercase_multiresponse($this->profile->get('race'));
								echo ($race != 'n/a') ? $race : Lang::txt('PLG_MEMBERS_PROFILE_RACE_ENTER');
							?>
						</div>
						<br class="clear" />
						<?php
							$race = $this->profile->get('race');
							if (!is_array($race))
							{
								$race = array();
							}

							$race_html = "";
							$race_html .= "\t\t" . '<fieldset class="sub">' . "\n";
							$race_html .= "\t\t\t" . '<p class="hint">' . Lang::txt('PLG_MEMBERS_PROFILE_SELECT_MULTIPLE') . '</p>' . "\n";

							$race_html .= "\t\t\t" . '<label><input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="1" ';
							if (in_array('nativeamerican', $race))
							{
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_RACE_OPT_NATIVEAMERICAN') . '</label>' . "\n";
							$race_html .= "\t\t\t" . '<label class="indent">' . Lang::txt('PLG_MEMBERS_PROFILE_RACE_OPT_TRIBE').':' . "\n";
							$race_html .= "\t\t\t" . '<input name="racenativetribe" class="input-text" id="racenativetribe" type="text" value="' . $this->escape($this->profile->get('nativeTribe')) . '" /></label>' . "\n";
							$race_html .= "\t\t\t" . '<label><input type="checkbox" class="option" name="raceasian" id="raceasian" value="1" ';
							if (in_array('asian', $race))
							{
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_RACE_OPT_ASIAN') . '</label>' . "\n";
							$race_html .= "\t\t\t" . '<label><input type="checkbox" class="option" name="raceblack" id="raceblack" value="1" ';
							if (in_array('black', $race))
							{
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_RACE_OPT_BLACK') . '</label>' . "\n";
							$race_html .= "\t\t\t" . '<label><input type="checkbox" class="option" name="racehawaiian" id="racehawaiian" value="1" ';
							if (in_array('hawaiian', $race))
							{
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_RACE_OPT_HAWAIIAN') . '</label>' . "\n";
							$race_html .= "\t\t\t" . '<label><input type="checkbox" class="option" name="racewhite" id="racewhite" value="1" ';
							if (in_array('white', $race))
							{
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_RACE_OPT_WHITE') . '</label>' . "\n";
							$race_html .= "\t\t\t" . '<label><input type="checkbox" class="option" name="racerefused" id="racerefused" value="1" ';
							if (in_array('refused', $race))
							{
								$race_html .= 'checked="checked" ';
							}
							$race_html .= '/> ' . Lang::txt('PLG_MEMBERS_PROFILE_OPT_REFUSED') . '</label>' . "\n";
							$race_html .= "\t\t" . '</fieldset>' . "\n";

							$this->view('default', 'edit')
							     ->set('registration_field', 'race')
							     ->set('profile_field', 'race')
							     ->set('registration', $this->registration->Race)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_RACE'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', '<div>' . Lang::txt('PLG_MEMBERS_PROFILE_RACE') . $race_html.'</div>')
							     ->set('access', '<div class="block"><label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[race]', $this->params->get('access_race'),'input-select') . '</label></div>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
							<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->registration->OptIn != REG_HIDE) : ?>
			<?php if ($this->params->get('access_optin') == 0
					|| ($this->params->get('access_optin') == 1 && $loggedin)
					|| ($this->params->get('access_optin') == 2 && $isUser)
				) : ?>
				<?php
					$cls = '';
					if ($this->params->get('access_optin') == 2)
					{
						$cls .= 'private';
					}
					if ($this->profile->get("mailPreferenceOption") == "" || is_null($this->profile->get("mailPreferenceOption")))
					{
						$cls .= ($isUser) ? " hidden" : " hide";
					}
					if (isset($update_missing) && in_array("optin",array_keys($update_missing)))
					{
						$cls = str_replace(' hide', '', $cls);
						$cls .= ' missing';
					}
					//dont show meant for stats only
					$cls .= (!$isUser) ? ' hide' : '' ;

					//get value of mail preference option
					switch ($this->profile->get('mailPreferenceOption'))
					{
						case '1':  $mailPreferenceValue = 'Yes, send me emails';       break;
						case '0':  $mailPreferenceValue = 'No, don\'t send me emails'; break;
						case '-1':
						default:   $mailPreferenceValue = 'Unanswered';                break;
					}
				?>
				<li class="profile-optin section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES'); ?></div>
						<div class="value"><?php echo $mailPreferenceValue; ?></div>
						<br class="clear" />
						<?php
							//define mail preference options
							$options = array(
								'-1' => Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_SELECT'),
								'1'  => Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_YES'),
								'0'  => Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_NO')
							);

							//build option list
							$optin_html  = '<strong>' . Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_EXPLANATION') . '</strong>';
							$optin_html .= '<label for="mailPreferenceOption">';
							$optin_html .= '<select name="mailPreferenceOption" id="mailPreferenceOption" class="input-select">';
							foreach ($options as $key => $value)
							{
								$sel = ($key == $this->profile->get('mailPreferenceOption')) ? 'selected="selected"' : '';
								$optin_html .= '<option ' . $sel . ' value="' . $key . '">' . $value . '</option>';
							}
							$optin_html .= '</select>';
							$optin_html .= '</label>';

							$this->view('default', 'edit')
							     ->set('registration_field', 'mailPreferenceOption')
							     ->set('profile_field', 'mailPreferenceOption')
							     ->set('registration', $this->registration->OptIn)
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', $optin_html)
							     ->set('access', '<div class="block"><label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . \Components\Members\Helpers\Html::selectAccess('access[optin]',$this->params->get('access_optin'),'input-select') . '</label></div>')
							     ->display();
						?>
					</div>
					<?php if ($isUser) : ?>
						<div class="section-edit">
							<a class="edit-profile-section" href="#">
							<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
</div><!-- /#profile-page-content -->
