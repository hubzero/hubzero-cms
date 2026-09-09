{{--
  Course managers management interface.

  Renders in component/modal mode (no page shell) — used for adding,
  removing, and updating course manager roles.

  Variables from controller (displayTask):
    $course — Course model instance

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $roles     = $course->offering(0)->roles(['alias' => '!student']);
  $offerings = $course->offerings();
  $managers  = $course->managers([], true);
  $formUrl   = Route::url('index.php?option=' . $option, false);
@endphp

@if($__view->getError())
  <div class="alert alert-error mb-4" role="alert">
    {{ implode('<br />', $__view->getErrors()) }}
  </div>
@endif

<div class="p-4">
  {{-- Add manager form --}}
  <form action="{{ $formUrl }}" method="post" class="mb-6">
    <fieldset>
      <legend class="text-sm font-semibold mb-3">
        {{ Lang::txt('COM_COURSES_ADD_MANAGER_LABEL') }}
      </legend>

      <div class="flex gap-3 items-end flex-wrap">
        <div class="flex-1 min-w-[200px]">
          <label for="field-usernames" class="form-field-label">
            {{ Lang::txt('COM_COURSES_ADD_MANAGER_LABEL') }}
          </label>
          @php
            $mc = Event::trigger('hubzero.onGetMultiEntry', [
                ['members', 'usernames', 'field-usernames', '', '']
            ]);
          @endphp
          @if(count($mc) > 0)
            {!! $mc[0] !!}
          @else
            <input type="text" name="usernames" id="field-usernames"
                   class="input input-bordered w-full" value="" />
          @endif
        </div>

        <div class="w-48">
          <label for="field-role" class="form-field-label">
            {{ Lang::txt('COM_COURSES_SELECT_ROLE') }}
          </label>
          <select name="role" id="field-role" class="select select-bordered w-full">
            @foreach($roles as $role)
              <option value="{{ $role->id }}">
                {{ e(stripslashes($role->title)) }}
              </option>
            @endforeach
            @foreach($offerings as $off)
              @php
                $oroles = $off->roles(['offering_id' => $off->get('id')]);
              @endphp
              @if($oroles && count($oroles))
                <optgroup label="{{ Lang::txt('COM_COURSES_OFFERING') }}: {{ e($off->get('title')) }}">
                  @foreach($oroles as $role)
                    <option value="{{ $role->id }}">
                      {{ e(stripslashes($role->title)) }}
                    </option>
                  @endforeach
                </optgroup>
              @endif
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn btn-primary">
          {{ Lang::txt('COM_COURSES_ADD') }}
        </button>
      </div>
    </fieldset>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="managers" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="id" value="{{ $course->get('id') }}" />
    <input type="hidden" name="gid" value="{{ $course->get('alias') }}" />
    <input type="hidden" name="task" value="add" />
    {!! Html::input('token') !!}
  </form>

  {{-- Existing managers --}}
  <form action="{{ $formUrl }}" method="post">
    <div class="overflow-x-auto">
      <table class="table table-sm">
        <thead>
          <tr>
            <th class="w-8">
              <span class="sr-only">{{ Lang::txt('COM_COURSES_SELECT') }}</span>
            </th>
            <th>{{ Lang::txt('COM_COURSES_MANAGER_NAME') }}</th>
            <th>{{ Lang::txt('COM_COURSES_MANAGER_ROLE') }}</th>
          </tr>
        </thead>
        <tbody>
          @if(count($managers) > 0)
            @foreach($managers as $i => $manager)
              @php
                $u = User::getInstance($manager->get('user_id'));
                if (!is_object($u)) { continue; }
                $displayName = $u->get('name')
                    ? e($u->get('name')) . ' (' . e($u->get('username')) . ')'
                    : Lang::txt('COM_COURSES_UNKNOWN');
                $memberUrl = Route::url('index.php?option=com_members&id=' . $u->get('id'), false);
              @endphp
              <tr>
                <td>
                  <input type="hidden" name="entries[{{ $i }}][course_id]"
                         value="{{ $manager->get('course_id') }}" />
                  <input type="hidden" name="entries[{{ $i }}][offering_id]"
                         value="{{ $manager->get('offering_id', 0) }}" />
                  <input type="hidden" name="entries[{{ $i }}][section_id]"
                         value="{{ $manager->get('section_id', 0) }}" />
                  <input type="hidden" name="entries[{{ $i }}][user_id]"
                         value="{{ $u->get('id') }}" />
                  <input type="checkbox"
                         class="checkbox checkbox-sm"
                         name="entries[{{ $i }}][select]"
                         value="{{ $u->get('id') }}"
                         aria-label="{{ Lang::txt('COM_COURSES_SELECT_MANAGER', $displayName) }}" />
                </td>
                <td>
                  <a class="link link-hover" href="{{ $memberUrl }}">
                    {!! $displayName !!}
                  </a>
                </td>
                <td>
                  <select name="entries[{{ $i }}][role_id]"
                          class="select select-bordered select-sm"
                          aria-label="{{ Lang::txt('COM_COURSES_ROLE_FOR', $displayName) }}">
                    @foreach($roles as $role)
                      <option value="{{ $role->id }}"
                              @if($manager->get('role_id') == $role->id) selected @endif>
                        {{ e(stripslashes($role->title)) }}
                      </option>
                    @endforeach
                    @foreach($offerings as $off)
                      @php
                        $oroles = $off->roles(['offering_id' => $off->get('id')]);
                      @endphp
                      @if($oroles && count($oroles))
                        <optgroup label="{{ Lang::txt('COM_COURSES_OFFERING') }}: {{ e($off->get('title')) }}">
                          @foreach($oroles as $role)
                            <option value="{{ $role->id }}"
                                    @if($manager->get('role_id') == $role->id) selected @endif>
                              {{ e(stripslashes($role->title)) }}
                            </option>
                          @endforeach
                        </optgroup>
                      @endif
                    @endforeach
                  </select>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="3" class="text-center text-base-content/50">
                {{ Lang::txt('COM_COURSES_NO_MANAGERS') }}
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    <div class="flex gap-2 mt-4">
      <button type="submit" name="action" value="remove" class="btn btn-error btn-sm">
        {{ Lang::txt('COM_COURSES_REMOVE') }}
      </button>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="managers" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="id" value="{{ $course->get('id') }}" />
    <input type="hidden" name="gid" value="{{ $course->get('alias') }}" />
    <input type="hidden" name="task" value="remove" />
    {!! Html::input('token') !!}
  </form>
</div>
