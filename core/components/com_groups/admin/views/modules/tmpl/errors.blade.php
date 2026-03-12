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

$__view->css();

Toolbar::title(
    $group->get('description') . ': ' . $module->get('title') . ' - ' . Lang::txt('COM_GROUPS_MODULES_ERRORS'),
    'groups'
);
Toolbar::custom('errorscheckagain', 'check', 'check', 'COM_GROUPS_MODULES_CHECK_AGAIN', false);
Toolbar::cancel();

$content = $module->get('content');
$formUrl = Route::url(
    'index.php?option=' . $option . '&controller=' . $controller . '&gid=' . $group->cn, false
);
@endphp

<form action="{{ $formUrl }}" method="post" name="adminForm" id="item-form">

    <p class="error">
        {!! Lang::txt('COM_GROUPS_MODULES_ERROR_LIST', $module->get('title'), $error) !!}
    </p>

    <h3>{{ Lang::txt('COM_GROUPS_MODULES_RAW_CODE') }}</h3>
    <div class="code">
        @php
            $lines    = explode("\n", $content);
            $lineCode = '';
            for ($i = 1; $i <= count($lines); $i++) {
                $lineCode .= '&nbsp;' . $i . '&nbsp;<br>';
            }
        @endphp
        <table>
            <tr>
                <td class="lines">{!! $lineCode !!}</td>
                <td class="code">
                    @php highlight_string($content); @endphp
                </td>
            </tr>
        </table>
    </div>

    <h3>{{ Lang::txt('COM_GROUPS_MODULES_UPDATE_CONTENT') }}</h3>
    <textarea name="module[content]" rows="40">{{ $content }}</textarea>

    <input type="hidden" name="module[id]" value="{{ $module->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="gid" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="task" value="save" />
    {!! Html::input('token') !!}
</form>
