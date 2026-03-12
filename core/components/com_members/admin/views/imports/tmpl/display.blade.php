{{--
  Member Imports — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $__view->css('import');
@endphp

@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_IMPORT_TITLE_IMPORTS'), 'import');

  if ($canDo->get('core.admin')) {
      Toolbar::custom('sample', 'sample', 'sample', 'COM_MEMBERS_IMPORT_SAMPLE', false);
      Toolbar::spacer();
      Toolbar::custom('run', 'script', 'script', 'COM_MEMBERS_RUN');
      Toolbar::custom('runtest', 'runtest', 'script', 'COM_MEMBERS_TEST_RUN');
      Toolbar::spacer();
      Toolbar::addNew();
      Toolbar::editList();
      Toolbar::deleteList();
  }

  Toolbar::spacer();
  Toolbar::help('import');
@endphp

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
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_NAME') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_NUMRECORDS') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_CREATED') }}</th>
          <th>{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_LASTRUN') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_FIELD_RUNCOUNT') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $imports->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @if($imports->count() > 0)
          @foreach($imports as $i => $import)
            @php
              $lastRun = $import->runs()
                  ->whereEquals('import_id', $import->get('id'))
                  ->whereEquals('dry_run', 0)
                  ->ordered()
                  ->row();

              $runCount = $import->runs()
                  ->whereEquals('import_id', $import->get('id'))
                  ->whereEquals('dry_run', 0)
                  ->total();
            @endphp
            <tr>
              <td class="column-check">
                @if($canDo->get('core.admin'))
                  <input type="checkbox"
                         name="id[]"
                         id="cb{{ $i }}"
                         value="{{ $import->get('id') }}"
                         class="checkbox checkbox-sm"
                         data-check-item />
                @endif
              </td>
              <td>
                @if($canDo->get('core.admin'))
                  @php
                    $editUrl = Route::url(
                        'index.php?option=' . $option . '&controller=' . $controller
                        . '&task=edit&id=' . $import->get('id'), false
                    );
                  @endphp
                  <a href="{!! $editUrl !!}"
                     class="link link-hover text-primary font-medium">
                    {{ $import->get('name') }}
                  </a>
                @else
                  {{ $import->get('name') }}
                @endif
                <br />
                <span class="hint">
                  {!! nl2br(e($import->get('notes'))) !!}
                </span>
              </td>
              <td class="priority-4">
                {{ $import->get('count', 0) }}
              </td>
              <td class="priority-3">
                <strong>{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_ON') }}</strong>
                <time datetime="{{ $import->get('created_at') }}">
                  {{ Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a') }}
                </time><br />
                <strong>{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_BY') }}</strong>
                @php
                  $createdBy = User::getInstance($import->get('created_by'));
                @endphp
                @if($createdBy)
                  {{ $createdBy->get('name') }}
                @endif
              </td>
              <td>
                @if($lastRun->get('id'))
                  <strong>{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_ON') }}</strong>
                  <time datetime="{{ $lastRun->get('ran_at') }}">
                    {{ Date::of($lastRun->get('ran_at'))->toLocal('m/d/Y @ g:i a') }}
                  </time><br />
                  <strong>{{ Lang::txt('COM_MEMBERS_IMPORT_DISPLAY_BY') }}</strong>
                  @php
                    $ranBy = User::getInstance($lastRun->get('ran_by'));
                  @endphp
                  @if($ranBy)
                    {{ $ranBy->get('name') }}
                  @endif
                @else
                  n/a
                @endif
              </td>
              <td class="priority-4">
                {{ $runCount }}
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="6">{{ Lang::txt('COM_MEMBERS_IMPORT_NONE') }}</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</x-admin-form>
