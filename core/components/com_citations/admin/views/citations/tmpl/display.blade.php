{{--
  Citations — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Citations\Helpers\Permissions::getActions('citation');

  Toolbar::title(Lang::txt('CITATIONS'), 'citation');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_citations', 600, 800);
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::help('citations');

  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'created';
  $token   = Session::getFormToken();
  $baseUrl = 'index.php?option=' . $option . '&controller=' . $controller;
@endphp

<form action="{{ Route::url('index.php?option=' . $option, false) }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <x-admin-filters>
    <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
    <input type="text" name="search" id="filter_search"
           class="filter"
           value="{{ $filters['search'] ?? '' }}"
           placeholder="{{ Lang::txt('COM_CITATIONS_FILTER_SEARCH_PLACEHOLDER') }}" />
    <button type="submit" class="btn btn-primary">{{ Lang::txt('GO') }}</button>
    <button type="button" class="filter-clear btn btn-secondary">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>

    @slot('right')
      <label for="scope">{{ Lang::txt('SCOPE') }}:</label>
      <select name="scope" id="scope" class="filter filter-submit">
        <option value="all" @selected(($filters['scope'] ?? '') == 'all')>{{ Lang::txt('- Scope -') }}</option>
        <option value="hub" @selected(($filters['scope'] ?? '') == 'hub')>{{ Lang::txt('HUB') }}</option>
        <option value="group" @selected(($filters['scope'] ?? '') == 'group')>{{ Lang::txt('GROUP') }}</option>
        <option value="member" @selected(($filters['scope'] ?? '') == 'member')>{{ Lang::txt('MEMBER') }}</option>
      </select>
    @endslot
  </x-admin-filters>

  <table class="admin-table">
    <thead>
      <tr>
        <th class="admin-table-col-check">
          <input type="checkbox" name="checkall-toggle" id="checkall-toggle"
                 value="" class="checkbox-toggle toggle-all" />
          <label for="checkall-toggle" class="sr-only visually-hidden">
            {{ Lang::txt('JGLOBAL_CHECK_ALL') }}
          </label>
        </th>
        <th scope="col">{!! Html::grid('sort', 'ID', 'id', $sortDir, $sort) !!}</th>
        <th scope="col">{!! Html::grid('sort', 'TYPE', 'type', $sortDir, $sort) !!}</th>
        <th scope="col">{{ Lang::txt('TITLE') }} / {{ Lang::txt('AUTHORS') }}</th>
        <th scope="col">{!! Html::grid('sort', 'PUBLISHED', 'published', $sortDir, $sort) !!}</th>
        <th scope="col">{!! Html::grid('sort', 'YEAR', 'year', $sortDir, $sort) !!}</th>
        <th scope="col">{!! Html::grid('sort', 'AFFILIATED', 'affiliated', $sortDir, $sort) !!}</th>
        <th scope="col">{!! Html::grid('sort', 'FUNDED_BY', 'fundedby', $sortDir, $sort) !!}</th>
        <th scope="col">{!! Html::grid('sort', 'SCOPE', 'scope', $sortDir, $sort) !!}</th>
        <th scope="col">{!! Html::grid('sort', 'SCOPE_ID', 'scope_id', $sortDir, $sort) !!}</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="10">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
    <tbody>
      @php $i = 0; @endphp
      @foreach($rows as $row)
        @php
          // State badge
          if ($row->published == 1) {
              $badge = 'badge-success';
              $alt   = Lang::txt('UNPUBLISH');
              $task  = 'unpublish';
          } elseif ($row->published == 0) {
              $badge = 'badge-error';
              $alt   = Lang::txt('PUBLISH');
              $task  = 'publish';
          } else {
              $badge = 'badge-warning';
              $alt   = Lang::txt('DELETED');
              $task  = 'publish';
          }

          $titleText  = html_entity_decode($row->title ?? '');
          $authorText = html_entity_decode($row->author ?? '');
          $editUrl = Route::url($baseUrl . '&task=edit&id=' . $row->id, false);
          $pubUrl  = Route::url($baseUrl . '&task=' . $task . '&id=' . $row->id . '&' . $token . '=1', false);
          $affUrl  = Route::url($baseUrl . '&task=affiliate&id=' . $row->id . '&' . $token . '=1', false);
          $fundUrl = Route::url($baseUrl . '&task=fund&id=' . $row->id . '&' . $token . '=1', false);
        @endphp
        <tr>
          <td>
            <input type="checkbox" name="id[]" id="cb{{ $row->id }}"
                   value="{{ $row->id }}" class="checkbox-toggle" />
            <label for="cb{{ $row->id }}" class="sr-only visually-hidden">{{ $row->id }}</label>
          </td>
          <td>{{ $row->id }}</td>
          <td>
            @php $type = $row->relatedType->get('type_title'); @endphp
            {{ $type ?: Lang::txt('GENERIC') }}
          </td>
          <td>
            @if($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">{{ $titleText }}</a>
              <br /><small>{{ $authorText }}</small>
            @else
              <span>{{ $titleText }}</span>
              <br /><small>{{ $authorText }}</small>
            @endif
          </td>
          <td>
            @if($canDo->get('core.edit.state'))
              <a class="badge {{ $badge }} gap-1" href="{{ $pubUrl }}">{{ $alt }}</a>
            @else
              <span class="badge {{ $badge }}">{{ $alt }}</span>
            @endif
          </td>
          <td>{{ $row->year ?? '' }}</td>
          <td>
            <a class="badge {{ $row->affiliated == 1 ? 'badge-success' : 'badge-error' }} gap-1"
               href="{{ $affUrl }}">
              {{ $row->affiliated == 1 ? Lang::txt('YES') : Lang::txt('NO') }}
            </a>
          </td>
          <td>
            <a class="badge {{ $row->fundedby == 1 ? 'badge-success' : 'badge-error' }} gap-1"
               href="{{ $fundUrl }}">
              {{ $row->fundedby == 1 ? Lang::txt('YES') : Lang::txt('NO') }}
            </a>
          </td>
          <td>{{ $row->scope == '' ? Lang::txt('Hub') : e($row->scope) }}</td>
          <td>{{ $row->scope_id == 0 ? Lang::txt('N/A') : e($row->scope_id) }}</td>
        </tr>
        @php $i++; @endphp
      @endforeach
    </tbody>
  </table>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $filters['sort'] ?? '' }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $filters['sort_Dir'] ?? '' }}" />

  {!! Html::input('token') !!}
</form>
