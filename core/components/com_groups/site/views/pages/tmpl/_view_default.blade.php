{{--
  Default group overview page — about section + member browser.

  Variables:
    $group   — Group object
    $fields  — collection of custom Field models
    $config  — Registry: component config
    $content — string: page content (unused by this partial)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Display system users setting
  $params = Component::params('com_groups');
  $displaySystemUsers = $params->get('display_system_users', 'no');
  $gparams = new \Hubzero\Config\Registry($group->get('params'));
  $displaySystemUsers = $gparams->get('display_system_users', $displaySystemUsers);

  // Get members
  $allMembers = $group->get('members');
  shuffle($allMembers);

  // Filter system users if needed
  if ($displaySystemUsers === 'no') {
      $allMembers = array_values(array_filter(array_map(function ($uid) {
          return ($uid < 1000) ? null : $uid;
      }, $allMembers)));
  }

  $isMember    = in_array(User::get('id'), $group->get('members'));
  $accessLevel = $isMember ? 2 : (User::isGuest() ? 0 : 1);
  $memberAccess = \Hubzero\User\Group\Helper::getPluginAccess($group, 'members');
@endphp

<div class="group-content-header">
  <h3>{{ Lang::txt('COM_GROUPS_OVERVIEW_ABOUT_HEADING') }}</h3>
  @foreach($fields as $field)
    @php
      if ($field->get('access') > $accessLevel) {
          continue;
      }
      $answers = array_column($field->answers->toArray(), 'value');
      $value = $field->renderValue($answers);
      if (!$value) {
          continue;
      }

      if ($field->get('type') === 'textarea') {
          $value = Html::content('prepare', $value);
      }
      if ($field->get('type') === 'url') {
          $parsed = parse_url($value);
          if (empty($parsed['scheme'])) {
              $value = 'http://' . ltrim($value, '/');
          }
          $value = '<a href="' . $value . '" rel="external">' . e($value) . '</a>';
      }

      if (is_array($value)) {
          $value = implode('<br />', $value);
      }
    @endphp
    <div class="input-wrap" id="input-{{ $field->get('name') }}">
      <h4>{{ $field->get('label') }}</h4>
      <div class="input-value">{!! $value !!}</div>
    </div>
  @endforeach
</div>

@if(
  $memberAccess === 'anyone'
  || ($memberAccess === 'registered' && !User::isGuest())
  || ($memberAccess === 'members' && $isMember)
)
  <div class="group-content-header">
    <h3>{{ Lang::txt('COM_GROUPS_OVERVIEW_MEMBERS_HEADING') }}</h3>
    <div class="group-content-header-extra">
      <a href="{{ Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=members') }}">
        {{ Lang::txt('COM_GROUPS_OVERVIEW_MEMBERS_BTN_TEXT') }} &rarr;
      </a>
    </div>
  </div>

  <div id="member_browser" class="member_browser flex flex-wrap gap-4">
    @php
      $profiles = \Components\Members\Models\Member::all()
          ->including('profiles')
          ->whereIn('id', $allMembers)
          ->rows();
      $counter = 0;
    @endphp
    @foreach($profiles as $profile)
      @if($counter < 12 && $profile->get('id'))
        @php
          $viewable = in_array($profile->get('access'), User::getAuthorisedViewLevels())
              && ($profile->get('activation') > 0);
          $counter++;
        @endphp
        @if($viewable)
          <a href="{{ Route::url($profile->link()) }}" class="member"
             title="{{ Lang::txt('COM_GROUPS_MEMBER_PROFILE', stripslashes($profile->get('name'))) }}">
        @else
          <div class="member">
        @endif
            <img src="{{ $profile->picture(0, true) }}"
                 alt="{{ e(stripslashes($profile->get('name'))) }}"
                 class="member-border rounded-full"
                 width="50" height="50" />
            <span class="name">{{ e(stripslashes($profile->get('name'))) }}</span>
            <span class="org">{{ e(stripslashes($profile->get('organization', ''))) }}</span>
        @if($viewable)
          </a>
        @else
          </div>
        @endif
      @endif
    @endforeach
  </div>
@endif
