{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $tmpl = \Hubzero\Facades\Request::getString('tmpl', '');
    $no_html = \Hubzero\Facades\Request::getInt('no_html', 0);
@endphp

    <script type="application/json" id="conditions-data">
    {
        "conditions": {!! json_encode($conditions) !!}
    }
    </script>

@if (!$tmpl && !$no_html)
    @php
        $this->js('json2.js');
        $this->js('condition.builder.js');
        $this->css('conditions.css');
        $formAction = \Hubzero\Facades\Route::url('index.php?option=' . $option);
        $missingTitleErr = \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE');
        $rowTitle = $__view->escape(stripslashes($row->title));
        $rowConditions = $__view->escape(stripslashes($row->conditions));
        $noHtmlVal = ($tmpl) ? 1 : \Hubzero\Facades\Request::getInt('no_html', 0);
    @endphp
    <form
        action="{{ $formAction }}"
        method="post"
        name="adminForm"
        id="item-form"
    >
        <div class="col span12">
            <fieldset class="adminform">
                <legend>{{ \Hubzero\Facades\Lang::txt('JDETAILS') }}</legend>

                <table class="admintable">
                    <tbody>
                        <tr>
                            <td class="key">
                                <label for="field-iscore">
                                    {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_FIELD_TYPE') }}
                                </label>
                            </td>
                            <td colspan="2">
                                <select name="fields[iscore]" id="field-iscore">
                                    <optgroup label="{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON') }}">
                                        <option value="2"{{ $row->iscore == 2 ? ' selected="selected"' : '' }}>
                                            {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_ACL') }}
                                        </option>
                                        <option value="4"{{ $row->iscore == 4 ? ' selected="selected"' : '' }}>
                                            {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_NO_ACL') }}
                                        </option>
                                    </optgroup>
                                    <option value="1"{{ $row->iscore == 1 ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_TYPE_MINE') }}
                                    </option>
                                    <option value="0"{{ $row->iscore == 0 ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_TYPE_CUSTOM') }}
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <label for="field-title">
                                    {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_FIELD_TITLE') }}
                                </label>
                            </td>
                            <td colspan="2">
                                <input
                                    type="text"
                                    name="fields[title]"
                                    id="field-title"
                                    data-empty="{{ $missingTitleErr }}"
                                    value="{{ $rowTitle }}"
                                />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <fieldset class="query">
                                    @if ($row->conditions)
                                        @php($condition = json_decode($row->conditions))
                                        {!! $__view->view('condition')
                                            ->set('option', $option)
                                            ->set('controller', $controller)
                                            ->set('condition', $condition)
                                            ->set('conditions', $conditions)
                                            ->set('row', $row)
                                            ->display() !!}
                                    @endif
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <label for="field-sort">
                                    {{ \Hubzero\Facades\Lang::txt('Sort results by:') }}
                                </label>
                            </td>
                            <td>
                                <select name="fields[sort]" id="field-sort">
                                    <option value="open"{{ $row->sort == 'open' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN') }}
                                    </option>
                                    <option value="status"{{ $row->sort == 'status' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS') }}
                                    </option>
                                    <option value="login"{{ $row->sort == 'login' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER') }}
                                    </option>
                                    <option value="owner"{{ $row->sort == 'owner' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER') }}
                                    </option>
                                    <option value="group"{{ $row->sort == 'group' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('Group') }}
                                    </option>
                                    <option value="id"{{ $row->sort == 'id' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_ID') }}
                                    </option>
                                    <option value="report"{{ $row->sort == 'report' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT') }}
                                    </option>
                                    <option value="severity"{{ $row->sort == 'severity' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY') }}
                                    </option>
                                    <option value="tag"{{ $row->sort == 'tag' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_TAG') }}
                                    </option>
                                    <option value="type"{{ $row->sort == 'type' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE') }}
                                    </option>
                                    <option value="created"{{ $row->sort == 'created' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED') }}
                                    </option>
                                    <option value="closed"{{ $row->sort == 'closed' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED') }}
                                    </option>
                                    <option value="category"{{ $row->sort == 'category' ? ' selected="selected"' : '' }}>
                                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY') }}
                                    </option>
                                </select>
                            </td>
                            <td>
                                <select name="fields[sort_dir]" id="field-sort_dir">
                                    <option value="DESC"{{ strtolower($row->sort_dir) == 'desc' ? ' selected="selected"' : '' }}>desc</option>
                                    <option value="ASC"{{ strtolower($row->sort_dir) == 'asc' ? ' selected="selected"' : '' }}>asc</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
        <input
            type="hidden"
            name="fields[conditions]"
            id="field-conditions"
            value="{{ $rowConditions }}"
        />
        <input type="hidden" name="fields[user_id]" value="{{ \Hubzero\Facades\User::get('id') }}" />

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="no_html" value="{{ $noHtmlVal }}" />
        <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
        <input type="hidden" name="task" value="save" />

        {!! \Hubzero\Facades\Html::input('token') !!}
    </form>
@else
    @php
        if ($row->iscore != 0) {
            $row->title .= ' ' . \Hubzero\Facades\Lang::txt('(copy)');
        }
        $formAction2 = \Hubzero\Facades\Route::url('index.php?option=' . $option);
        $missingTitleErr = \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE');
        $rowTitle = $__view->escape(stripslashes($row->title));
        $rowConditions = $__view->escape(stripslashes($row->conditions));
        $rowIdVal = ($row->iscore == 0) ? $row->id : 0;
        $noHtmlVal2 = ($tmpl) ? 1 : \Hubzero\Facades\Request::getInt('no_html', 0);
    @endphp
    <form
        action="{{ $formAction2 }}"
        method="post"
        name="adminForm"
        id="queryForm"
    >
        <h3>
            <span class="configuration-options">
                <input type="submit" value="{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SAVE') }}" />
            </span>
            <span class="configuration">
                {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_BUILDER') }}
            </span>
        </h3>
        <fieldset class="wrapper">

        <fieldset class="fields title">
            <label for="field-title">{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_FIELD_TITLE') }}</label>
            <input
                type="text"
                name="fields[title]"
                id="field-title"
                data-empty="{{ $missingTitleErr }}"
                value="{{ $rowTitle }}"
            />
        </fieldset>

        <fieldset class="query">
            @if ($row->conditions)
                @php($condition = json_decode($row->conditions))
                {!! $__view->view('condition')
                    ->set('option', $option)
                    ->set('controller', $controller)
                    ->set('condition', $condition)
                    ->set('conditions', $conditions)
                    ->set('row', $row)
                    ->display() !!}
            @endif
        </fieldset>

        <fieldset class="fields sort">
            <p>
                <label for="field-folder_id">{{ \Hubzero\Facades\Lang::txt('In folder') }}</label>
                <select name="fields[folder_id]" id="field-folder_id">
                    @php
                        $folders = \Components\Support\Models\QueryFolder::all()
                            ->whereEquals('user_id', \Hubzero\Facades\User::get('id'))
                            ->order('ordering', 'ASC')
                            ->rows();
                    @endphp
                    @if ($folders)
                        @foreach ($folders as $folder)
                            <option
                                value="{{ $folder->id }}"
                                {{ $row->folder_id == $folder->id ? ' selected="selected"' : '' }}
                            >{{ $__view->escape(stripslashes($folder->title)) }}</option>
                        @endforeach
                    @endif
                </select>

                <label for="field-sort">{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_BY') }}</label>
                <select name="fields[sort]" id="field-sort">
                    <option value="open"{{ $row->sort == 'open' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN') }}
                    </option>
                    <option value="status"{{ $row->sort == 'status' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS') }}
                    </option>
                    <option value="login"{{ $row->sort == 'login' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER') }}
                    </option>
                    <option value="owner"{{ $row->sort == 'owner' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER') }}
                    </option>
                    <option value="group"{{ $row->sort == 'group' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('Group') }}
                    </option>
                    <option value="id"{{ $row->sort == 'id' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_ID') }}
                    </option>
                    <option value="report"{{ $row->sort == 'report' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT') }}
                    </option>
                    <option value="severity"{{ $row->sort == 'severity' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY') }}
                    </option>
                    <option value="tag"{{ $row->sort == 'tag' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_TAG') }}
                    </option>
                    <option value="type"{{ $row->sort == 'type' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE') }}
                    </option>
                    <option value="created"{{ $row->sort == 'created' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED') }}
                    </option>
                    <option value="closed"{{ $row->sort == 'closed' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED') }}
                    </option>
                    <option value="category"{{ $row->sort == 'category' ? ' selected="selected"' : '' }}>
                        {{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY') }}
                    </option>
                </select>
                <select name="fields[sort_dir]" id="field-sort_dir">
                    <option value="DESC"{{ strtolower($row->sort_dir) == 'desc' ? ' selected="selected"' : '' }}>desc</option>
                    <option value="ASC"{{ strtolower($row->sort_dir) == 'asc' ? ' selected="selected"' : '' }}>asc</option>
                </select>
            </p>
        </fieldset>

        <input type="hidden" name="fields[id]" value="{{ $rowIdVal }}" />
        <input
            type="hidden"
            name="fields[conditions]"
            id="field-conditions"
            value="{{ $rowConditions }}"
        />
        <input type="hidden" name="fields[user_id]" value="{{ \Hubzero\Facades\User::get('id') }}" />

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="no_html" value="{{ $noHtmlVal2 }}" />
        <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
        <input type="hidden" name="task" value="save" />

        {!! \Hubzero\Facades\Html::input('token') !!}
        </fieldset>
    </form>
@endif
