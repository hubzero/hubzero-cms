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

$canDo = \Components\Groups\Helpers\Permissions::getActions('component');

Toolbar::title(
    Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_IMPORTHOOK_TITLE_HOOKS'),
    'import'
);

if ($canDo->get('core.admin')) {
    Toolbar::spacer();
    Toolbar::addNew();
    Toolbar::editList();
    Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::help('import');

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

<x-admin-form option="{{ $option }}" controller="{{ $controller }}">
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
                    <th scope="col" class="priority-3">
                        {{ Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FIELD_NAME') }}
                    </th>
                    <th scope="col" class="priority-2">
                        {{ Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FIELD_TYPE') }}
                    </th>
                    <th scope="col">
                        {{ Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FIELD_FILE') }}
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
                @if ($hooks->count() > 0)
                    @foreach ($hooks as $i => $hook)
                        @php
                        $rawUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=raw&id=' . $hook->get('id'), false
                        );
                        @endphp
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="id[]"
                                    id="cb{{ $i }}"
                                    value="{{ $hook->get('id') }}"
                                    class="checkbox checkbox-sm"
                                    aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $hook->get('name')) }}"
                                    data-check-item />
                            </td>
                            <td class="priority-3">
                                {{ $hook->get('name') }}<br />
                                <span class="hint">{{ nl2br(e($hook->get('notes'))) }}</span>
                            </td>
                            <td class="priority-2">
                                @php
                                switch ($hook->get('event')) {
                                    case 'postconvert':
                                        echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_TYPE_POSTCONVERT');
                                        break;
                                    case 'postmap':
                                        echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_TYPE_POSTMAP');
                                        break;
                                    case 'postparse':
                                    default:
                                        echo Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_TYPE_POSTPARSE');
                                        break;
                                }
                                @endphp
                            </td>
                            <td>
                                {{ $hook->get('file') }} &mdash;
                                <a rel="noopener noreferrer" target="_blank" href="{{ $rawUrl }}">
                                    {{ Lang::txt('COM_GROUPS_IMPORTHOOK_DISPLAY_FILE_VIEWRAW') }}
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
</x-admin-form>
