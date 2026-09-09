{{--
  Plain text email notification for event creation
  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$sef = \Hubzero\Facades\Route::url('index.php?option=' . $option . '&task=details&id=' . $row->id);
$titleVal = html_entity_decode(strip_tags(stripslashes($row->title)));
$descVal = html_entity_decode(strip_tags(stripslashes($row->content)));
@endphp
{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_ACT_ADDED_BY', $user->get('name'), $user->get('username')) }}


{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE') }}: {{ $titleVal }}

{{ \Hubzero\Facades\Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION') }}: {{ $descVal }}


{{ \Hubzero\Facades\Request::base() }}{{ ltrim($sef, '/') }}
