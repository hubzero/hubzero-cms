{{--
  Resource contribution step — Authors & group ownership.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $task       — current task
    $step       — current step number
    $next_step  — next step number
    $steps      — array of step names
    $id         — resource ID
    $row        — resource model
    $groups     — user's groups
    $progress   — array of step completion flags

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('create.css');
  $__view->js('create.js');

  $draftUrl = Route::url('index.php?option=' . $option . '&task=draft');
  $actionUrl = Route::url(
      'index.php?option=' . $option
      . '&task=draft&step=' . $next_step
      . '&id=' . $id
  );
  $iframeSrc = '/index.php?option=' . $option
      . '&controller=authors&id=' . $id
      . '&tmpl=component';
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $draftUrl }}">
      {{ Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION') }}
    </a>
  @endslot

  @include('steps::steps', [
      'option'   => $option,
      'step'     => $step,
      'steps'    => $steps,
      'id'       => $id,
      'resource' => $row,
      'progress' => $progress,
  ])

  @if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ $__view->getError() }}</span>
    </div>
  @endif

  <form action="{{ $actionUrl }}" method="post" id="hubForm" class="space-y-8">
    {{-- Group ownership section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
          <legend class="fieldset-legend text-lg font-semibold">
            {{ Lang::txt('COM_CONTRIBUTE_GROUPS_OWNERSHIP') }}
          </legend>

          @if($groups && count($groups) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="group_owner" class="label">
                  <span class="label-text">
                    {{ Lang::txt('COM_CONTRIBUTE_GROUPS_GROUP') }}
                    <span class="text-base-content/50 text-sm">
                      ({{ Lang::txt('COM_CONTRIBUTE_OPTIONAL') }})
                    </span>
                  </span>
                </label>
                <select name="group_owner"
                        id="group_owner"
                        class="select select-bordered w-full">
                  <option value="">{{ Lang::txt('COM_CONTRIBUTE_SELECT_GROUP') }}</option>
                  @foreach($groups as $group)
                    <option value="{{ e($group->cn) }}"
                            @selected($row->group_owner->get('cn') == $group->cn)>
                      {{ e(stripslashes($group->description)) }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div>
                <label for="access" class="label">
                  <span class="label-text">
                    {{ Lang::txt('COM_CONTRIBUTE_GROUPS_ACCESS_LEVEL') }}
                    <span class="text-base-content/50 text-sm">
                      ({{ Lang::txt('COM_CONTRIBUTE_OPTIONAL') }})
                    </span>
                  </span>
                </label>
                <select name="access"
                        id="access"
                        class="select select-bordered w-full">
                  <option value="0" @selected($row->access == 0)>
                    {{ Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC') }}
                  </option>
                  <option value="1" @selected($row->access == 1)>
                    {{ Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED') }}
                  </option>
                  <option value="3" @selected($row->access == 3)>
                    {{ Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED') }}
                  </option>
                  <option value="4" @selected($row->access == 4)>
                    {{ Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE') }}
                  </option>
                </select>
              </div>
            </div>

            <div class="mt-4 text-sm text-base-content/70 space-y-1">
              <p>
                <strong>{{ Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC') }}</strong>
                = {{ Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC_EXPLANATION') }}
              </p>
              <p>
                <strong>{{ Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED') }}</strong>
                = {{ Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED_EXPLANATION') }}
              </p>
              <p>
                <strong>{{ Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED') }}</strong>
                = {{ Lang::txt('COM_CONTRIBUTE_ACCESS_PROTECTED_EXPLANATION') }}
              </p>
              <p>
                <strong>{{ Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE') }}</strong>
                = {{ Lang::txt('COM_CONTRIBUTE_ACCESS_PRIVATE_EXPLANATION') }}
              </p>
            </div>
          @else
            <div role="alert" class="alert alert-info">
              <span>{{ Lang::txt('COM_CONTRIBUTE_GROUPS_JOIN') }}</span>
            </div>
          @endif
        </fieldset>
      </div>

      <div class="space-y-4">
        <div class="card bg-base-200 shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-base">
              {{ Lang::txt('COM_CONTRIBUTE_GROUPS_HEADER') }}
            </h3>
            <p class="text-sm text-base-content/70">
              {{ Lang::txt('COM_CONTRIBUTE_GROUPS_EXPLANATION') }}
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- Authors section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
          <legend class="fieldset-legend text-lg font-semibold">
            {{ Lang::txt('COM_CONTRIBUTE_AUTHORS_AUTHORS') }}
          </legend>

          <div>
            <iframe width="100%"
                    height="400"
                    name="authors"
                    id="authors"
                    title="{{ Lang::txt('COM_CONTRIBUTE_AUTHORS_AUTHORS') }}"
                    class="border border-base-300 rounded-box w-full"
                    src="{{ $iframeSrc }}"></iframe>
          </div>

          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="{{ $controller }}" />
          <input type="hidden" name="task" value="{{ $task }}" />
          <input type="hidden" name="step" value="{{ $next_step }}" />
          <input type="hidden" name="id" value="{{ $id }}" />
        </fieldset>
      </div>

      <div class="space-y-4">
        <div class="card bg-base-200 shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-base">
              {{ Lang::txt('COM_CONTRIBUTE_AUTHORS_NO_LOGIN') }}
            </h3>
            <p class="text-sm text-base-content/70">
              {{ Lang::txt('COM_CONTRIBUTE_AUTHORS_NO_LOGIN_EXPLANATION') }}
            </p>
          </div>
        </div>
        <div class="card bg-base-200 shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-base">
              {{ Lang::txt('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR') }}
            </h3>
            <p class="text-sm text-base-content/70">
              {{ Lang::txt('COM_CONTRIBUTE_AUTHORS_NOT_AUTHOR_EXPLANATION') }}
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('COM_CONTRIBUTE_NEXT') }}
      </button>
    </div>
  </form>
</x-page-container>
