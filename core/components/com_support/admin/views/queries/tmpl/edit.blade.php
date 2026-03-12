{{--
  Support — Query condition builder edit form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('conditions.css')
         ->js('condition.builder.blade.js')
         ->js('support.blade.js');

  $tmpl = Request::getString('tmpl', '');

  $text = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

  if (!$tmpl) {
      \Hubzero\Facades\Toolbar::title(
          Lang::txt('COM_SUPPORT_TICKET') . ': ' . Lang::txt('COM_SUPPORT_QUERIES') . ': ' . $text,
          'support'
      );
      \Hubzero\Facades\Toolbar::save();
      \Hubzero\Facades\Toolbar::cancel();
  }
@endphp

<template id="conditions-data">
{
    "conditions": {!! json_encode($conditions) !!}
}
</template>

@if (!$tmpl)
    @php
      $formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
      $titleVal   = e($row->title == null ? '' : $row->title);
      $condVal    = e($row->conditions == null ? '' : $row->conditions);
      $so         = $row->sort;
      $ss         = ' selected="selected"';
    @endphp
    <form action="{{ $formAction }}" method="post" name="adminForm" id="item-form">
        <fieldset class="adminform">
            <legend>{{ Lang::txt('JDETAILS') }}</legend>

            <table class="admintable">
                <tbody>
                    <tr>
                        <td class="key">
                            <label for="field-iscore">
                                {{ Lang::txt('COM_SUPPORT_FIELD_TYPE') }}
                            </label>
                        </td>
                        <td colspan="2">
                            <select name="fields[iscore]" id="field-iscore">
                                <optgroup label="{{ Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON') }}">
                                    <option value="2" @selected($row->iscore == 2)>
                                        {{ Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_ACL')}}
                                    </option>
                                    <option value="4" @selected($row->iscore == 4)>
                                        {{ Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_NO_ACL')}}
                                    </option>
                                </optgroup>
                                <option value="1" @selected($row->iscore == 1)>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_TYPE_MINE')}}
                                </option>
                                <option value="0" @selected($row->iscore == 0)>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_TYPE_CUSTOM')}}
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <label for="field-title">
                                {{ Lang::txt('COM_SUPPORT_FIELD_TITLE') }}
                            </label>
                        </td>
                        <td colspan="2">
                            <input
                                type="text"
                                name="fields[title]"
                                id="field-title"
                                value="{{ $titleVal }}"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <fieldset class="query">
                                @if ($row->conditions)
                                    @php
                                      $condition = json_decode($row->conditions);
                                    @endphp
                                    @include('com_support::admin/views/queries/tmpl/condition', [
                                        'option'     => $option,
                                        'controller' => $controller,
                                        'condition'  => $condition,
                                        'conditions' => $conditions,
                                        'row'        => $row,
                                    ])
                                @endif
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <label for="field-sort">
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_BY') }}
                            </label>
                        </td>
                        <td>
                            <select name="fields[sort]" id="field-sort">
                                <option value="open" @selected($so == 'open')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN')}}
                                </option>
                                <option value="status" @selected($so == 'status')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS')}}
                                </option>
                                <option value="login" @selected($so == 'login')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER')}}
                                </option>
                                <option value="owner" @selected($so == 'owner')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER')}}
                                </option>
                                <option value="group" @selected($so == 'group')>
                                    {{ Lang::txt('Group')}}
                                </option>
                                <option value="id" @selected($so == 'id')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_ID')}}
                                </option>
                                <option value="report" @selected($so == 'report')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT')}}
                                </option>
                                <option value="severity" @selected($so == 'severity')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY')}}
                                </option>
                                <option value="tag" @selected($so == 'tag')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_TAG')}}
                                </option>
                                <option value="type" @selected($so == 'type')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE')}}
                                </option>
                                <option value="created" @selected($so == 'created')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED')}}
                                </option>
                                <option value="closed" @selected($so == 'closed')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED')}}
                                </option>
                                <option value="category" @selected($so == 'category')>
                                    {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY')}}
                                </option>
                            </select>
                        </td>
                        <td>
                            <select name="fields[sort_dir]" id="field-sort_dir" aria-label="{{ Lang::txt('COM_SUPPORT_QUERY_SORT_DIR') }}">
                                <option value="DESC" @selected(strtolower($row->sort_dir) == 'desc')>desc</option>
                                <option value="ASC" @selected(strtolower($row->sort_dir) == 'asc')>asc</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>

        <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
        <input
            type="hidden"
            name="fields[conditions]"
            id="field-conditions"
            value="{{ $condVal }}"
        />
        <input type="hidden" name="fields[user_id]" value="{{ User::get('id') }}" />

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input
            type="hidden"
            name="no_html"
            value="{{ $tmpl ? 1 : Request::getInt('no_html', 0) }}"
        />
        <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
        <input type="hidden" name="task" value="save" />

        {!! Html::input('token') !!}
    </form>
@else
    @php
      if ($row->iscore != 0) {
          $row->title .= ' ' . Lang::txt('COM_SUPPORT_COPY');
      }
      $formAction2      = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
      $missingTitleMsg  = Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE');
      $actionUrl        = Route::url('index.php?option=' . $option, false);
      $titleVal2        = e($row->title == null ? '' : $row->title);
      $condVal2         = e($row->conditions == null ? '' : $row->conditions);
    @endphp
    <form
        action="{{ $formAction2 }}"
        method="post"
        name="adminForm"
        id="component-form"
    >
        <fieldset>
            <div class="configuration">
                <div class="configuration-options">
                    <button
                        type="button"
                        id="btn-apply"
                        data-invalid="{{ $missingTitleMsg }}"
                        data-action="{{ $actionUrl }}"
                    >{{ Lang::txt('JAPPLY') }}</button>
                    <button type="button" id="btn-cancel">{{ Lang::txt('JCANCEL') }}</button>
                </div>

                {{ Lang::txt('COM_SUPPORT_QUERY_BUILDER') }}
            </div>
        </fieldset>

        <fieldset class="fields title">
            <label for="field-title">{{ Lang::txt('COM_SUPPORT_FIELD_TITLE') }}</label>
            <input
                type="text"
                name="fields[title]"
                id="field-title"
                value="{{ $titleVal2 }}"
            />
        </fieldset>

        <fieldset class="query">
            @if ($row->conditions)
                @php
                  $condition = json_decode($row->conditions);
                @endphp
                @include('com_support::admin/views/queries/tmpl/condition', [
                    'option'     => $option,
                    'controller' => $controller,
                    'condition'  => $condition,
                    'conditions' => $conditions,
                    'row'        => $row,
                ])
            @endif
        </fieldset>

        <fieldset class="fields sort">
            <p>
                <label for="field-folder_id">{{ Lang::txt('In folder') }}</label>
                @php
                  $folders = \Components\Support\Models\QueryFolder::all()
                      ->whereEquals('user_id', User::get('id'))
                      ->order('ordering', 'ASC')
                      ->rows();
                @endphp
                <select name="fields[folder_id]" id="field-folder_id">
                    @if ($folders)
                        @foreach ($folders as $folder)
                            <option
                                value="{{ $folder->id }}"
                                @selected($row->folder_id == $folder->id)
                            >{{ $folder->title }}</option>
                        @endforeach
                    @endif
                </select>

                <label for="field-sort">{{ Lang::txt('COM_SUPPORT_QUERY_SORT_BY') }}</label>
                <select name="fields[sort]" id="field-sort">
                    <option value="open" @selected($row->sort == 'open')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN')}}
                    </option>
                    <option value="status" @selected($row->sort == 'status')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS')}}
                    </option>
                    <option value="login" @selected($row->sort == 'login')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER')}}
                    </option>
                    <option value="owner" @selected($row->sort == 'owner')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER')}}
                    </option>
                    <option value="group" @selected($row->sort == 'group')>
                        {{ Lang::txt('Group')}}
                    </option>
                    <option value="id" @selected($row->sort == 'id')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_ID')}}
                    </option>
                    <option value="report" @selected($row->sort == 'report')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT')}}
                    </option>
                    <option value="severity" @selected($row->sort == 'severity')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY')}}
                    </option>
                    <option value="tag" @selected($row->sort == 'tag')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_TAG')}}
                    </option>
                    <option value="type" @selected($row->sort == 'type')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE')}}
                    </option>
                    <option value="created" @selected($row->sort == 'created')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED')}}
                    </option>
                    <option value="closed" @selected($row->sort == 'closed')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED')}}
                    </option>
                    <option value="category" @selected($row->sort == 'category')>
                        {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY')}}
                    </option>
                </select>
                <select name="fields[sort_dir]" id="field-sort_dir">
                    <option value="DESC" @selected(strtolower($row->sort_dir) == 'desc')>desc</option>
                    <option value="ASC" @selected(strtolower($row->sort_dir) == 'asc')>asc</option>
                </select>
            </p>
        </fieldset>

        <input
            type="hidden"
            name="fields[id]"
            value="{{ ($row->iscore == 0) ? $row->id : 0 }}"
        />
        <input
            type="hidden"
            name="fields[conditions]"
            id="field-conditions"
            value="{{ $condVal2 }}"
        />
        <input type="hidden" name="fields[user_id]" value="{{ User::get('id') }}" />

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input
            type="hidden"
            name="no_html"
            value="{{ $tmpl ? 1 : Request::getInt('no_html', 0) }}"
        />
        <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
        <input type="hidden" name="task" value="save" />

        {!! Html::input('token') !!}
    </form>
@endif
