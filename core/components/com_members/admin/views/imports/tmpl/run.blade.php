{{--
  Member Import — Run / Test UI

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_IMPORT_TITLE_RUN'),
      'import'
  );

  $__view->js('import')
         ->js('handlebars', 'system')
         ->css('import');

  $progressUrl = Route::url(
      'index.php?option=com_members&controller=import&task=progress&id='
      . $import->get('id'), false
  );
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

@foreach($__view->getErrors() as $error)
  <p class="error">{{ $error }}</p>
@endforeach

<form action="{!! Route::url('index.php?option=com_members&controller=import&task=dorun', false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  <fieldset class="adminform import-results">

    @if($dryRun)
      <div class="dryrun-message">
        <strong>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_NOTICE') }}</strong>
        <p>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_NOTICE_DESC') }}</p>
      </div>
    @endif

    <div class="countdown" data-timeout="5">
      {!! Lang::txt('COM_MEMBERS_IMPORT_RUN_START', '<span>5</span>') !!}
    </div>

    <div class="countdown-actions" data-progress="{{ $progressUrl }}">
      <button type="button" class="start btn btn-sm">
        {{ Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_START') }}
      </button>
      <button type="button" class="stop btn btn-sm">
        {{ Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_STOP') }}
      </button>
      <button type="button" class="start-over btn btn-sm">
        {{ Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_RERUN') }}
      </button>
      @if($dryRun)
        <button type="button" class="start-real btn btn-sm">
          {{ Lang::txt('COM_MEMBERS_IMPORT_RUN_BUTTON_REAL') }}
        </button>
      @endif
    </div>

    <hr />

    <strong>
      {{ Lang::txt('COM_MEMBERS_IMPORT_RUN_PROGRESS') }}
      <span class="progress-percentage">0%</span>
    </strong>
    <div class="progress"></div>

    <hr />

    <strong>
      {{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULTS') }}
      <span class="results-stats"></span>
    </strong>
    <div class="results">
      <span class="hint">{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULTS_WAITING') }}</span>
    </div>

    <template id="entry-template">
      <h3 class="resource-title">
        @{{#if record.errors}}<span class="has-errors">{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_CONTAINSERRORS') }}</span>@{{/if}}
        @{{#if record.notices}}<span class="has-notices">{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_CONTAINSNOTICES') }}</span>@{{/if}}
        @{{{ record.entry.name }}}
      </h3>

      <div class="resource-data">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
          @{{#if record.errors}}
            <div class="errors">
              <strong>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_ERRORMESSAGE') }}</strong>
              <ol>
                @{{#each record.errors}}
                  <li>@{{this}}</li>
                @{{/each}}
              </ol>
            </div>
          @{{/if}}

          @{{#if record.notices}}
            <div class="notices">
              <strong>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_NOTICEMESSAGE') }}</strong>
              <ol>
                @{{#each record.notices}}
                  <li>@{{{this}}}</li>
                @{{/each}}
              </ol>
            </div>
          @{{/if}}

          <div class="md:col-span-7">
            @{{{entry_data record}}}
          </div>
          <div class="md:col-span-5">

            <h4>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_DISABILITY') }}</h4>
            <ul>
              @{{#each record.entry.disability}}
                <li>@{{{ this }}}</li>
              @{{else}}
                <li><span class="hint">{{ Lang::txt('COM_MEMBERS_NONE') }}</span></li>
              @{{/each}}
            </ul>

            <hr />

            <h4>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_RACE') }}</h4>
            <ul>
              @{{#each record.entry.race}}
                <li>@{{{ this }}}</li>
              @{{else}}
                <li><span class="hint">{{ Lang::txt('COM_MEMBERS_NONE') }}</span></li>
              @{{/each}}
            </ul>

            <hr />

            <h4>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_TAGS') }}</h4>
            <ul>
              @{{#each record.tags}}
                <li>@{{{ this }}}</li>
              @{{else}}
                <li><span class="hint">{{ Lang::txt('COM_MEMBERS_NONE') }}</span></li>
              @{{/each}}
            </ul>

            <hr />

            <h4>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_GROUPS') }}</h4>
            <ul>
              @{{#each record.groups}}
                <li>@{{{ this }}}</li>
              @{{else}}
                <li><span class="hint">{{ Lang::txt('COM_MEMBERS_NONE') }}</span></li>
              @{{/each}}
            </ul>

          </div>
          <hr />

          <div class="unused-data">
            <h4>{{ Lang::txt('COM_MEMBERS_IMPORT_RUN_RESULT_UNUSED') }}</h4>
            <pre>@{{print_json_data raw._unused}}</pre>
          </div>
        </div>
      </div>
    </template>

  </fieldset>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="dorun" />
  <input type="hidden" name="id" value="{{ $import->get('id') }}" />
  <input type="hidden" name="dryrun" value="{{ $dryRun }}" />

  {!! Html::input('token') !!}
</form>
