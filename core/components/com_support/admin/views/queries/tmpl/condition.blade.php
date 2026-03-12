{{--
  Support — Query condition/expression builder partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $randId      = rand();
  $andSelected = (strtolower($condition->operator) == 'and') ? ' selected="selected"' : '';
  $orSelected  = (strtolower($condition->operator) == 'or')  ? ' selected="selected"' : '';
  $selectHtml  = '<select id="match' . $randId . '" aria-label="' . Lang::txt('COM_SUPPORT_QUERY_MATCH_CONDITION') . '">'
      . '<option value="AND"' . $andSelected . '>' . Lang::txt('COM_SUPPORT_QUERY_ALL') . '</option>'
      . '<option value="OR"' . $orSelected . '>' . Lang::txt('COM_SUPPORT_QUERY_ANY') . '</option>'
      . '</select>';
@endphp
<fieldset class="condition-set">
    <p class="operator">
        <button class="remove" alt="{{ Lang::txt('COM_SUPPORT_QUERY_REMOVE') }}">&times;</button>
        {!! Lang::txt('COM_SUPPORT_QUERY_MATCH', $selectHtml) !!}
    </p>
    <div class="querycntnr">
        <div class="querystmts querycntnr">
            @if($condition->expressions)
                @foreach($condition->expressions as $expression)
                    @php
                        $operators = $conditions->{$expression->fldval}->operators;
                        $values    = $conditions->{$expression->fldval}->values;
                    @endphp
                    <p class="conditions">
                        <button class="remove" alt="{{ Lang::txt('COM_SUPPORT_QUERY_REMOVE') }}">&times;</button>
                        <select class="fld" aria-label="{{ Lang::txt('COM_SUPPORT_QUERY_FIELD') }}">
                            <option value="open"@if($expression->fldval == 'open') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN') }}
                            </option>
                            <option value="status"@if($expression->fldval == 'status') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS') }}
                            </option>
                            <option value="login"@if($expression->fldval == 'login') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER') }}
                            </option>
                            <option value="owner"@if($expression->fldval == 'owner') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER') }}
                            </option>
                            <option value="group"@if($expression->fldval == 'group') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_GROUP') }}
                            </option>
                            <option value="id"@if($expression->fldval == 'id') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_ID') }}
                            </option>
                            <option value="report"@if($expression->fldval == 'report') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT') }}
                            </option>
                            <option value="severity"@if($expression->fldval == 'severity') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY') }}
                            </option>
                            <option value="tag"@if($expression->fldval == 'tag') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_TAG') }}
                            </option>
                            <option value="type"@if($expression->fldval == 'type') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE') }}
                            </option>
                            <option value="created"@if($expression->fldval == 'created') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED') }}
                            </option>
                            <option value="closed"@if($expression->fldval == 'closed') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED') }}
                            </option>
                            <option value="category"@if($expression->fldval == 'category') selected="selected"@endif>
                                {{ Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY') }}
                            </option>
                        </select>
                        <select class="op" aria-label="{{ Lang::txt('COM_SUPPORT_QUERY_OPERATOR') }}">
                            @if($operators)
                                @foreach($operators as $operator)
                                    <option
                                        value="{{ $operator->val }}"
                                        @if($expression->opval == $operator->val) selected="selected"@endif
                                    >{{ $operator->label }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if(is_array($values))
                            <select class="val" aria-label="{{ Lang::txt('COM_SUPPORT_QUERY_VALUE') }}">
                                @foreach($values as $value)
                                    <option
                                        value="{{ $value->val }}"
                                        @if($expression->val == $value->val) selected="selected"@endif
                                    >{{ $value->label }}</option>
                                @endforeach
                            </select>
                        @else
                            @php
                                if ($expression->val == '$me') {
                                    $expression->val = User::get('username');
                                }
                            @endphp
                            <input
                                type="text"
                                class="val"
                                value="{{ $expression->val }}"
                            />
                        @endif
                    </p>
                @endforeach
            @endif
            <span class="query-btns">
                <button class="add">+</button>
                <button class="addroot">...</button>
            </span>
        </div>
    </div>
    @if($condition->nestedexpressions && count($condition->nestedexpressions) > 0)
        @foreach($condition->nestedexpressions as $nested)
            @include('com_support::admin/views/queries/tmpl/condition', [
                'option'     => $option,
                'controller' => $controller,
                'condition'  => $nested,
                'conditions' => $conditions,
                'row'        => $row,
            ])
        @endforeach
    @endif
</fieldset>
