{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 * NOTE: This is a plain text EMAIL template. Minimal formatting is intentional.
 */
--}}
@php
// Get the group
$group = \Hubzero\User\Group::getInstance($announcement->get('scope_id'));
$groupLink = rtrim(Request::base(), '/') . '/groups/' . $group->get('cn');
@endphp
{{ Lang::txt('Group Announcement') }} - {{ $group->get('description') }}
-------------------------------------------------------

{{ strip_tags($announcement->get('content')) }}

{{ $groupLink }}/announcements
