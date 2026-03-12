{{--
  Tool User Preference — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS_USER_PREFS') . ': ' . $text, 'user');
  Toolbar::apply();
  Toolbar::save();
  Toolbar::spacer();
  Toolbar::cancel();

  $__view->js();

  $user = User::getInstance($row->user_id ?? 0);
@endphp

@if($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_USER_PREFS_LEGEND') }}">

    @if(!$row->id)
      {{-- New record: user picker --}}
      <div class="admin-field">
        <label for="field-user_id" class="label text-base-content">{{ Lang::txt('COM_TOOLS_USER_PREFS_USER') }}</label>
        @php
          $mc = Event::trigger('hubzero.onGetSingleEntry', [[
            'members',
            'fields[user_id]',
            'field-user_id',
            '',
            ''
          ]]);
        @endphp
        @if(count($mc) > 0)
          {!! $mc[0] !!}
        @else
          <input type="text"
                 name="fields[user_id]"
                 id="field-user_id"
                 class="input input-bordered w-full" />
        @endif
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_TOOLS_USER_PREFS_USER_HINT') }}</p>
      </div>
    @else
      <input type="hidden" name="fields[user_id]" id="field-user_id" value="{{ $row->user_id }}" />
    @endif

    @php
      $classValUrl = Route::url(
        'index.php?option=' . $option . '&controller=' . $controller . '&task=getClassValues', false
      );
    @endphp
    <div class="admin-field" data-href="{{ $classValUrl }}">
      <label for="class_id" class="label text-base-content">{{ Lang::txt('COM_TOOLS_USER_PREFS_CLASS') }}</label>
      {!! $classes !!}
    </div>

    <div class="admin-field">
      <label for="field-jobs" class="label text-base-content">{{ Lang::txt('COM_TOOLS_USER_PREFS_JOBS') }}</label>
      <input type="text"
             name="fields[jobs]"
             id="field-jobs"
             class="input input-bordered w-full"
             @readonly($row->class_id)
             value="{{ $row->jobs ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-params" class="label text-base-content">{{ Lang::txt('COM_TOOLS_USER_PREFS_PREFERENCES') }}</label>
      <textarea name="fields[params]"
                id="field-params"
                rows="5"
                class="textarea textarea-bordered w-full font-mono text-sm">{{ $row->params ?? '' }}</textarea>
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_USER_PREFS_ID') }}</td>
          <td>{{ $row->user_id ?? '—' }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_USER_PREFS_USERNAME') }}</td>
          <td>{{ $user ? e($user->username) : '—' }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_USER_PREFS_NAME') }}</td>
          <td>{{ $user ? e($user->name) : '—' }}</td>
        </tr>
      </tbody>
    </table>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->id ?? '' }}" />
</x-admin-edit>
