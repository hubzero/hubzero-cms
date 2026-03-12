{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

$canDo = \Components\Groups\Helpers\Permissions::getActions('component');

Toolbar::title(
    Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_IMPORT_TITLE_IMPORTS'),
    'import'
);

if ($canDo->get('core.admin')) {
    Toolbar::custom('sample', 'sample', 'sample', 'COM_GROUPS_IMPORT_SAMPLE', false);
    Toolbar::spacer();
    Toolbar::custom('run', 'script', 'script', 'COM_GROUPS_RUN');
    Toolbar::custom('runtest', 'runtest', 'script', 'COM_GROUPS_TEST_RUN');
    Toolbar::spacer();
    Toolbar::addNew();
    Toolbar::editList();
    Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::help('import');

$__view->css('import');
@endphp

@php
$importsUrl = Route::url('index.php?option=' . $option . '&controller=imports', false);
$hooksUrl   = Route::url('index.php?option=' . $option . '&controller=importhooks', false);
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

<x-admin-form option="{{ $option }}" controller="{{ $controller }}" sort="{{ $filters['sort'] }}" sortDir="{{ $filters['sort_Dir'] }}">

    <table class="admin-table">
        <thead>
            <tr>
                <th scope="col">
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        data-check-all />
                    <label for="checkall-toggle" class="sr-only">
                        {{ Lang::txt('JGLOBAL_CHECK_ALL') }}
                    </label>
                </th>
                <th scope="col" class="priority-6">
                    {!! Html::grid('sort', 'ID', 'id', @$filters['sort_Dir'], @$filters['sort']) !!}
                </th>
                <th scope="col">
                    {!! Html::grid(
                        'sort',
                        'COM_GROUPS_IMPORT_DISPLAY_FIELD_NAME',
                        'name',
                        @$filters['sort_Dir'],
                        @$filters['sort']
                    ) !!}
                </th>
                <th scope="col" class="priority-4">
                    {{ Lang::txt('COM_GROUPS_IMPORT_DISPLAY_FIELD_NUMRECORDS') }}
                </th>
                <th scope="col" class="priority-3">
                    {!! Html::grid(
                        'sort',
                        'COM_GROUPS_IMPORT_DISPLAY_FIELD_CREATED',
                        'created_at',
                        @$filters['sort_Dir'],
                        @$filters['sort']
                    ) !!}
                </th>
                <th scope="col">
                    {!! Html::grid(
                        'sort',
                        'COM_GROUPS_IMPORT_DISPLAY_FIELD_LASTRUN',
                        'ran_at',
                        @$filters['sort_Dir'],
                        @$filters['sort']
                    ) !!}
                </th>
                <th scope="col" class="priority-4">
                    {{ Lang::txt('COM_GROUPS_IMPORT_DISPLAY_FIELD_RUNCOUNT') }}
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7">
                    {!! $imports->pagination !!}
                </td>
            </tr>
        </tfoot>
        <tbody>
            @if ($imports->count() > 0)
                @foreach ($imports as $i => $import)
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
                        <td>
                            @if ($canDo->get('core.admin'))
                                <input
                                    type="checkbox"
                                    name="id[]"
                                    id="cb{{ $i }}"
                                    value="{{ $import->get('id') }}"
                                    class="checkbox checkbox-sm"
                                    aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $import->get('name')) }}"
                                    data-check-item />
                            @endif
                        </td>
                        <td class="priority-6">
                            {{ $import->get('id') }}
                        </td>
                        <td>
                            @if ($canDo->get('core.admin'))
                                @php
                                $editUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=edit&id=' . $import->get('id'), false
                                );
                                @endphp
                                <a href="{{ $editUrl }}">{{ $import->get('name') }}</a>
                            @else
                                {{ $import->get('name') }}
                            @endif
                            <br />
                            <span class="hint">{{ nl2br(e($import->get('notes'))) }}</span>
                        </td>
                        <td class="priority-4">
                            {{ $import->get('count', 0) }}
                        </td>
                        <td class="priority-3">
                            <strong>{{ Lang::txt('COM_GROUPS_IMPORT_DISPLAY_ON') }}</strong>
                            <time datetime="{{ $import->get('created_at') }}">
                                {{ Date::of($import->get('created_at'))->toLocal('m/d/Y @ g:i a') }}
                            </time><br />
                            <strong>{{ Lang::txt('COM_GROUPS_IMPORT_DISPLAY_BY') }}</strong>
                            @php
                            $createdBy = User::getInstance($import->get('created_by'));
                            @endphp
                            @if ($createdBy)
                                {{ $createdBy->get('name') }}
                            @endif
                        </td>
                        <td>
                            @if ($lastRun->get('id'))
                                <strong>{{ Lang::txt('COM_GROUPS_IMPORT_DISPLAY_ON') }}</strong>
                                <time datetime="{{ $lastRun->get('ran_at') }}">
                                    {{ Date::of($lastRun->get('ran_at'))->toLocal('m/d/Y @ g:i a') }}
                                </time><br />
                                <strong>{{ Lang::txt('COM_GROUPS_IMPORT_DISPLAY_BY') }}</strong>
                                @php
                                $ranBy = User::getInstance($lastRun->get('ran_by'));
                                @endphp
                                @if ($ranBy)
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
                    <td colspan="7">{{ Lang::txt('COM_GROUPS_IMPORT_NONE') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

</x-admin-form>
