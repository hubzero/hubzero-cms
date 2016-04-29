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
defined('_HZEXEC_') or die();

$this->css('profile.css');

include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'html.php');

$database = App::get('db');
$loggedin = (! User::isGuest());
$isUser   = false;
?>
<?php if ($this->membership_control == 1) { ?>
	<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
		<ul id="page_options">
			<li>
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&task=invite'); ?>">
					<?php echo Lang::txt('PLG_GROUPS_MEMBERS_INVITE_MEMBERS'); ?>
				</a>
				<?php if ($this->membership_control == 1 && $this->authorized == 'manager') : ?>
					<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=members&action=addrole'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ADD_ROLE'); ?>
					</a>
				<?php endif; ?>
			</li>
		</ul>
	<?php } ?>
<?php } ?>

<div class="section">
	<div class="section-inner">
		<h4>
			<?php echo $this->escape($this->profile->get('name')); ?>
		</h4>

		<?php
		$output = Event::trigger('groups.onGroupMemberBefore', array($this->group, $this->profile));
		echo implode("\n", $output);
		?>

		<ul class="member-profile">
			<li class="profile-full field">
				<div class="field-content">
					<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_FULL'); ?></div>
					<div class="value">
						<a href="<?php echo $this->profile->link(); ?>"><?php echo Lang::txt('PLG_GROUPS_PROFILE_FULL_GO'); ?></a>
					</div>
				</div>
			</li>

			<?php if ($this->registration->Organization != REG_HIDE && $this->profile->get('organization')) : ?>
				<?php if ($this->params->get('access_org') == 0
						|| ($this->params->get('access_org') == 1 && $loggedin)
						|| ($this->params->get('access_org') == 2 && $isUser)
						) : ?>
					<li class="profile-org field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_ORGANIZATION'); ?></div>
							<div class="value">
								<?php echo $this->escape(stripslashes($this->profile->get('organization'))); ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Employment != REG_HIDE && $this->profile->get('orgtype')) : ?>
				<?php if ($this->params->get('access_orgtype') == 0
						|| ($this->params->get('access_orgtype') == 1 && $loggedin)
						|| ($this->params->get('access_orgtype') == 2 && $isUser)
						) : ?>
					<li class="profile-orgtype field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_EMPLOYMENT_TYPE'); ?></div>
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
								<?php echo $orgtype; ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->profile->get('email')) : ?>
				<?php if ($this->params->get('access_email', 2) == 0
						|| ($this->params->get('access_email', 2) == 1 && $loggedin)
						|| ($this->params->get('access_email', 2) == 2 && $isUser)
						) : ?>
					<li class="profile-email field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_EMAIL'); ?></div>
							<div class="value">
								<a class="email" href="mailto:<?php echo \Hubzero\Utility\String::obfuscate($this->profile->get('email')); ?>" rel="nofollow">
									<?php echo \Hubzero\Utility\String::obfuscate($this->profile->get('email')); ?>
								</a>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->ORCID != REG_HIDE && $this->profile->get('orcid')) : ?>
				<?php if ($this->params->get('access_orcid') == 0
						|| ($this->params->get('access_orcid') == 1 && $loggedin)
						|| ($this->params->get('access_orcid') == 2 && $isUser)
					) : ?>
					<li class="profile-web field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_ORCID'); ?></div>
							<div class="value">
								<?php echo '<a class="orcid" rel="external" href="http://orcid.org/' . $this->profile->get('orcid') . '">' . $this->profile->get('orcid') . '</a>'; ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->URL != REG_HIDE && $this->profile->get('url')) : ?>
				<?php if ($this->params->get('access_url') == 0
						|| ($this->params->get('access_url') == 1 && $loggedin)
						|| ($this->params->get('access_url') == 2 && $isUser)
					) : ?>
					<li class="profile-web field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_WEBSITE'); ?></div>
							<div class="value">
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
								$title = Lang::txt('PLG_GROUPS_PROFILE_WEBSITE_MEMBERS', $this->profile->get('name'));

								echo '<a class="url" rel="external" title="' . $title . '" href="' . $url . '">' . $url . '</a>';
								?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Phone != REG_HIDE && $this->profile->get('phone')) : ?>
				<?php if ($this->params->get('access_phone') == 0
						|| ($this->params->get('access_phone') == 1 && $loggedin)
						|| ($this->params->get('access_phone') == 2 && $isUser)
					) : ?>
					<li class="profile-phone field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_TELEPHONE'); ?></div>
							<div class="value">
								<?php echo $this->escape(str_replace(' ', '-', $this->profile->get('phone'))); ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->address != REG_HIDE) : ?>
				<?php if ($this->params->get('access_address') == 0
						|| ($this->params->get('access_address') == 1 && $loggedin)
						|| ($this->params->get('access_address') == 2 && $isUser)
					) : ?>
					<?php
						include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'address.php');
						// Get member addresses
						$db = App::get('db');
						$membersAddress = new \Components\Members\Tables\Address($db);
						$addresses = $membersAddress->getAddressesForMember($this->profile->get('id'));

						if (count($addresses) > 0) :
					?>
					<li class="profile-address field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_ADDRESS'); ?></div>
							<div class="value">
								<?php
								$this->view('address')
								     ->set('addresses', $addresses)
								     ->set('displayEditLinks', $isUser)
								     ->set('profile', $this->profile)
								     ->display();
								?>
							</div>
						</div>
					</li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->profile->get('bio')) : ?>
				<?php if ($this->params->get('access_bio') == 0
						|| ($this->params->get('access_bio') == 1 && $loggedin)
						|| ($this->params->get('access_bio') == 2 && $isUser)
					) : ?>
					<li class="profile-bio field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_BIOGRAPHY'); ?></div>
							<div class="value">
								<?php echo $this->profile->get('bio'); ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Interests != REG_HIDE) : ?>
				<?php if ($this->params->get('access_tags') == 0
						|| ($this->params->get('access_tags') == 1 && $loggedin)
						|| ($this->params->get('access_tags') == 2 && $isUser)
					) : ?>
					<?php
						include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'tags.php');

						$mt = new \Components\Members\Models\Tags($this->profile->get('id'));
						$tags = $mt->render();
						if ($tags) :
					?>
						<li class="profile-interests field">
							<div class="field-content">
								<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_INTERESTS'); ?></div>
								<div class="value">
									<?php echo $tags; ?>
								</div>
							</div>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php
			// get countries list
			$co = \Hubzero\Geocode\Geocode::countries();
			?>
			<?php if ($this->registration->Citizenship != REG_HIDE && $this->profile->get('countryorigin')) : ?>
				<?php if ($this->params->get('access_countryorigin') == 0
						|| ($this->params->get('access_countryorigin') == 1 && $loggedin)
						|| ($this->params->get('access_countryorigin') == 2 && $isUser)
					) : ?>
					<li class="profile-countryorigin field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_CITIZENSHIP'); ?></div>
							<?php
								$img = '';
								$citizenship = '';
								if (is_file(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'site' . DS . 'assets' . DS . 'img' . DS . 'flags' . DS . strtolower($this->profile->get('countryorigin')) . '.gif'))
								{
									$img = '<img src="' . rtrim(Request::base(true), '/') . '/core/components/com_members/site/assets/img/flags/' . strtolower($this->profile->get('countryorigin')) . '.gif" alt="' . $this->escape($this->profile->get('countryorigin')) . ' ' . Lang::txt('PLG_GROUPS_PROFILE_FLAG') . '" /> ';
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
								<?php echo $citizenship; ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Residency != REG_HIDE && $this->profile->get('countryresident')) : ?>
				<?php if ($this->params->get('access_countryresident') == 0
						|| ($this->params->get('access_countryresident') == 1 && $loggedin)
						|| ($this->params->get('access_countryresident') == 2 && $isUser)
					) : ?>
					<li class="profile-countryresident field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_RESIDENCE'); ?></div>
							<?php
								$img = '';
								$residence = '';
								if (is_file(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'site' . DS . 'assets' . DS . 'img' . DS . 'flags' . DS . strtolower($this->profile->get('countryresident')) . '.gif'))
								{
									$img = '<img src="' . rtrim(Request::base(true), '/') . '/core/components/com_members/site/assets/img/flags/' . strtolower($this->profile->get('countryresident')) . '.gif" alt="' . $this->escape($this->profile->get('countryresident')) . ' ' . Lang::txt('PLG_GROUPS_PROFILE_FLAG') . '" /> ';
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
								<?php echo $residence; ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Sex != REG_HIDE && $this->profile->get('gender')) : ?>
				<?php if ($this->params->get('access_gender') == 0
						|| ($this->params->get('access_gender') == 1 && $loggedin)
						|| ($this->params->get('access_gender') == 2 && $isUser)
					) : ?>
					<li class="profile-sex field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_GENDER'); ?></div>
							<div class="value">
								<?php echo \Components\Members\Helpers\Html::propercase_singleresponse($this->profile->get('gender')); ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Disability != REG_HIDE && $this->profile->get('disability')) : ?>
				<?php if ($this->params->get('access_disability') == 0
						|| ($this->params->get('access_disability') == 1 && $loggedin)
						|| ($this->params->get('access_disability') == 2 && $isUser)
					) : ?>
					<li class="profile-disability field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_DISABILITY'); ?></div>
							<div class="value">
								<?php echo \Components\Members\Helpers\Html::propercase_multiresponse($this->profile->get('disability')); ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Hispanic != REG_HIDE && $this->profile->get('hispanic')) : ?>
				<?php if ($this->params->get('access_hispanic') == 0
						|| ($this->params->get('access_hispanic') == 1 && $loggedin)
						|| ($this->params->get('access_hispanic') == 2 && $isUser)
					) : ?>
					<li class="profile-hispanic field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_HISPANIC'); ?></div>
							<div class="value">
								<?php echo \Components\Members\Helpers\Html::propercase_multiresponse($this->profile->get('hispanic')); ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($this->registration->Race != REG_HIDE && $this->profile->get('race')) : ?>
				<?php if ($this->params->get('access_race') == 0
						|| ($this->params->get('access_race') == 1 && $loggedin)
						|| ($this->params->get('access_race') == 2 && $isUser)
					) : ?>
					<li class="profile-race field">
						<div class="field-content">
							<div class="key"><?php echo Lang::txt('PLG_GROUPS_PROFILE_RACE'); ?></div>
							<div class="value">
								<?php echo \Components\Members\Helpers\Html::propercase_multiresponse($this->profile->get('race')); ?>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endif; ?>
		</ul>

		<?php
		$output = Event::trigger('groups.onGroupMemberAfter', array($this->group, $this->profile));
		echo implode("\n", $output);
		?>
	</div>
</div>
