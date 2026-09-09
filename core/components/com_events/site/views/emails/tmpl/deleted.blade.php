{{--
  Plain text email notification for event deletion
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$titleVal = html_entity_decode(strip_tags(stripslashes($event->title)));
$descVal = html_entity_decode(strip_tags(stripslashes($event->content)));
@endphp
{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_ACT_DELETED_BY', $user->get('name'), $user->get('login')) }}


{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE') }}: {{ $titleVal }}
{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION') }}: {{ $descVal }}
