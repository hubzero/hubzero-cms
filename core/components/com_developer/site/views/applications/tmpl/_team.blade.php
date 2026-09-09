{{--
  Team member listing sub-template.

  Variables (set by parent):
    $members  — Collection of Application\Member models
    $cls      — Optional CSS class string (e.g. 'compact')

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$cls = $cls ?? '';
@endphp

<div class="flex flex-wrap gap-3 {{ $cls }}">
  @foreach ($members as $member)
    @php
    $profile = $member->getProfile();
    $me = ($profile->get('uidNumber') == User::get('id'));
    $profileName = $profile->get('name');
    @endphp
    <div @class([
      'flex items-center gap-2 rounded-lg border border-base-300 px-3 py-2',
      'bg-primary/5' => $me,
    ])>
      <div class="avatar">
        <div class="w-8 rounded-full">
          <img src="{{ $profile->picture(0, true) }}" alt="" />
        </div>
      </div>
      <div>
        <a class="link link-hover text-sm font-medium" href="{{ $profile->link() }}">
          {{ $profileName }}
        </a>
        @if ($me)
          <span class="badge badge-xs badge-primary ml-1">You</span>
        @endif
      </div>
      @if (!$me)
        @php
        $removeUrl = Route::url($member->link('remove'));
        @endphp
        <a class="btn btn-ghost btn-xs text-error ml-auto"
           href="{{ $removeUrl }}"
           data-confirm="{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE_CONFIRM') }}">
          {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE') }}
        </a>
      @endif
    </div>
  @endforeach
</div>
