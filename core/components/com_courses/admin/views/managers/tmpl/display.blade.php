{{--
  Courses — Managers iframe view (tmpl=component)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('managers.blade.js');

  $roles     = $course->offering(0)->roles(array('alias' => '!student'));
  $offerings = $course->offerings();
  $managers  = $course->managers(array(), true);

  $routeUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
@endphp

@if(!empty($errors))
  <div class="alert alert-error mb-2">
    {!! implode('<br />', $errors) !!}
  </div>
@endif

<div id="groups" class="p-2">
  {{-- Add user form --}}
  <form action="{{ $routeUrl }}" method="post" class="mb-4">
    <div class="flex items-end gap-2 flex-wrap">
      <div>
        <label class="label label-text text-base-content text-xs" for="field-usernames">
          {{ Lang::txt('COM_COURSES_ENTER_USERS') }}
        </label>
        <input type="text"
               name="usernames"
               id="field-usernames"
               value=""
               class="input input-bordered input-sm" />
      </div>
      <div>
        <label for="filter-role" class="sr-only">{{ Lang::txt('COM_COURSES_COL_ROLE') }}</label>
        <select name="role" id="filter-role" class="select select-bordered select-sm">
          @foreach($roles as $role)
            <option value="{{ $role->id }}">
              {{ $role->title }}
            </option>
          @endforeach
          @foreach($offerings as $offering)
            @php
              $oroles = $offering->roles(array('offering_id' => $offering->get('id')));
            @endphp
            @if($oroles && count($oroles))
              <optgroup label="{{ Lang::txt('Offering:') }} {{ $offering->get('title') }}">
                @foreach($oroles as $role)
                  <option value="{{ $role->id }}">
                    {{ $role->title }}
                  </option>
                @endforeach
              </optgroup>
            @endif
          @endforeach
        </select>
      </div>
      <div>
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="id" value="{{ $course->get('id') }}" />
        <input type="hidden" name="task" value="add" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_COURSES_ADD_USER') }}
        </button>
      </div>
    </div>

    {!! Html::input('token') !!}
  </form>

  {{-- Existing managers list --}}
  <form action="{{ $routeUrl }}" method="post" id="adminForm">
    <div class="mb-2">
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />
      <input type="hidden" name="tmpl" value="component" />
      <input type="hidden" name="id" value="{{ $course->get('id') }}" />
      <input type="hidden" name="task" id="task" value="remove" />

      <button type="submit" name="action" class="btn btn-sm btn-error btn-outline">
        {{ Lang::txt('COM_COURSES_REMOVE_USER') }}
      </button>
    </div>

    <table class="table table-sm table-zebra w-full">
      <tbody>
        @if(count($managers) > 0)
          @foreach($managers as $i => $manager)
            @php
              $u = User::getInstance($manager->get('user_id'));
              if (!is_object($u)) {
                  continue;
              }
              $memberUrl = Route::url(
                  'index.php?option=com_members&controller=members&task=edit&id=' . $u->get('id'), false
              );
            @endphp
            <tr>
              <td class="w-8">
                <input type="hidden"
                       name="entries[{{ $i }}][course_id]"
                       value="{{ $manager->get('course_id') }}" />
                <input type="hidden"
                       name="entries[{{ $i }}][offering_id]"
                       value="{{ $manager->get('offering_id', 0) }}" />
                <input type="hidden"
                       name="entries[{{ $i }}][section_id]"
                       value="{{ $manager->get('section_id', 0) }}" />
                <input type="hidden"
                       name="entries[{{ $i }}][user_id]"
                       value="{{ $u->get('id') }}" />
                <input type="checkbox"
                       name="entries[{{ $i }}][select]"
                       value="{{ $u->get('id') }}"
                       aria-label="{{ $u->get('name', Lang::txt('COM_COURSES_UNKNOWN')) }}"
                       class="checkbox checkbox-sm" />
              </td>
              <td>
                <a href="{{ $memberUrl }}" target="_parent">
                  @if($u->get('name'))
                    {{ $u->get('name') }} ({{ $u->get('username') }})
                  @else
                    {{ Lang::txt('COM_COURSES_UNKNOWN') }}
                  @endif
                </a>
              </td>
              <td>
                <a href="mailto:{{ $u->get('email') }}">
                  {{ $u->get('email') }}
                </a>
              </td>
              <td>
                <select name="entries[{{ $i }}][role_id]"
                        aria-label="{{ Lang::txt('COM_COURSES_COL_ROLE') }}"
                        class="select select-bordered select-sm entry-role">
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                            @selected($manager->get('role_id') == $role->id)>
                      {{ $role->title }}
                    </option>
                  @endforeach
                  @foreach($offerings as $offering)
                    @php
                      $oroles = $offering->roles(array('offering_id' => $offering->get('id')));
                    @endphp
                    @if($oroles && count($oroles))
                      <optgroup label="{{ Lang::txt('Offering:') }} {{ $offering->get('title') }}">
                        @foreach($oroles as $role)
                          <option value="{{ $role->id }}"
                                  @selected($manager->get('role_id') == $role->id)>
                            {{ $role->title }}
                          </option>
                        @endforeach
                      </optgroup>
                    @endif
                  @endforeach
                </select>
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>

    {!! Html::input('token') !!}
  </form>
</div>
