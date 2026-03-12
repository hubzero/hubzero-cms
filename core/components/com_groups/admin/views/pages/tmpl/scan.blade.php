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
    $group->get('description') . ': ' . $page->get('title') . ' - ' . Lang::txt('COM_GROUPS_PAGES_SCAN'),
    'groups'
);
Toolbar::custom('markscanned', 'check', 'check', 'COM_GROUPS_PAGES_MARK_SCANNED', false);
Toolbar::spacer();
Toolbar::custom('scanagain', 'check', 'check', 'COM_GROUPS_PAGES_SCAN_AGAIN', false);
Toolbar::cancel();


$__view->js();

$content = $page->version()->get('content');
$action  = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&gid=' . $group->cn
    . '&task=markscanned', false
);
@endphp

<form
    action="{{ $action }}"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-confirm="{{ Lang::txt('COM_GROUPS_PAGES_MARK_SCANNED_CONFIRM') }}"
    data-invalid-msg="{{ Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED') }}">

    @php
        unset($issues->count);
        $severe = $elevated = $minor = [];
        foreach ($issues as $lang => $languageIssues) {
            foreach ($languageIssues as $type => $languageIssue) {
                foreach ($languageIssue as $line => $issue) {
                    array_push($$type, 'Line ' . $line . '. ' . e($issue));
                }
            }
        }
    @endphp

    @if (count($severe) > 0)
        <p class="error">
            {!! Lang::txt('COM_GROUPS_PAGES_SCAN_SEVERE', implode('<br />', $severe)) !!}
        </p>
    @endif

    @if (count($elevated) > 0)
        <p class="warning">
            {!! Lang::txt('COM_GROUPS_PAGES_SCAN_ELEVATED', implode('<br />', $elevated)) !!}
        </p>
    @endif

    @if (count($minor) > 0)
        <p class="info">
            {!! Lang::txt('COM_GROUPS_PAGES_SCAN_MINOR', implode('<br />', $minor)) !!}
        </p>
    @endif

    <h3>{{ Lang::txt('COM_GROUPS_PAGES_VIEW_RAW_CODE') }}</h3>
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

    <h3>{{ Lang::txt('COM_GROUPS_PAGES_UPDATE_CONTENT') }}</h3>
    <textarea name="page[content]" rows="40">{{ $content }}</textarea>

    <input type="hidden" name="page[id]" value="{{ $page->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="gid" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="task" value="save" />
    {!! Html::input('token') !!}
</form>
