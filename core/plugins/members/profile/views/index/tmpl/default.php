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
defined('_HZEXEC_') or die('Restricted access');

$this->css()
     ->js()
     ->js('jquery.fileuploader.js', 'system');

// flags for not logged in and not user
$loggedin = false;
$isUser   = false;

// if we are logged in set logged in flag
if (!User::isGuest())
{
	$loggedin = true;
}

// if we are this user set user flag
if (User::get('id') == $this->profile->get('id'))
{
	$isUser = true;
}

$isIncrementalEnabled = false;
$update_missing = array();
$invalid = array();


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


// incremental registration
require_once Component::path('com_members') . '/models/incremental/awards.php';
require_once Component::path('com_members') . '/models/incremental/groups.php';
require_once Component::path('com_members') . '/models/incremental/options.php';

$uid = (int)$this->profile->get('id');
$incrOpts = new Components\Members\Models\Incremental\Options;
$isIncrementalEnabled = $incrOpts->isEnabled($uid);

// Profile info
$entries = $this->profile->profiles();

$p = $entries->getTableName();
$f = Components\Members\Models\Profile\Field::blank()->getTableName();
$o = Components\Members\Models\Profile\Option::blank()->getTableName();

$profiles = $entries
	->select($p . '.*,' . $o . '.label')
	->join($f, $f . '.name', $p . '.profile_key', 'inner')
	->joinRaw($o, $o . '.field_id=' . $f . '.id AND ' . $o . '.value=' . $p . '.profile_value', 'left')
	->ordered()
	->rows();

// Convert to XML so we can use the Form processor
$xml = Components\Members\Models\Profile\Field::toXml($this->fields, 'edit');

// Gather data to pass to the form processor
$data = new Hubzero\Config\Registry(
	Components\Members\Models\Profile::collect($profiles)
);

// Create a new form
Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');

$form = new Hubzero\Form\Form('profile', array('control' => 'profile'));
$form->load($xml);
$form->bind($data);

$fields = array();
foreach ($profiles as $profile)
{
	if (isset($fields[$profile->get('profile_key')]))
	{
		$values = $fields[$profile->get('profile_key')]->get('profile_value');
		if (!is_array($values))
		{
			$values = array($values => $fields[$profile->get('profile_key')]->get('label', $values));
		}
		$values[$profile->get('profile_value')] = $profile->get('label', $profile->get('profile_value'));

		$fields[$profile->get('profile_key')]->set('profile_value', $values);
	}
	else
	{
		$fields[$profile->get('profile_key')] = $profile;
	}
}

/**
 * Renders if argument is json?
 *
 * @param  string  $v
 * @return string
 */
function renderIfJson($v)
{
	if (strstr($v, '{'))
	{
		$v = json_decode((string)$v, true);

		if (!$v || json_last_error() !== JSON_ERROR_NONE)
		{
			return $v;
		}

		$o = array();
		$o[] = '<table>';
		$o[] = '<tbody>';
		foreach ($v as $nm => $vl)
		{
			if (!trim($vl))
			{
				continue;
			}
			$o[] = '<tr><th>' . $nm . ':</th><td>' . $vl . '</td></tr>';
		}
		$o[] = '</tbody>';
		$o[] = '</table>';

		$v = implode("\n", $o);
	}
	return $v;
}

// Legacy access values for profile fields
$legacy = array(
	0 => Lang::txt('COM_MEMBERS_FIELD_ACCESS_PUBLIC'),
	1 => Lang::txt('COM_MEMBERS_FIELD_ACCESS_REGISTERED'),
	2 => Lang::txt('COM_MEMBERS_FIELD_ACCESS_PRIVATE')
);
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
		<?php if (count($update_missing) == 1 && in_array("usageAgreement", array_keys($update_missing))) : ?>
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
					<span id="meter-percent" data-percent="<?php echo $this->completeness; ?>" data-percent-level="<?php echo @$this->completeness_level; ?>"></span>
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
		$awards = new Components\Members\Models\Incremental\Awards($this->profile);
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

		$increm .= '<p>' . Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_EARN_MORE_POINTS', $incrOpts->getAwardPerField(), Route::url('index.php?option=com_store'), Route::url('index.php?option=com_answers'), Route::url('index.php?option=com_wishlist')) . '</p>';

		$increm .= '</div>';
		$increm .= '<div id="wallet"><span>' . ($awards['prior'] + $awards['new']) . '</span></div>';
		$increm .= '<script type="text/javascript">
						window.bonus_eligible_fields = ' . json_encode($awards['eligible']) . ';
						window.bonus_amount = ' . $incrOpts->getAwardPerField() . ';
					</script>';
		echo $increm;

		$this->js('incremental', 'com_members');
	}
	?>

	<?php if (isset($update_missing) && in_array("usageAgreement", array_keys($update_missing))) : ?>
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
							echo $page->text;
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
				<input type="hidden" name="id" value="<?php echo User::get('id'); ?>" />
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
						$name  = '<label class="side-by-side three">' . Lang::txt('PLG_MEMBERS_PROFILE_FIRST_NAME') . ' <input type="text" name="name[first]" id="first-name" class="input-text" value="'.$this->escape($this->profile->get('givenName')).'" /></label>';
						$name .= '<label class="side-by-side three">' . Lang::txt('PLG_MEMBERS_PROFILE_MIDDLE_NAME') . ' <input type="text" name="name[middle]" id="middle-name" class="input-text" value="'.$this->escape($this->profile->get('middleName')).'" /></label>';
						$name .= '<label class="side-by-side three no-padding-right">' . Lang::txt('PLG_MEMBERS_PROFILE_LAST_NAME') . ' <input type="text" name="name[last]" id="last-name" class="input-text" value="'.$this->escape($this->profile->get('surname')).'" /></label>';

						$this->view('default', 'edit')
						     ->set('registration_field', 'name')
						     ->set('profile_field', 'name')
						     ->set('registration', $this->profile->get('name'))
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

		<?php if ($isUser) : ?>
			<li class="profile-name section hidden">
				<div class="section-content">
					<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_USERNAME'); ?></div>
					<div class="value"><?php echo $this->escape($this->profile->get('username')); ?></div>
					<br class="clear" />
				</div>
			</li>
		<?php endif; ?>

		<?php if ($isUser) : ?>
			<?php
			// Determine what type of password change the user needs
			$hzup = \Hubzero\User\Password::getInstance($this->profile->get('id'));
			if ($hzup && !empty($hzup->passhash))
			{
				// A password has already been set, now check if they're logged in with a linked account
				if (array_key_exists('auth_link_id', User::toArray()))
				{
					// Logged in with linked account
					$passtype = 'changelocal';
				}
				else
				{
					// Logged in with hub
					$passtype = 'changehub';
				}
			}
			else
			{
				// No password has been set...
				$passtype = 'set';
			}
			?>
			<?php if (!Plugin::isEnabled('members', 'account')) : ?>
				<li class="profile-password section hidden">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD'); ?></div>
						<div class="value">
							<?php
							// Determine what type of password change the user needs
							if ($passtype == 'changelocal' || $passtype == 'changehub')
							{
								echo '***************';
							}
							else
							{
								// No password has been set...
								echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_NOT_SET');
							}
							?>
						</div>
						<br class="clear" />
						<div class="section-edit-container">
							<div class="section-edit-content">
								<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" data-section-registation="password" data-section-profile="password">
									<span class="section-edit-errors"></span>
									<?php if ($passtype == 'changelocal' || $passtype == 'changehub'): ?>
										<div class="input-wrap">
											<label for="password">
												<?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_CURRENT'); ?>
												<input type="password" name="oldpass" id="password" class="input-text" />
											</label>
										</div>
									<?php endif; ?>
									<div class="input-wrap">
										<label for="newpass" class="side-by-side">
											<?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_NEW'); ?>
											<input type="password" name="newpass" id="newpass" class="input-text" />
										</label>
										<label for="newpass2" class="side-by-side no-padding-right">
											<?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_CONFIRM'); ?>
											<input type="password" name="newpass2" id="newpass2" class="input-text" />
										</label>
									</div>
									<input type="hidden" name="change" value="1" />
									<input type="submit" class="section-edit-submit btn" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_SAVE'); ?>" />
									<input type="reset" class="section-edit-cancel btn" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_CANCEL'); ?>" />
									<input type="hidden" name="option" value="com_members" />
									<input type="hidden" name="controller" value="profiles" />
									<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
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
			<?php else: ?>
				<li class="profile-password section hidden">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD'); ?></div>
						<div class="value">
							<?php
							// Determine what type of password change the user needs
							if ($passtype == 'changelocal' || $passtype == 'changehub')
							{
								echo '***************';
							}
							else
							{
								// No password has been set...
								echo Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_NOT_SET');
							}
							?>
						</div>
					</div>
					<div class="section-edit">
						<a href="<?php echo Route::url($this->profile->link() . '&active=account'); ?>">
							<?php echo Lang::txt('PLG_MEMBERS_PROFILE_EDIT'); ?>
						</a>
					</div>
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
						if ($this->profile->get("email") == '' || is_null($this->profile->get("email")))
						{
							$cls .= ($isUser) ? " hidden" : " hide";
						}

						$select  = '<select name="access[email]" class="input-select">' . "\n";
						foreach ($legacy as $k => $v)
						{
							$selected = ($k == $this->params->get('access_email', 2))
									  ? ' selected="selected"'
									  : '';
							$select .= ' <option value="' . $k . '"' . $selected . '>' . $v . '</option>' . "\n";
						}
						$select .= '</select>' . "\n";
					?>
				<li class="profile-email section <?php echo $cls; ?>">
					<div class="section-content">
						<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_EMAIL'); ?></div>
						<div class="value">
							<a class="email" href="mailto:<?php echo \Components\Members\Helpers\Html::obfuscate($this->profile->get('email')); ?>" rel="nofollow">
								<?php echo \Components\Members\Helpers\Html::obfuscate($this->profile->get('email')); ?>
							</a>
						</div>
						<?php if ($isUser) : ?>
							<br class="clear" />
							<input type="hidden" class="input-text" name="email" id="email" value="<?php echo $this->escape($this->profile->get('email')); ?>" />
							<?php
								if ($this->profile->get('access') > 2)
								{
									$access  = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY')  . '</label>';
									$access .= Lang::txt('PLG_MEMBERS_PROFILE_ACCESS_MUST_BE_PUBLIC');
									$access .= '<input type="hidden" name="access[email]" value="' . $this->params->get('access_email') . '" />';
								}
								else
								{
									$access = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . $select . '</label>';
								}

								$this->view('default', 'edit')
								     ->set('registration_field', 'email')
								     ->set('profile_field', 'email')
								     ->set('registration', 1)
								     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_EMAIL'))
								     ->set('profile', $this->profile)
								     ->set('isUser', $isUser)
								     ->set('inputs', '<label class="side-by-side">' . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_VALID') . ' <input type="text" class="input-text" name="email" id="profile-email" value="' . $this->escape($this->profile->get('email')) . '" /></label>'
													. '<label class="side-by-side no-padding-right">' . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_CONFIRM') . ' <input type="text" class="input-text" name="email2" id="profile-email2" value="' . $this->escape($this->profile->get('email')) . '" /></label>'
													. '<br class="clear" /><p class="warning no-margin-top">' . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_WARNING') . '</p>')
								     ->set('access', $access)
								     ->display();
							?>
						<?php endif; ?>
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

		<?php
		$scripts = array();
		$toggle = array();

		// We need to collect a list of all field options
		// that have dependent fields. We'll use this later
		// on to determine what we should or shouldn't
		// display, regardless if the field has a value
		// submitted by the user.
		$f = array();
		foreach ($this->fields as $field)
		{
			if ($field->options->count())
			{
				foreach ($field->options as $option)
				{
					if (!$option->get('dependents'))
					{
						continue;
					}

					$events = json_decode($option->get('dependents'));
					$option->set('dependents', $events);

					if (empty($events))
					{
						continue;
					}

					if (!isset($f[$field->get('name')]))
					{
						$f[$field->get('name')] = array();
					}
					$f[$field->get('name')][$option->get('value')] = $events;
				}
			}
		}

		$sh = array();
		$hd = array();

		foreach ($this->fields as $field):
			// Build scripts for toggling dependent fields
			if ($isUser && $field->options->count())
			{
				$i = 0;
				$hasEvents = false;
				$opts = array();
				$hide = array();

				foreach ($field->options as $option)
				{
					$opts[] = '#profile_' . $field->get('name') . $i;

					$i++;

					$events = $option->get('dependents');

					if (empty($events))
					{
						continue;
					}

					$hasEvents = true;
				}

				if ($hasEvents)
				{
					if ($field->get('type') == 'dropdown')
					{
						$scripts[] = '	$("#profile_' . $field->get('name') . '").on("change", function(e){';
					}
					else
					{
						$scripts[] = '	$("'. implode(',', $opts) . '").on("change", function(e){';
					}
				}

				$i = 0;
				foreach ($field->options as $option)
				{
					if (!$option->get('dependents'))
					{
						continue;
					}

					$events = $option->get('dependents');

					if ($field->get('type') == 'dropdown')
					{
						$scripts[] = '		if ($(this).val() == "' . ($option->value ? $option->value : $option->label) . '") {';
						$show = array();
						foreach ($events as $s)
						{
							$show[] = '#profile_' . $s;
						}
						$hide = array_merge($hide, $show);
						$scripts[] = '			$("' . implode(', ', $show) . '").closest("li.section").show();';
						$scripts[] = '		} else {';
						$scripts[] = '			$("' . implode(', ', $show) . '").closest("li.section").hide();';
						$scripts[] = '		}';

						$toggle[] = '	if ($("#profile_' . $field->get('name') . '").val() == "' . ($option->value ? $option->value : $option->label) . '") {';
						$toggle[] = '		$("' . implode(', ', $show) . '").closest("li.section").show();';
						$toggle[] = '	} else {';
						$toggle[] = '		$("' . implode(', ', $show) . '").closest("li.section").hide();';
						$toggle[] = '	}';
					}
					else
					{
						$scripts[] = '		if ($(this).is(":checked") && $(this).val() == "' . ($option->value ? $option->value : $option->label) . '") {';
						$show = array();
						foreach ($events as $s)
						{
							$show[] = '#profile_' . $s;
						}
						$hide = array_merge($hide, $show);
						$scripts[] = '			$("' . implode(', ', $show) . '").closest("li.section").show();';
						$scripts[] = '		} else {';
						$scripts[] = '			$("' . implode(', ', $show) . '").closest("li.section").hide();';
						$scripts[] = '		}';

						$toggle[] = '	if ($("#profile_' . $field->get('name') . $i . '").is(":checked") && $("#profile_' . $field->get('name') . $i . '").val() == "' . ($option->value ? $option->value : $option->label) . '") {';
						$toggle[] = '		$("' . implode(', ', $show) . '").closest("li.section").show();';
						$toggle[] = '	} else {';
						$toggle[] = '		$("' . implode(', ', $show) . '").closest("li.section").hide();';
						$toggle[] = '	}';
					}

					$i++;
				}

				if ($hasEvents)
				{
					$scripts[] = '	});';
					$scripts[] = implode("\n", $toggle);
				}
			}

			//---

			if (!isset($fields[$field->get('name')]))
			{
				$fields[$field->get('name')] = Components\Members\Models\Profile::blank();
				$fields[$field->get('name')]->set('access', 1);
			}

			$profile = $fields[$field->get('name')];
			if (!$profile->get('access'))
			{
				$profile->set('access', 5);
			}

			if (in_array($profile->get('access', $field->get('access', 5)), User::getAuthorisedViewLevels()) || $isUser)
			{
				$cls = array('profile-' . $field->get('name'));

				if ($profile->get('access', $field->get('access')) == 2)
				{
					$cls[] = 'registered';
				}

				if ($profile->get('access', $field->get('access')) == 5)
				{
					$cls[] = 'private';
				}

				// Tags need to be rendered a little differently
				if ($field->get('type') == 'tags')
				{
					$value = $this->profile->tags();
				}
				else
				{
					$value = $profile->get('profile_value');
					if (!is_array($value))
					{
						$value = $profile->get('label', $value);
					}
					$value = $value ?: $this->profile->get($field->get('name'), $field->get('default_value'));

					if ($value)
					{
						// If the type is a block of text, parse for macros
						if ($field->get('type') == 'textarea')
						{
							$value = Html::content('prepare', $value);
						}
						// IF the type is a URL, link it
						if ($field->get('type') == 'url')
						{
							$parsed = parse_url($value);
							if (empty($parsed['scheme']))
							{
								$value = 'http://' . ltrim($value, '/');
							}
							$value = '<a href="' . $value . '" rel="external">' . $value . '</a>';
						}
					}
				}

				$val = $value;

				// If we have a field that has options with dependent values
				// We push all the dependent fields into a "hide" list
				if (isset($f[$field->get('name')]))
				{
					foreach ($f[$field->get('name')] as $opt => $deps)
					{
						$hd = array_merge($deps, $hd);
					}
				}

				if (is_array($value))
				{
					// If the value is from an option that has dependent fields
					// We need to caputre its list of dependent fields so we
					// can tell the code to display those fields
					foreach (array_keys($value) as $k => $v)
					{
						if (isset($f[$field->get('name')]) && isset($f[$field->get('name')][$v]))
						{
							$sh += $f[$field->get('name')][$v];
						}
					}

					$val = array();
					foreach ($value as $k => $v)
					{
						$val[$k] = renderIfJson($v);
					}
					$val = implode('<br />', $val);
				}
				else
				{
					if (isset($f[$field->get('name')]) && isset($f[$field->get('name')][$value]))
					{
						$sh += $f[$field->get('name')][$value];
					}

					$val = renderIfJson($value);
				}

				$hd = array_diff($hd, $sh);

				// Is the field in the list of hidden dependents?
				if (in_array($field->get('name'), $hd))
				{
					if (!$isUser)
					{
						continue;
					}
					$cls[] = 'hide';
				}

				if (empty($value))
				{
					$cls[] = ($isUser) ? 'hidden' : 'hide';
				}
				?>
				<li class="<?php echo implode(' ', $cls); ?> section" id="input-section-<?php echo $this->escape($field->get('name')); ?>">
					<div class="section-content">
						<div class="key"><?php echo $field->get('label'); ?></div>
						<div class="value"><?php echo (!empty($val)) ? (is_array($val) ? implode(', ', $val) : $val) : Lang::txt('PLG_MEMBERS_PROFILE_NOT_SET'); ?></div>
						<br class="clear" />
						<?php
						if ($isUser)
						{
							if ($field->get('type') == 'url')
							{
								$value = strip_tags($val);
							}
							if ($field->get('type') == 'tags')
							{
								$value = $this->profile->tags('string');
							}
							if ($field->get('type') == 'address')
							{
								$value = $profile->get('profile_value');
								$value = $value ?: $this->profile->get($field->get('name'));
							}
							if (is_array($value))
							{
								$value = array_keys($value);
							}
							$formfield = $form->getField($field->get('name'));
							if ($formfield)
							{
								$formfield->setValue($value);

								if ($this->profile->get('access') > 2)
								{
									$access  = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY')  . '</label>';
									$access .= Lang::txt('PLG_MEMBERS_PROFILE_ACCESS_MUST_BE_PUBLIC');
									$access .= '<input type="hidden" name="access[' . $field->get('name') . ']" value="' . $profile->get('access', $field->get('access')) . '" />';
								}
								else
								{
									$access = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY')  . '</label>' . Components\Members\Helpers\Html::selectAccess('access[' . $field->get('name') . ']', $profile->get('access', $field->get('access')), 'input-select');
								}

								$this->view('default', 'edit')
									->set('registration_field', $field->get('name'))
									->set('profile_field', $field->get('name'))
									->set('registration', $field->get('action_edit'))
									->set('title', $field->get('label'))
									->set('profile', $this->profile)
									->set('isUser', $isUser)
									->set('inputs', $formfield->label . $formfield->input)
									->set('access', $access)
									->display();
							}
						}
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
			<?php } ?>
		<?php endforeach;

		if (!empty($scripts))
		{
			$this->js("function profileDependencies(){\n" . implode("\n", $scripts) . "\n};");
			$this->js("jQuery(document).ready(function($){\nprofileDependencies();\n});");
			$this->js("jQuery(document).on('ajaxLoad', function($){\nprofileDependencies();\n});");
		}
		?>

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
				if ($this->profile->get("sendEmail") == "" || is_null($this->profile->get("sendEmail")))
				{
					$cls .= ($isUser) ? " hidden" : " hide";
				}
				if (isset($update_missing) && in_array("optin", array_keys($update_missing)))
				{
					$cls = str_replace(' hide', '', $cls);
					$cls .= ' missing';
				}
				// dont show meant for stats only
				$cls .= (!$isUser) ? ' hide' : '';

				// get value of mail preference option
				switch ($this->profile->get('sendEmail'))
				{
					case '1':
						$mailPreferenceValue = 'Yes, send me emails';
						break;
					case '0':
						$mailPreferenceValue = 'No, don\'t send me emails';
						break;
					case '-1':
					default:
						$mailPreferenceValue = 'Unanswered';
						break;
				}

				$select  = '<select name="access[optin]" class="input-select">' . "\n";
				foreach ($legacy as $k => $v)
				{
					$selected = ($k == $this->params->get('access_optin'))
							  ? ' selected="selected"'
							  : '';
					$select .= ' <option value="' . $k . '"' . $selected . '>' . $v . '</option>' . "\n";
				}
				$select .= '</select>' . "\n";
			?>
			<li class="profile-optin section <?php echo $cls; ?>">
				<div class="section-content">
					<div class="key"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES'); ?></div>
					<div class="value"><?php echo $mailPreferenceValue; ?></div>
					<?php if ($isUser) : ?>
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
							$optin_html .= '<label for="sendEmail">';
							$optin_html .= '<select name="sendEmail" id="sendEmail" class="input-select">';
							foreach ($options as $key => $value)
							{
								$sel = ($key == $this->profile->get('sendEmail')) ? 'selected="selected"' : '';
								$optin_html .= '<option ' . $sel . ' value="' . $key . '">' . $value . '</option>';
							}
							$optin_html .= '</select>';
							$optin_html .= '</label>';

							if ($this->profile->get('access') > 2)
							{
								$access  = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY')  . '</label>';
								$access .= Lang::txt('PLG_MEMBERS_PROFILE_ACCESS_MUST_BE_PUBLIC');
								$access .= '<input type="hidden" name="sendEmail" value="' . $this->params->get('access_optin') . '" />';
							}
							else
							{
								$access = '<div class="block"><label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . $select . '</label></div>';
							}

							$this->view('default', 'edit')
							     ->set('registration_field', 'sendEmail')
							     ->set('profile_field', 'sendEmail')
							     ->set('registration', $this->profile->get('sendEmail'))
							     ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES'))
							     ->set('profile', $this->profile)
							     ->set('isUser', $isUser)
							     ->set('inputs', $optin_html)
							     ->set('access', $access)
							     ->display();
						?>
					<?php endif; ?>
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
	</ul>
</div><!-- /#profile-page-content -->
