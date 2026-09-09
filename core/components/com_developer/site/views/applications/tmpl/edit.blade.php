{{--
  Application create/edit form — daisyUI layout.

  Variables from controller:
    $application  — Application model (new or existing)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$isNew   = !$application->get('id');
$title   = $isNew
    ? Lang::txt('COM_DEVELOPER_API_APPLICATION_NEW')
    : Lang::txt('COM_DEVELOPER_API_APPLICATION_EDIT', $application->get('name'));
$return  = $isNew
    ? Route::url('index.php?option=com_developer&controller=applications')
    : Route::url($application->link());
$formAction = Route::url('index.php?option=com_developer');

$uris = implode(PHP_EOL, explode(' ', $application->get('redirect_uri')));
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-ghost btn-sm"
       href="{{ Route::url('index.php?option=com_developer&controller=applications') }}">
      {{ Lang::txt('COM_DEVELOPER_API_APPLICATIONS_ALL') }}
    </a>
  @endslot

  @if (!$isNew)
    @slot('sidebar')
      {{-- Reset Client Secret --}}
      <x-sidebar-card :title="Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET_CLIENT_SECRET')">
        <p class="text-sm mb-3">
          {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET_CLIENT_SECRET_DESC') }}
        </p>
        <form action="{{ $formAction }}" method="post">
          <button type="submit"
                  class="btn btn-warning btn-sm"
                  data-confirm="{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET_CLIENT_SECRET_CONFIRM') }}">
            {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_RESET') }}
          </button>
          <input type="hidden" name="option" value="com_developer" />
          <input type="hidden" name="controller" value="applications" />
          <input type="hidden" name="task" value="resetclientsecret" />
          <input type="hidden" name="id" value="{{ $application->get('id') }}" />
          {!! Html::input('token') !!}
        </form>
      </x-sidebar-card>

      {{-- Delete Application --}}
      <x-sidebar-card :title="Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE')">
        <p class="text-sm mb-3">
          {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE_DESC') }}
        </p>
        <form action="{{ $formAction }}" method="post">
          <button type="submit"
                  class="btn btn-error btn-sm"
                  data-confirm="{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE_CONFIRM') }}">
            {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_DELETE') }}
          </button>
          <input type="hidden" name="option" value="com_developer" />
          <input type="hidden" name="controller" value="applications" />
          <input type="hidden" name="task" value="delete" />
          <input type="hidden" name="id" value="{{ $application->get('id') }}" />
          {!! Html::input('token') !!}
        </form>
      </x-sidebar-card>
    @endslot
  @endif

  <form action="{{ $formAction }}" method="post" id="hubForm">
    {{-- Application Details --}}
    <x-form-section :heading="Lang::txt('COM_DEVELOPER_API_APPLICATION_DETAILS')">
      <x-form-field name="application[name]"
                    inputId="field-name"
                    :label="Lang::txt('COM_DEVELOPER_API_APPLICATION_NAME')"
                    :required="true">
        <input type="text"
               name="application[name]"
               id="field-name"
               class="input input-bordered w-full"
               value="{{ e($application->get('name')) }}"
               required />
      </x-form-field>

      <x-form-field name="application[description]"
                    inputId="field-description"
                    :label="Lang::txt('COM_DEVELOPER_API_APPLICATION_DESCRIPTION')"
                    :required="true">
        <textarea name="application[description]"
                  id="field-description"
                  class="textarea textarea-bordered w-full"
                  rows="6"
                  required>{{ e($application->get('description')) }}</textarea>
      </x-form-field>

      <x-form-field name="application[redirect_uri]"
                    inputId="field-redirect_uri"
                    :label="Lang::txt('COM_DEVELOPER_API_APPLICATION_REDIRECT_URI')"
                    :hint="Lang::txt('COM_DEVELOPER_API_APPLICATION_REDIRECT_URI_HINT')"
                    :required="true">
        <textarea name="application[redirect_uri]"
                  id="field-redirect_uri"
                  class="textarea textarea-bordered w-full"
                  rows="3"
                  required>{{ e($uris) }}</textarea>
      </x-form-field>
    </x-form-section>

    {{-- Team --}}
    <x-form-section :heading="Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM')">
      @if (!$isNew)
        <x-form-field :label="Lang::txt('COM_DEVELOPER_API_APPLICATION_CURRENT_TEAM')">
          @php $team = $application->team()->rows(); @endphp
          {!! $__view->view('_team')
                ->set('members', $team)
                ->loadTemplate() !!}
        </x-form-field>
      @else
        <div class="alert alert-info mb-4">
          {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_DONT_ADD_YOURSELF') }}
        </div>
      @endif

      <x-form-field name="team"
                    inputId="acmembers"
                    :label="Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_ADD')"
                    :hint="Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_ADD_HINT')">
        @php
        $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'team', 'acmembers']]);
        @endphp
        @if (count($mc) > 0)
          {!! $mc[0] !!}
        @else
          <input type="text"
                 name="team"
                 id="acmembers"
                 class="input input-bordered w-full"
                 value="" />
        @endif
      </x-form-field>
    </x-form-section>

    {{-- Submit --}}
    <div class="flex gap-2">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('COM_DEVELOPER_SAVE') }}
      </button>
      <a class="btn btn-ghost" href="{{ $return }}">
        {{ Lang::txt('JCANCEL') }}
      </a>
    </div>

    <input type="hidden" name="option" value="com_developer" />
    <input type="hidden" name="controller" value="applications" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="application[id]" value="{{ $application->get('id') }}" />
    {!! Html::input('token') !!}
  </form>

</x-page-container>
