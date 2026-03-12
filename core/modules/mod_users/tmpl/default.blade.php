{{--
  mod_users — pending user approval widget

  Shows count of users requiring approval, or an all-clear message.

  Variables: $unapproved, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $count = is_countable($unapproved) ? count($unapproved) : 0;
@endphp

@if ($count > 0)
  <a href="{{ Route::url('index.php?option=com_members&approved=0', false) }}"
     class="flex items-center gap-3 p-3 rounded-lg bg-warning/10 hover:bg-warning/20 transition-colors">
    <div class="text-3xl font-bold text-warning leading-none">{{ $count }}</div>
    <div class="text-sm">{{ Lang::txts('MOD_USERS_REQUIRE_APPROVAL', $count) }}</div>
  </a>
@else
  <div class="flex items-center gap-2 text-sm text-success-fg py-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
      <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ Lang::txt('MOD_USERS_ALL_CLEAR') }}
  </div>
@endif
