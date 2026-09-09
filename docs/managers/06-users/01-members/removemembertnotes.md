<!--
status: imported
source: https://help.hubzero.org/documentation/240/managers/users/members/removemembertnotes
source-id: 3356
modified: 2021-03-04
imported: 2026-09-09
source-state: unpublished
-->
# Members Removal Tech Notes

## Com_members

// Check if I am a Super Admin
$iAmSuperAdmin = User::authorise('core.admin');

// Loop through each ID and delete the necessary items
foreach ($ids as $id)
{
// Remove the profile
$user = Member::oneOrFail(intval($id));

// Access checks.
$allow = User::authorise('core.delete', 'com_members');

// Don't allow non-super-admin to delete a super admin
$allow = (!$iAmSuperAdmin && Access::check($user->get('id'), 'core.admin')) ? false : $allow;

if (!$allow)
{
Notify::warning(Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
continue;
}

$data = $user->toArray();

if (!$user->destroy())
{
Notify::error($user->getError());
continue;
}

Event::trigger('user.onUserAfterDelete', array($data, true, $this->getError()));

$i++;
}

## Member is removed from the User - XUsers plugin

$xprofile = \\Hubzero\\User\\Profile::getInstance($user['id']);

// remove user from groups
\\Hubzero\\User\\Helper::removeUserFromGroups($user['id']);

if (is_object($xprofile))
{
$xprofile->delete();
}

\\Hubzero\\Auth\\Link::delete_by_user_id($user['id']);

// Check if quota exists for the user
require_once Component::path('com_members') . DS . 'models' . DS . 'quota.php';

$quota = Components\\Members\\Models\\Quota::all()
->whereEquals('user_id', $user['id'])
->row();

if ($quota->get('id'))
{
$quota->destroy();
}

if ($success)
{
Event::trigger('members.onMemberAfterDelete', array($user, $success, $msg));
}

com_members model

/\*\*
\* Delete the record and all associated data
\*
\* @return boolean False if error, True on success
\*/
public function destroy()
{
$data = $this->toArray();

Event::trigger('user.onUserBeforeDelete', array($data));

// Remove profile fields
foreach ($this->profiles()->rows() as $field)
{
if (!$field->destroy())
{
$this->addError($field->getError());
return false;
}
}

// Remove notes
foreach ($this->notes()->rows() as $note)
{
if (!$note->destroy())
{
$this->addError($note->getError());
return false;
}
}

// Remove hosts
foreach ($this->hosts()->rows() as $host)
{
if (!$host->destroy())
{
$this->addError($host->getError());
return false;
}
}

// Remove tags
$this->tag('');

// Attempt to delete the record
$result = parent::destroy();

if ($result)
{
Event::trigger('user.onUserAfterDelete', array($data, true, $this->getError()));
}

## Member is removed from the User - Hubzero plugin

$db = App::get('db');
$query = $db->getQuery()
->delete('#\__session')
->whereEquals('userid', (int) $user['id']);
$db->setQuery($query->toString());
$db->query();

## Member is removed from the User - Geo plugin

// Check params for group name
$groupAlias = $this->params->get('group', false);

if ($groupAlias)
{
// Get the group
$group = \\Hubzero\\User\\Group::getInstance($groupAlias);

if (is_object($group))
{
// Remove the user from the group
$group->remove('members', array($user['id']));

// Update the groups
$group->update();
}
}

## Member is removed from the User - LDAP plugin

\\Hubzero\\Utility\\Ldap::syncUser($user['id']);

## Member is removed from the User - Middleware plugin

$userId = \\Hubzero\\Utility\\Arr::getValue($user, 'id', 0, 'int');

if ($userId)
{
try
{
$db = App::get('db');
$db->setQuery("DELETE FROM `#\__users_quotas` WHERE `user_id`=" . $userId);

if (!$db->query())
{
throw new Exception($db->getErrorMsg());
}

$db->setQuery("DELETE FROM `#\__users_tool_preferences` WHERE `user_id`=" . $userId);

if (!$db->query())
{
throw new Exception($db->getErrorMsg());
}
}
catch (Exception $e)
{
Log::error($e->getMessage());
return false;
}
}
