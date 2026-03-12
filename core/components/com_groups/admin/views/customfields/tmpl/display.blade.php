{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

$canDo = \Components\Groups\Helpers\Permissions::getActions('fieldset');

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_CUSTOMFIELDS'), 'form');
if ($canDo->get('core.delete')) {
    Toolbar::deleteList();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('forms');

$sortDir = @$filters['sort_Dir'];
$sort    = @$filters['sort'];
@endphp

<x-admin-form option="{{ $option }}" controller="{{ $controller }}" sort="{{ $sort }}" sortDir="{{ $sortDir }}">
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <input type="text"
            name="search"
            id="filter_search"
            class="input input-bordered input-sm"
            value="{{ $filters['search'] }}"
            placeholder="{{ Lang::txt('JSEARCH_FILTER') }}"
        />
      @endslot
    </x-admin-filters>
  @endslot

    <table class="admin-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        data-check-all
                    />
                    <label for="checkall-toggle" class="sr-only">
                        {{ Lang::txt('JGLOBAL_CHECK_ALL') }}
                    </label>
                </th>
                <th scope="col" class="priority-5">
                    {!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}
                </th>
                <th scope="col">
                    {!! Html::grid('sort', 'JGLOBAL_TITLE', 'content', $sortDir, $sort) !!}
                </th>
                <th scope="col" class="priority-2">
                    {!! Html::grid('sort', 'COM_GROUPS_COL_CREATOR', 'created_by', $sortDir, $sort) !!}
                </th>
                <th scope="col" class="priority-3">
                    {!! Html::grid('sort', 'COM_GROUPS_COL_ANONYMOUS', 'state', $sortDir, $sort) !!}
                </th>
                <th scope="col" class="priority-4">
                    {!! Html::grid('sort', 'JDATE', 'created', $sortDir, $sort) !!}
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="6">{!! $rows->pagination !!}</td>
            </tr>
        </tfoot>
        <tbody>
            @php $k = 0; $i = 0; @endphp
            @foreach ($rows as $row)
                @php
                if (!$row->get('anonymous')) {
                    $calt  = Lang::txt('JOFF');
                    $cls2  = 'off';
                    $state = 1;
                } else {
                    $calt  = Lang::txt('JON');
                    $cls2  = 'on';
                    $state = 0;
                }
                $editUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=edit&id=' . $row->get('id'), false
                );
                $anonUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=anonymous&state=' . $state
                    . '&id=' . $row->get('id')
                    . '&' . Session::getFormToken() . '=1', false
                );
                @endphp
                <tr class="row{{ $k }}">
                    <td>
                        <input type="checkbox"
                            name="id[]"
                            id="cb{{ $i }}"
                            value="{{ $row->get('id') }}"
                            class="checkbox checkbox-sm"
                            aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', \Hubzero\Utility\Str::truncate(strip_tags($row->content), 50)) }}"
                            data-check-item
                        />
                    </td>
                    <td class="priority-5">
                        {{ $row->get('id') }}
                    </td>
                    <td>
                        {{ $row->get('treename') }}
                        @if ($canDo->get('core.edit'))
                            <a href="{{ $editUrl }}">
                                {{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), 90) }}
                            </a>
                        @else
                            <span>
                                {{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), 90) }}
                            </span>
                        @endif
                    </td>
                    <td class="priority-2">
                        {{ $row->creator->get('name') }}
                    </td>
                    <td class="priority-3">
                        <a class="state {{ $cls2 }}" href="{{ $anonUrl }}">
                            <span>{{ $calt }}</span>
                        </a>
                    </td>
                    <td class="priority-4">
                        <time datetime="{{ $row->get('created') }}">
                            {{ $row->created('date') }}
                        </time>
                    </td>
                </tr>
                @php $k = 1 - $k; $i++; @endphp
            @endforeach
        </tbody>
    </table>

</x-admin-form>
