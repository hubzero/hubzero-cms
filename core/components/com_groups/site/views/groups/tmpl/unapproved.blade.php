{{--
  Unapproved group notice page.

  Variables from controller:
    $title   — string: page title
    $group   — Group object
    $option  — string: component option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();

  $groupUrl   = Route::url('index.php?option=com_groups&controller=groups&cn=' . $group->get('cn'));
  $allUrl     = Route::url('index.php?option=com_groups');
  $myGroupUrl = Route::url('index.php?option=com_members&task=myaccount&active=groups');
@endphp

<x-page-container :title="$title">
  <div class="max-w-lg mx-auto text-center py-8">
    <p class="text-lg font-semibold mb-2">{{ $group->get('description') }}</p>

    <div class="alert alert-warning mb-6" role="alert">
      {{ Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING') }}
    </div>

    @if(in_array(User::get('id'), $group->get('invitees')))
      <div class="divider"></div>
      <a href="{{ $groupUrl }}&task=accept" class="btn btn-success">
        {{ Lang::txt('COM_GROUPS_ACCEPT_INVITE') }}
      </a>
      <div class="divider"></div>
    @endif

    <p class="mt-4">
      <a class="link link-hover" href="{{ $allUrl }}">{{ Lang::txt('COM_GROUPS_ALL_GROUPS') }}</a>
      <span class="mx-1">|</span>
      <a class="link link-hover" href="{{ $myGroupUrl }}">{{ Lang::txt('COM_GROUPS_MY_GROUPS') }}</a>
    </p>
  </div>
</x-page-container>
