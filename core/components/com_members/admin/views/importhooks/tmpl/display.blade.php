{{--
  Import Hooks — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_TITLE_HOOKS') }}"
    icon="import"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a @class(['active' => $controller == 'imports'])
         href="{!! Route::url('index.php?option=' . $option . '&controller=imports', false) !!}">
        {{ Lang::txt('COM_MEMBERS_IMPORT_TITLE_IMPORTS') }}
      </a>
    </li>
    <li>
      <a @class(['active' => $controller == 'importhooks'])
         href="{!! Route::url('index.php?option=' . $option . '&controller=importhooks', false) !!}">
        {{ Lang::txt('COM_MEMBERS_IMPORT_HOOKS') }}
      </a>
    </li>
  </ul>
</nav>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th scope="col">
            <input type="checkbox"
                   name="checkall-toggle"
                   id="checkall-toggle"
                   value=""
                   class="checkbox checkbox-sm toggle-all" />
            <label for="checkall-toggle"
                   class="sr-only visually-hidden">{{ Lang::txt('JGLOBAL_CHECK_ALL') }}</label>
          </th>
          <th scope="col" class="priority-3">
            {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FIELD_NAME') }}
          </th>
          <th scope="col" class="priority-2">
            {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FIELD_TYPE') }}
          </th>
          <th scope="col">
            {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FIELD_FILE') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            {!! $hooks->pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @if($hooks->count() > 0)
          @foreach($hooks as $i => $hook)
            <tr>
              <td>
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $hook->get('id') }}"
                       class="checkbox checkbox-sm" />
                <label for="cb{{ $i }}"
                       class="sr-only visually-hidden">{{ $hook->get('id') }}</label>
              </td>
              <td class="priority-3">
                {{ $hook->get('name') }}
                <br />
                <span class="hint">
                  {!! nl2br(e($hook->get('notes'))) !!}
                </span>
              </td>
              <td class="priority-2">
                @switch($hook->get('event'))
                  @case('postconvert')
                    {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT') }}
                    @break
                  @case('postmap')
                    {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_TYPE_POSTMAP') }}
                    @break
                  @default
                    {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE') }}
                @endswitch
              </td>
              <td>
                {{ $hook->get('file') }} &mdash;
                <a rel="noopener"
                   target="_blank"
                   href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=raw&id=' . $hook->get('id'), false) !!}">
                  {{ Lang::txt('COM_MEMBERS_IMPORTHOOK_DISPLAY_FILE_VIEWRAW') }}
                </a>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="4">{{ Lang::txt('Currently there are no import hooks.') }}</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</x-admin-form>
