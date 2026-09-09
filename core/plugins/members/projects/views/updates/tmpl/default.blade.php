{{--
  Member Projects — updates feed view.

  Variables from plugin (onMembers):
    $uid          — member ID
    $projectcount — total project count
    $newcount     — new activity count
    $content      — rendered updates HTML

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css();

  $base = 'index.php?option=com_members&id=' . $uid . '&active=projects';
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_PROJECTS') }}</h3>

<div class="flex justify-end mb-4">
  <a class="btn btn-primary btn-sm" href="{{ Route::url('index.php?option=com_projects&task=start') }}">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
    {{ Lang::txt('PLG_MEMBERS_PROJECTS_ADD') }}
  </a>
</div>

{{-- Sub-menu tabs --}}
<div role="tablist" class="tabs tabs-border mb-4">
  <a role="tab" class="tab" href="{{ Route::url($base . '&action=all') }}">
    {{ Lang::txt('PLG_MEMBERS_PROJECTS_LIST') }} ({{ $projectcount }})
  </a>
  <a role="tab" class="tab tab-active" href="{{ Route::url($base . '&action=updates') }}" aria-selected="true">
    {{ Lang::txt('PLG_MEMBERS_PROJECTS_UPDATES_FEED') }}
    @if ($newcount)
      <span class="badge badge-primary badge-sm ml-1">{{ $newcount }}</span>
    @endif
  </a>
</div>

<div id="project-updates">
  {!! $content !!}
</div>
