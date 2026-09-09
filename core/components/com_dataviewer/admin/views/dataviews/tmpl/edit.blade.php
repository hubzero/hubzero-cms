@php
/**
 * Data definition editor view (admin).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$dvLink = '/' . urlencode($comName) . '/view/'
    . urlencode($dbId) . ':db/' . urlencode($ddName) . '/';
$backLink = '/administrator/index.php?option=com_dataviewer&controller=dataviews&task=display&db='
    . urlencode($dbId);
$formAction = '/administrator/index.php?option=com_dataviewer&controller=dataviews&task=save';
$editorHeight = $fullScreen ? 600 : 500;
@endphp

<div id="dv-admin-config"
    data-com-name="{{ $comName }}"
    data-back-link="{{ $backLink }}"
    class="hidden">
</div>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{{ Lang::txt('COM_DATAVIEWER_TAB_DATAVIEW') }}</a></li>
        <li><a href="#tabs-2">{{ Lang::txt('COM_DATAVIEWER_TAB_EDITOR') }}</a></li>
        <li><a href="#tabs-3">{{ Lang::txt('COM_DATAVIEWER_TAB_JSON') }}</a></li>
    </ul>

    <div id="tabs-1">
        <iframe seamless
            src="{{ $dvLink }}?tmpl=component"
            id="db-tables-dv-iframe"
            style="width: 100%; min-width: 1000px;" height="650px;"
            marginWidth="0" marginHeight="0" frameBorder="0"
            scrolling="auto">
            {{ Lang::txt('COM_DATAVIEWER_IFRAMES_NOT_SUPPORTED') }}</iframe>
    </div>

    <div id="tabs-2">
        <textarea id="db-dd-source-php" style="display: none;">{{ $ddPhp }}</textarea>
        <div id="db-dd-editor-php"
            style="height: {{ (int)$editorHeight }}px;
                width: 100%;"></div>
        <input id="db-dd-update" type="button" value="{{ Lang::txt('COM_DATAVIEWER_UPDATE_DATAVIEW') }}"
            style="color: blue; position: absolute;
                top: 60px; right: 60px;" />
        <br />
    </div>

    <div id="tabs-3">
        <div id="db-dd-editor"
            style="height: {{ (int)$editorHeight }}px;
                width: 100%;">
            {{ $ddJson }}
        </div>
    </div>

    <div style="position: absolute; top: 5px; right: 10px;">
        @if ($fullScreen)
            @php
            $closeLink = '/administrator/index.php?option=com_dataviewer&controller=dataviews&task=edit&db='
                . urlencode($dbId) . '&dd=' . urlencode($ddName);
            @endphp
            [<a href="{{ $backLink }}"
                title="{{ Lang::txt('COM_DATAVIEWER_GO_BACK_LIST') }}">
                &nbsp;<span class="ui-icon ui-icon-arrowthick-1-w"
                    style="display: inline-block;
                        margin-bottom: -4px;"></span>&nbsp;
            </a>] &nbsp;
            [<a href="{{ $closeLink }}"
                title="{{ Lang::txt('COM_DATAVIEWER_LEAVE_FULLSCREEN') }}">
                &nbsp;<span class="ui-icon ui-icon-close"
                    style="display: inline-block;
                        margin-bottom: -4px;"></span>&nbsp;
            </a>]
        @else
            @php
            $fsLink = '/administrator/index.php?option=com_dataviewer&controller=dataviews&task=edit&db='
                . urlencode($dbId) . '&dd=' . urlencode($ddName) . '&tmpl=component';
            @endphp
            [<a href="{{ $fsLink }}"
                title="{{ Lang::txt('COM_DATAVIEWER_SWITCH_FULLSCREEN') }}">
                &nbsp;<span class="ui-icon ui-icon-arrow-4-diag"
                    style="display: inline-block;
                        margin-bottom: -4px;"></span>&nbsp;
            </a>]
        @endif
    </div>
</div>

<form id="db-dd-update-form" method="post"
    action="{{ $formAction }}">
    <input name="{{ $csrfToken }}" type="hidden" value="1" />
    <input name="db" type="hidden" value="{{ $dbId }}" />
    <input name="dd" type="hidden" value="{{ $ddName }}" />
    <input name="update" type="hidden" value="true" />
    <input name="dd_text" type="hidden" value="" />
</form>
