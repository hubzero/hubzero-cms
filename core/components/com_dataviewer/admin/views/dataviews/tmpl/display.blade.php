@php
/**
 * Dataview list view (admin).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$backLink = '/administrator/index.php?option=com_databases';
@endphp

<div id="dv-admin-config"
    data-com-name="{{ $comName }}"
    data-back-link="{{ $backLink }}"
    class="hidden">
</div>

<style type="text/css"> .toolbar-box .header:before {content: " ";}</style>

<table class="adminlist">
    <thead>
        <tr>
            <th>#</th>
            <th width="55%">{{ Lang::txt('COM_DATAVIEWER_TITLE') }}</th>
            <th>{{ Lang::txt('COM_DATAVIEWER_REMOVE') }}</th>
            <th>{{ Lang::txt('COM_DATAVIEWER_LAST_UPDATED') }}</th>
            <th>{{ Lang::txt('COM_DATAVIEWER_DATA_VIEW') }}</th>
            <th>{{ Lang::txt('COM_DATAVIEWER_DATA_DEFINITION') }}</th>
        </tr>
    </thead>
    <tbody>
    @if (count($definitions) < 1)
        <tr>
            <td colspan="6">
                <h2>{{ Lang::txt('COM_DATAVIEWER_NO_DATAVIEWS') }}</h2>
            </td>
        </tr>
    @else
        @foreach ($definitions as $i => $def)
            @php
            $viewLink = '/' . $comName . '/view/' . $dbId . ':db/' . $def['name'] . '/';
            $editLink = '/administrator/index.php?option=com_dataviewer&controller=dataviews&task=edit&db='
                . urlencode($dbId) . '&dd=' . urlencode($def['name']);
            $fsLink = '/administrator/index.php?option=com_dataviewer&tmpl=component&controller=dataviews&task=edit&db='
                . urlencode($dbId) . '&dd=' . urlencode($def['name']);
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $def['title'] }} &nbsp;<small>[{{ $def['name'] }}]</small></td>
                <td>
                    <a class="db-dd-remove-link" style="color: red;"
                        data-dd="{{ $def['name'] }}" href="#">
                        {{ Lang::txt('COM_DATAVIEWER_REMOVE') }}
                    </a>
                </td>
                <td>{{ $def['lastMod'] }}</td>
                <td align="center">
                    <a target="_blank" href="{{ $viewLink }}">
                        {{ Lang::txt('COM_DATAVIEWER_VIEW') }}
                    </a>
                </td>
                <td>
                    <a href="{{ $editLink }}">{{ Lang::txt('COM_DATAVIEWER_EDIT') }} &nbsp; </a>
                    &nbsp;[<a target="_blank" href="{{ $fsLink }}">{{ Lang::txt('COM_DATAVIEWER_FULL_SCREEN') }}</a>]
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

@if ($dbError)
    <h3>{{ Lang::txt('COM_DATAVIEWER_INVALID_DB') }}</h3>
@else
    {{-- Remove form --}}
    <form id="db-dd-remove-frm" method="post"
        action="/administrator/index.php?option=com_dataviewer&controller=dataviews&task=remove"
        style="display: none;">
        <input name="{{ $csrfToken }}" type="hidden" value="1" />
        <input name="db" type="hidden" value="{{ $dbId }}" />
        <input name="dd_name" type="hidden" />
    </form>

    {{-- New dataview dialog --}}
    <div id="db-dd-new" style="display: none;"
        title="{{ Lang::txt('COM_DATAVIEWER_ADD_DATAVIEW_TITLE', $dbConf['name']) }}">
        <form method="post"
            action="/administrator/index.php?option=com_dataviewer&controller=dataviews&task=create">
            <input name="{{ $csrfToken }}" type="hidden" value="1" />
            <input name="db" type="hidden" value="{{ $dbId }}" />
            <label for="table">{{ Lang::txt('COM_DATAVIEWER_SELECT_TABLE') }}</label>
            <br />
            <select name="table" id="table">
            @foreach ($tableList as $table)
                <option value="{{ $table['TABLE_NAME'] }}">{{ $table['TABLE_NAME'] }}</option>
            @endforeach
            </select>

            <br />
            <label for="name">{{ Lang::txt('COM_DATAVIEWER_FIELD_NAME') }}</label>
            <br />
            <input type="text" id="name" name="name" />

            <br />
            <label for="title">{{ Lang::txt('COM_DATAVIEWER_FIELD_TITLE') }}</label>
            <br />
            <input type="text" id="title" name="title" />

            <input type="submit" value="{{ Lang::txt('COM_DATAVIEWER_CREATE') }}" />
        </form>
    </div>
@endif
