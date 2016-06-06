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

$loggedin = (! User::isGuest());
$isUser   = false;

$profiles = $this->profile->profiles()->ordered()->rows();

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
			$values = array($values);
		}
		$values[] = $profile->get('profile_value');

		$fields[$profile->get('profile_key')]->set('profile_value', $values);
	}
	else
	{
		$fields[$profile->get('profile_key')] = $profile;
	}
}
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

			<?php foreach ($this->fields as $field): ?>
				<?php
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

				if (in_array($profile->get('access', $field->get('access', 5)), User::getAuthorisedViewLevels()))
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

					if ($field->get('type') == 'tags')
					{
						$value = $this->profile->tags();
					}
					else
					{
						$value = $profile->get('profile_value');
						$value = $value ?: $this->profile->get($field->get('name'));
					}

					if (is_array($value))
					{
						foreach ($value as $k => $v)
						{
							if (strstr($v, '{'))
							{
								$v = json_decode((string)$v, true);

								if (!$v|| json_last_error() !== JSON_ERROR_NONE)
								{
									continue;
								}

								foreach ($v as $nm => $vl)
								{
									$v[$nm] = '<strong>' . $nm . ':</strong> ' . $vl;
								}

								$value[$k] = implode('<br />', $v);
							}
						}
					}

					if (empty($value))
					{
						$cls[] = 'hide';
					}
					?>
					<li class="<?php echo implode(' ', $cls); ?> section">
						<div class="section-content">
							<div class="key"><?php echo $field->get('label'); ?></div>
							<div class="value"><?php echo (!empty($value) ? (is_array($value) ? implode(', ', $value) : $value) : '(not set)'); ?></div>
						</div>
					</li>
				<?php } ?>
			<?php endforeach; ?>
		</ul>

		<?php
		$output = Event::trigger('groups.onGroupMemberAfter', array($this->group, $this->profile));
		echo implode("\n", $output);
		?>
	</div>
</div>
