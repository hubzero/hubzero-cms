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

//get the members plugin access for this group
$memberAccess = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'members');
?>

<div class="group-content-header">
	<h3><?php echo Lang::txt('COM_GROUPS_OVERVIEW_ABOUT_HEADING'); ?></h3>
	<?php if ($isMember && $this->privateDesc != '') : ?>
		<div class="group-content-header-extra">
			<a id="toggle_description" class="hide" href="#"><?php echo Lang::txt('COM_GROUPS_SHOW_PUBLIC_DESCRIPTION'); ?></a>
		</div>
	<?php endif; ?>
</div>
<div id="description">
	<?php if ($isMember && $this->privateDesc != '') : ?>
		<div id="private">
			<?php echo $this->privateDesc; ?>
		</div>
		<div id="public" class="hide">
			<?php echo $this->publicDesc; ?>
		</div>
	<?php else : ?>
		<div id="public">
			<?php echo $this->publicDesc; ?>
		</div>
	<?php endif; ?>
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
				<?php if (in_array($profile->get('access'), User::getAuthorisedViewLevels())) { ?>
					<a href="<?php echo Route::url($profile->link()); ?>" class="member" title="<?php echo Lang::txt('COM_GROUPS_MEMBER_PROFILE', stripslashes($profile->get('name'))); ?>">
				<?php } else { ?>
					<div class="member">
				<?php } ?>
						<img src="<?php echo $profile->picture(0, true); ?>" alt="<?php echo $this->escape(stripslashes($profile->get('name'))); ?>" class="member-border" width="50px" height="50px" />
						<span class="name"><?php echo $this->escape(stripslashes($profile->get('name'))); ?></span>
						<span class="org"><?php print_r($profile->get('organization'));//echo $this->escape(stripslashes($profile->get('organization'))); ?></span>
				<?php if (in_array($profile->get('access'), User::getAuthorisedViewLevels())) { ?>
					</a>
				<?php } else { ?>
					</div>
				<?php } ?>
				<?php $counter++; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>