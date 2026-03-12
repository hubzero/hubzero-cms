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

$base = 'index.php?option=' . $option . '&controller=' . $controller . '&gid=' . $group->cn;

Toolbar::title($group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_MODULES'), 'groups');

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_GROUPS_MODULES_CONFIRM_DELETE', 'delete');
}
Toolbar::spacer();
Toolbar::custom('manage', 'config', 'config', 'COM_GROUPS_MANAGE', false);

$__view->css();

@endphp

@php require_once dirname(__DIR__, 2) . '/pages/tmpl/menu.php'; @endphp

@if ($needsAttention->count() > 0)
    <table class="adminlist attention">
        <thead>
            <tr>
                <th scope="col">({{ $needsAttention->count() }}) {{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION') }}</th>
                <th scope="col">{{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_VIEW') }}</th>
                <th scope="col">{{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_CHECKS') }}</th>
                <th scope="col">{{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($needsAttention as $item)
                <tr>
                    <td>
                        {{ $item->get('title') }}
                    </td>
                    <td>
                        <ol class="attention-view">
                            <li class="raw">
                                <a class="version" href="{{ Route::url($base . '&task=raw&moduleid=' . $item->get('id'), false) }}" class="btn">
                                    {{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_VIEW_RAW') }}
                                </a>
                            </li>
                            @if ($item->get('checked_errors') && $item->get('scanned'))
                                <li class="preview">
                                    <a class="preview" href="{{ Route::url($base . '&task=preview&moduleid=' . $item->get('id'), false) }}" class="btn">
                                        {{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_RENDER_PREVIEW') }}
                                    </a>
                                </li>
                            @else
                                <li class="preview">
                                    {{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_RENDER_PREVIEW_HINT') }}
                                </li>
                            @endif
                            <li class="edit">
                                <a href="{{ Route::url($base . '&task=edit&id[]=' . $item->get('id'), false) }}" class="btn">
                                    {{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_EDIT') }}
                                </a>
                            </li>
                        </ol>
                    </td>
                    <td>
                        <ol class="attention-actions">
                            <li class="{{ $item->get('checked_errors') ? 'completed' : '' }}">
                                <a href="{{ Route::url($base . '&task=errors&id=' . $item->get('id'), false) }}" class="btn">
                                    {{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_CHECK_FOR_ERRORS') }}
                                </a>
                            </li>
                            <li class="{{ $item->get('scanned') ? 'completed' : '' }}">
                                <a href="{{ Route::url($base . '&task=scan&id=' . $item->get('id'), false) }}" class="btn">
                                    {{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_SCAN_CONTENT') }}
                                </a>
                            </li>
                        </ol>
                    </td>
                    <td>
                        <ol class="attention-actions">
                            @if ($item->get('checked_errors') && $item->get('scanned'))
                                <li class="approve">
                                    <a href="{{ Route::url($base . '&task=approve&id=' . $item->get('id'), false) }}" class="btn">
                                        <strong>{{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE') }}</strong>
                                    </a>
                                </li>
                            @else
                                <span><em>{{ Lang::txt('COM_GROUPS_MODULES_NEEDING_ATTENTION_APPROVE_HINT') }}</em></span>
                            @endif
                        </ol>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br />
@endif

@php
$formUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&gid=' . $group->cn, false);
@endphp
<form action="{{ $formUrl }}" name="adminForm" id="adminForm" method="post">
    <table class="admin-table">
        <thead>
            <tr>
                <th>
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        data-check-all />
                    <label for="checkall-toggle" class="sr-only">{{ Lang::txt('JGLOBAL_CHECK_ALL') }}</label>
                </th>
                <th scope="col">{{ Lang::txt('COM_GROUPS_MODULES_TITLE') }}</th>
                <th scope="col">{{ Lang::txt('COM_GROUPS_MODULES_STATUS') }}</th>
                <th scope="col">{{ Lang::txt('COM_GROUPS_MODULES_POSITION') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($modules->count() > 0)
                @foreach ($modules as $k => $module)
                    @php
                        $editUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=edit&gid=' . $group->cn
                            . '&id=' . $module->get('id'), false
                        );
                        $menus      = $module->menu('list');
                        $modulePages = [];
                        foreach ($menus as $menu) {
                            $modulePages[] = $menu->getPageTitle();
                        }
                    @endphp
                    <tr>
                        <td>
                            <input
                                type="checkbox"
                                name="id[]"
                                id="cb{{ $k }}"
                                value="{{ $module->get('id') }}"
                                class="checkbox checkbox-sm"
                                aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $module->get('title')) }}"
                                data-check-item />
                        </td>
                        <td>
                            <a href="{{ $editUrl }}">{{ $module->get('title') }}</a><br />
                            <span class="hint">
                                {!! Lang::txt('COM_GROUPS_MODULES_INCLUDED_ON', implode(', ', $modulePages)) !!}
                            </span>
                        </td>
                        <td>
                            @php
                                switch ($module->get('state')) {
                                    case 0:
                                        echo '<span class="badge badge-sm badge-warning">'
                                            . Lang::txt('COM_GROUPS_MODULES_STATUS_UNPUBLISHED')
                                            . '</span>';
                                        break;
                                    case 1:
                                        echo '<span class="badge badge-sm badge-success">'
                                            . Lang::txt('COM_GROUPS_MODULES_STATUS_PUBLISHED')
                                            . '</span>';
                                        break;
                                    case 2:
                                        echo '<span class="badge badge-sm badge-error">'
                                            . Lang::txt('COM_GROUPS_MODULES_STATUS_DELETED')
                                            . '</span>';
                                        break;
                                }
                            @endphp
                        </td>
                        <td>{{ $module->get('position') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">{{ Lang::txt('COM_GROUPS_MODULES_NO_MODULES') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    {!! Html::input('token') !!}
</form>
