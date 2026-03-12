{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_IMPORT_TITLE_RUN'), 'import');

$__view->css('import');
$__view->js('import');
$__view->js('handlebars', 'system');

$importsUrl = Route::url('index.php?option=' . $option . '&controller=imports', false);
$hooksUrl   = Route::url('index.php?option=' . $option . '&controller=importhooks', false);

$progressUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=progress&id=' . $import->get('id'), false
);

$formUrl = Route::url(
    'index.php?option=' . $option . '&controller=' . $controller . '&task=dorun', false
);

// Handlebars lang strings
$hb_contains_errors   = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_CONTAINSERRORS'));
$hb_contains_notices  = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_CONTAINSNOTICES'));
$hb_error_msg         = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_ERRORMESSAGE'));
$hb_notice_msg        = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_NOTICEMESSAGE'));
$hb_members           = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_MEMBERS'));
$hb_managers          = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_MANAGERS'));
$hb_tags              = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_TAGS'));
$hb_projects          = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_PROJECTS'));
$hb_unused            = e(Lang::txt('COM_GROUPS_IMPORT_RUN_RESULT_UNUSED'));
$hb_none              = e(Lang::txt('COM_GROUPS_NONE'));
@endphp

<nav role="navigation" class="sub sub-navigation">
    <ul>
        <li>
            <a{{ $controller == 'imports' ? ' class="active"' : '' }} href="{{ $importsUrl }}">
                {{ Lang::txt('COM_GROUPS_IMPORT_TITLE_IMPORTS') }}
            </a>
        </li>
        <li>
            <a{{ $controller == 'importhooks' ? ' class="active"' : '' }} href="{{ $hooksUrl }}">
                {{ Lang::txt('COM_GROUPS_IMPORT_HOOKS') }}
            </a>
        </li>
    </ul>
</nav>

@foreach ($__view->getErrors() as $error)
    <p class="error">{{ $error }}</p>
@endforeach

<form action="{{ $formUrl }}" method="post" name="adminForm" id="adminForm">

    <div class="import-results">

        @if ($dryRun)
            <div class="dryrun-message">
                <strong>{{ Lang::txt('COM_GROUPS_IMPORT_RUN_NOTICE') }}</strong>
                <p>{{ Lang::txt('COM_GROUPS_IMPORT_RUN_NOTICE_DESC') }}</p>
            </div>
        @endif

        <div class="countdown" data-timeout="5">
            {!! Lang::txt('COM_GROUPS_IMPORT_RUN_START', '<span>5</span>') !!}
        </div>

        <div class="countdown-actions" data-progress="{{ $progressUrl }}">
            <button type="button" class="btn btn-primary start">
                {{ Lang::txt('COM_GROUPS_IMPORT_RUN_BUTTON_START') }}
            </button>
            <button type="button" class="btn stop">
                {{ Lang::txt('COM_GROUPS_IMPORT_RUN_BUTTON_STOP') }}
            </button>
            <button type="button" class="btn start-over">
                {{ Lang::txt('COM_GROUPS_IMPORT_RUN_BUTTON_RERUN') }}
            </button>
            @if ($dryRun)
                <button type="button" class="btn btn-secondary start-real">
                    {{ Lang::txt('COM_GROUPS_IMPORT_RUN_BUTTON_REAL') }}
                </button>
            @endif
        </div>

        <hr />

        <strong>{{ Lang::txt('COM_GROUPS_IMPORT_RUN_PROGRESS') }}<span class="progress-percentage">0%</span></strong>
        <div class="progress"></div>

        <hr />

        <strong>{{ Lang::txt('COM_GROUPS_IMPORT_RUN_RESULTS') }}<span class="results-stats"></span></strong>
        <div class="results">
            <span class="hint">{{ Lang::txt('COM_GROUPS_IMPORT_RUN_RESULTS_WAITING') }}</span>
        </div>

        <template id="entry-template">
            <h3 class="resource-title">
                @{{#if record.errors}}<span class="has-errors">{{ $hb_contains_errors }}</span>@{{/if}}
                @{{#if record.notices}}<span class="has-notices">{{ $hb_contains_notices }}</span>@{{/if}}
                @{{{ record.entry.description }}}
            </h3>

            <div class="resource-data">
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_24rem] gap-6">
                    @{{#if record.errors}}
                        <div class="errors">
                            <strong>{{ $hb_error_msg }}</strong>
                            <ol>
                                @{{#each record.errors}}
                                    <li>@{{this}}</li>
                                @{{/each}}
                            </ol>
                        </div>
                    @{{/if}}

                    @{{#if record.notices}}
                        <div class="notices">
                            <strong>{{ $hb_notice_msg }}</strong>
                            <ol>
                                @{{#each record.notices}}
                                    <li>@{{{this}}}</li>
                                @{{/each}}
                            </ol>
                        </div>
                    @{{/if}}

                    <div>
                        @{{{entry_data record}}}
                    </div>
                    <div>
                        <h4>{{ $hb_members }}</h4>
                        <ul>
                            @{{#each record.members}}
                                <li>@{{{ this }}}</li>
                            @{{else}}
                                <li><span class="hint">{{ $hb_none }}</span></li>
                            @{{/each}}
                        </ul>

                        <hr />

                        <h4>{{ $hb_managers }}</h4>
                        <ul>
                            @{{#each record.managers}}
                                <li>@{{{ this }}}</li>
                            @{{else}}
                                <li><span class="hint">{{ $hb_none }}</span></li>
                            @{{/each}}
                        </ul>

                        <hr />

                        <h4>{{ $hb_tags }}</h4>
                        <ul>
                            @{{#each record.tags}}
                                <li>@{{{ this }}}</li>
                            @{{else}}
                                <li><span class="hint">{{ $hb_none }}</span></li>
                            @{{/each}}
                        </ul>

                        <hr />

                        <h4>{{ $hb_projects }}</h4>
                        <ul>
                            @{{#each record.projects}}
                                <li>@{{{ this }}}</li>
                            @{{else}}
                                <li><span class="hint">{{ $hb_none }}</span></li>
                            @{{/each}}
                        </ul>
                    </div>
                    <br class="clr" />
                    <hr />

                    <div class="unused-data">
                        <h4>{{ $hb_unused }}</h4>
                        <pre>@{{print_json_data raw._unused}}</pre>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="dorun" />
    <input type="hidden" name="id" value="{{ $import->get('id') }}" />
    <input type="hidden" name="dryrun" value="{{ $dryRun ? 1 : 0 }}" />
    {!! Html::input('token') !!}
</form>
