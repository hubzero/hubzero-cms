{{--
  Resource Import — Run/dry-run execution UI

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->js('handlebars.js', 'system')
      ->js('import.blade.js');
  $__view->css('import');

  $actionUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=dorun', false
  );
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES_IMPORT_TITLE_RUN') }}"
    icon="script"
/>

<form action="{{ $actionUrl }}" method="post" name="adminForm" id="adminForm">

  <x-admin-fieldset>
      @if($dryRun)
        <div class="alert alert-warning mb-4">
          <strong>{{ Lang::txt('COM_RESOURCES_IMPORT_RUN_NOTICE') }}</strong>
          <p>{{ Lang::txt('COM_RESOURCES_IMPORT_RUN_NOTICE_DESC') }}</p>
        </div>
      @endif

      <div class="countdown" data-timeout="5">
        {!! Lang::txt('COM_RESOURCES_IMPORT_RUN_START', '<span>5</span>') !!}
      </div>

      <div class="countdown-actions flex gap-2 my-4">
        <button type="button" class="start btn btn-sm btn-primary">
          {{ Lang::txt('COM_RESOURCES_IMPORT_RUN_BUTTON_START') }}
        </button>
        <button type="button" class="stop btn btn-sm btn-error">
          {{ Lang::txt('COM_RESOURCES_IMPORT_RUN_BUTTON_STOP') }}
        </button>
        <button type="button" class="start-over btn btn-sm btn-ghost">
          {{ Lang::txt('COM_RESOURCES_IMPORT_RUN_BUTTON_RERUN') }}
        </button>
        @if($dryRun)
          <button type="button" class="start-real btn btn-sm btn-success">
            {{ Lang::txt('COM_RESOURCES_IMPORT_RUN_BUTTON_REAL') }}
          </button>
        @endif
      </div>

      <div class="divider"></div>

      <strong>
        {{ Lang::txt('COM_RESOURCES_IMPORT_RUN_PROGRESS') }}
        <span class="progress-percentage">0%</span>
      </strong>
      <div class="progress"></div>

      <div class="divider"></div>

      <strong>
        {{ Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULTS') }}
        <span class="results-stats"></span>
      </strong>
      <div class="results">
        <span class="text-muted-foreground">
          {{ Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULTS_WAITING') }}
        </span>
      </div>

      {{-- Handlebars template — must be preserved as-is --}}
      <template id="resource-template">
          <h3 class="resource-title">
              @{{#if record.errors}}<span class="has-errors"><?php
                  echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_CONTAINSERRORS');
              ?></span>@{{/if}}
              @{{#if record.notices}}<span class="has-notices"><?php
                  echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_CONTAINSNOTICES');
              ?></span>@{{/if}}
              @{{{ record.resource.title }}}
          </h3>

          <div class="resource-data">
              <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                  @{{#if record.errors}}
                      <div class="col-span-full">
                          <div class="errors">
                              <strong><?php
                                  echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_ERRORMESSAGE');
                              ?></strong>
                              <ol>
                                  @{{#each record.errors}}
                                      <li>@{{this}}</li>
                                  @{{/each}}
                              </ol>
                          </div>
                      </div>
                  @{{/if}}

                  @{{#if record.notices}}
                      <div class="col-span-full">
                          <div class="notices">
                              <strong><?php
                                  echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_NOTICEMESSAGE');
                              ?></strong>
                              <ol>
                                  @{{#each record.notices}}
                                      <li>@{{{this}}}</li>
                                  @{{/each}}
                              </ol>
                          </div>
                      </div>
                  @{{/if}}

                  <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                      <div class="md:col-span-7">
                          @{{{resource_data record}}}
                      </div>
                      <div class="md:col-span-5">
                          <h4><?php
                              echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_CHILDREN');
                          ?></h4>
                          @{{{child_resource_data record.children}}}
                          <hr />

                          <h4><?php
                              echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_CONTRIBUTORS');
                          ?></h4>
                          <table>
                              @{{#each raw.contributors}}
                                  <tr>
                                      <td>
                                          <span class="contributor-name">@{{{ name }}}</span>
                                          <span class="contributor-org">@{{{ organization }}}</span>
                                      </td>
                                      <td>
                                          <span class="contributor-role">
                                              @{{#if role}}
                                                  @{{{ucfirst role }}}
                                              @{{else}}
                                                  Author
                                              @{{/if}}
                                          </span>
                                      </td>
                                  </tr>
                              @{{/each}}
                          </table>

                          <hr />

                          <h4><?php echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_TAGS'); ?></h4>
                          <table>
                              <tr>
                                  <td>
                                      @{{#each record.tags}}
                                          @{{{ this }}}<br />
                                      @{{else}}
                                          <span class="hint">No Tags</span>
                                      @{{/each}}
                                  </td>
                              </tr>
                          </table>

                          <hr />

                          <h4><?php echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_CUSTOM'); ?></h4>
                          <table>
                              @{{#each record.custom}}
                                  <tr>
                                      <th width="25%">@{{{ ucfirst @key }}}</th>
                                      <td>@{{{ this }}}</td>
                                  </tr>
                              @{{/each}}
                          </table>
                      </div>
                  </div>
                  <hr />

                  <div class="unused-data">
                      <h4><?php echo Lang::txt('COM_RESOURCES_IMPORT_RUN_RESULT_UNUSED'); ?></h4>
                      <pre>@{{print_json_data raw._unused}}</pre>
                  </div>
              </div>
          </div>
      </template>
  </x-admin-fieldset>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="dorun" />
  <input type="hidden" name="id" value="{{ $import->get('id') }}" />
  <input type="hidden" name="dryrun" value="{{ $dryRun }}" />

  {!! Html::input('token') !!}
</form>
