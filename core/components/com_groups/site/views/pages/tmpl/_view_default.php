<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!function_exists('isSystemUser'))
{
	function isSystemUser( $userid )
	{
		return ($userid < 1000) ? null : $userid;
	}
}

// get group params
$params = Component::params("com_groups");
$displaySystemUsers = $params->get('display_system_users', 'no');

//get this groups params
$gparams = new \Hubzero\Config\Registry($this->group->get('params'));
$displaySystemUsers = $gparams->get('display_system_users', $displaySystemUsers);

//get the group members
$members = $this->group->get('members');
shuffle($members);

//if we dont want to display system users
//filter values through callback above and then reset array keys
if ($displaySystemUsers == 'no')
{
	$members = array_map("isSystemUser", $members);
	$members = array_values(array_filter($members));
}

//are we a group member
$isMember = (in_array(User::get('id'), $this->group->get('members'))) ? true : false;
$accessLevel = 0;
if ($isMember)
{
	$accessLevel = 2;
}
else
{
	$accessLevel = (!User::isGuest()) ? 1 : 0;
}
//get the members plugin access for this group
$memberAccess = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'members');
?>

<div class="group-content-header">
	<h3><?php echo Lang::txt('COM_GROUPS_OVERVIEW_ABOUT_HEADING'); ?></h3>
	<?php
	foreach ($this->fields as $field)
	{
		if ($field->get('access') > $accessLevel)
		{
			continue;
		}
		$answers = $field->answers->toArray();
		$answers = array_column($answers, 'value');
		if (!$value = $field->renderValue($answers))
		{
			continue;
		}

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

		if (is_array($value))
		{
			$value = implode('<br />', $value);
		}
		echo '<div class="input-wrap" id="input-' . $field->get('name') . '">';
		echo '<h4>' . $field->get('label') . '</h4>';
		echo '<div class="input-value">';
		echo $value;
		echo '</div>';
		echo '</div>';
	}
	?>
</div>

<?php if ($memberAccess == 'anyone' || ($memberAccess == 'registered' && !User::isGuest()) || ($memberAccess == 'members' && $isMember)) : ?>
	<div class="group-content-header">
		<h3><?php echo Lang::txt('COM_GROUPS_OVERVIEW_MEMBERS_HEADING'); ?></h3>
		<div class="group-content-header-extra">
			<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=members'); ?>">
				<?php echo Lang::txt('COM_GROUPS_OVERVIEW_MEMBERS_BTN_TEXT') . ' &rarr;'; ?>
			</a>
		</div>
	</div>

	<div id="member_browser" class="member_browser">
		<?php
		$counter = 1;
		require_once Component::path('com_members') . DS . 'models' . DS . 'member.php';

		$profiles = Components\Members\Models\Member::all()
			->including('profiles')
			->whereIn('id', $members)
			->rows();

		foreach ($profiles as $profile) : ?>
			<?php if ($counter <= 12 && $profile->get('id')) : ?>
				<?php if (in_array($profile->get('access'), User::getAuthorisedViewLevels()) && ($profile->get('activation') > 0)) { ?>
					<a href="<?php echo Route::url($profile->link()); ?>" class="member" title="<?php echo Lang::txt('COM_GROUPS_MEMBER_PROFILE', stripslashes($profile->get('name'))); ?>">
				<?php } else { ?>
					<div class="member">
				<?php } ?>
						<img src="<?php echo $profile->picture(0, true); ?>" alt="<?php echo $this->escape(stripslashes($profile->get('name'))); ?>" class="member-border" width="50px" height="50px" />
						<span class="name"><?php echo $this->escape(stripslashes($profile->get('name'))); ?></span>
						<span class="org"><?php echo $this->escape(stripslashes($profile->get('organization'))); ?></span>
				<?php if (in_array($profile->get('access'), User::getAuthorisedViewLevels())) { ?>
					</a>
				<?php } else { ?>
					</div>
				<?php } ?>
				<?php $counter++; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; 