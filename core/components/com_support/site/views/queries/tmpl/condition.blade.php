{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $andSelected = (strtolower($condition->operator) == 'and') ? ' selected="selected"' : '';
    $orSelected  = (strtolower($condition->operator) == 'or') ? ' selected="selected"' : '';
    $selectHtml  = '<select>'
        . '<option value="AND"' . $andSelected . '>'
        . \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_ALL') . '</option>'
        . '<option value="OR"' . $orSelected . '>'
        . \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_ANY') . '</option>'
        . '</select>';
@endphp

<fieldset class="condition-set">
    <p class="operator">
        <button class="remove" alt="Remove">&times;</button>
        {!! \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_MATCH', $selectHtml) !!}
    </p>
    <div>
        <div class="querystmts">
@if (!empty($condition->expressions))
    @foreach ($condition->expressions as $expression)
        @php
            $operators = $conditions->{$expression->fldval}->operators;
            $values    = $conditions->{$expression->fldval}->values;
        @endphp
        <p class="conditions"><button class="remove" alt="Remove">&times;</button> <select class="fld">
            <option value="open"{{ $expression->fldval == 'open' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_OPEN') }}</option>
                <option value="status"{{ $expression->fldval == 'status' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_STATUS') }}</option>
                <option value="login"{{ $expression->fldval == 'login' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_SUBMITTER') }}</option>
                <option value="owner"{{ $expression->fldval == 'owner' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_OWNER') }}</option>
                <option value="group"{{ $expression->fldval == 'group' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_GROUP') }}</option>
                <option value="id"{{ $expression->fldval == 'id' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_ID') }}</option>
                <option value="report"{{ $expression->fldval == 'report' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_REPORT') }}</option>
                <option value="severity"{{ $expression->fldval == 'severity' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_SEVERITY') }}</option>
                <option value="tag"{{ $expression->fldval == 'tag' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_TAG') }}</option>
                <option value="type"{{ $expression->fldval == 'type' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_TYPE') }}</option>
                <option value="created"{{ $expression->fldval == 'created' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CREATED') }}</option>
                <option value="closed"{{ $expression->fldval == 'closed' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CLOSED') }}</option>
                <option value="category"{{ $expression->fldval == 'category' ? ' selected="selected"' : '' }}>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERY_SORT_CATEGORY') }}</option>
            </select>
            <select class="op">
        @if ($operators)
            @foreach ($operators as $operator)
                <option value="{{ $operator->val }}"{{ $expression->opval == $operator->val ? ' selected="selected"' : '' }}>{{ $operator->label }}</option>
            @endforeach
        @endif
            </select>
        @if (is_array($values))
            <select class="val">
            @foreach ($values as $value)
                <option value="{{ $value->val }}"{{ $expression->val == $value->val ? ' selected="selected"' : '' }}>{{ $value->label }}</option>
            @endforeach
            </select>
        @else
            @php
                if ($expression->val == '$me') {
                    $expression->val = \Hubzero\Facades\User::get('username');
                }
            @endphp
            <input
                type="text"
                class="val"
                value="{{ $__view->escape(stripslashes($expression->val)) }}"
            />
        @endif
        </p>
    @endforeach
@endif
            <span>
                <button class="add">+</button>
                <button class="addroot">...</button>
            </span>
        </div>
@if (!empty($condition->nestedexpressions) && count($condition->nestedexpressions) > 0)
    @foreach ($condition->nestedexpressions as $nested)
        {!! $__view->view('condition')
            ->set('option', $option)
            ->set('controller', $controller)
            ->set('condition', $nested)
            ->set('conditions', $conditions)
            ->set('row', $row)
            ->display() !!}
    @endforeach
@endif
    </div>
</fieldset>
