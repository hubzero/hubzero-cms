{{--
  Member Todo — browse shared todo items.

  Variables from plugin (onMembers):
    $model    — todo model
    $todo     — todo instance
    $projects — array of project objects
    $member   — member profile object
    $filters  — filters array

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('jquery.datepicker.css', 'system')
      ->css('jquery.timepicker.css', 'system')
      ->css()
      ->css('todo.css', 'plg_projects_todo')
      ->js()
      ->js('jquery.timepicker', 'system');

  $cfilters = ['mine' => 1, 'active' => 1, 'editor' => 1];
  $url = 'index.php?option=com_members&id=' . $member->get('id') . '&active=todo';
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_TODO') }}</h3>

@if ($model->entries('count', $cfilters))
  <div class="flex justify-end mb-4">
    <a class="btn btn-primary btn-sm showinbox" href="{{ Route::url($url . '&action=new') }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      {{ Lang::txt('PLG_MEMBERS_TODO_ADD_TODO') }}
    </a>
  </div>
@endif

@php
  $isOwnerNoProjects = User::get('id') == $member->get('id') && empty($projects);
  $noTodoEntries = !$todo->entries('count', ['projects' => $projects]);
@endphp

@if ($isOwnerNoProjects || $noTodoEntries)
  <div class="text-center py-8">
    <p class="text-base-content/70 mb-4">{{ Lang::txt('PLG_MEMBERS_TODO_INTRO_EMPTY') }}</p>
    <p class="mb-2"><strong>{{ Lang::txt('PLG_MEMBERS_TODO_INTRO_HOW_TO_START') }}</strong></p>
    @php $projectsUrl = Route::url('index.php?option=com_projects'); @endphp
    <p class="text-base-content/70">{!! Lang::txt('PLG_MEMBERS_TODO_INTRO_HOW_TO_START_EXPLANATION', $projectsUrl) !!}</p>
  </div>
@else
  <div>
    @if ($__view->getError())
      <p class="alert alert-error" role="alert">{{ $__view->getError() }}</p>
    @endif

    {!! implode("\n", Event::trigger('projects.onShared', ['todo', $model, $projects, $member->get('id'), $filters])) !!}
  </div>
@endif
