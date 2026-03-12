{{--
  User Quotas — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Members\Helpers\Admin::getActions('component');
  $sort    = $filters['sort'] ?? 'user_id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  if ($canDo->get('core.edit')) {
      Toolbar::custom('syncQuotasToSystem', 'refresh', 'refresh', 'Sync Selected User Quotas');
      Toolbar::spacer();
  }

  $__view->css('quotas.css')->js('quotas.blade.js');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS_QUOTAS') }}"
    icon="user"
    :canDo="$canDo"
    option="{{ $option }}"
/>

@include('com_members::admin.views.quotas.tmpl._submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
    @slot('search')
      <select name="search_field"
              id="filter_search_field"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('COM_MEMBERS_SEARCH_FIELD') }}">
        <option value="username" @selected(($filters['search_field'] ?? '') == 'username')>
          {{ Lang::txt('COM_MEMBERS_QUOTA_USERNAME') }}
        </option>
        <option value="name" @selected(($filters['search_field'] ?? '') == 'name')>
          {{ Lang::txt('COM_MEMBERS_QUOTA_NAME') }}
        </option>
      </select>
      <input type="text"
             name="search"
             id="filter_search"
             class="input input-bordered input-sm w-48"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER') }}" />
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('COM_MEMBERS_GO') }}
      </button>
    @endslot

    <select name="class_alias"
            id="filter_class_alias"
            class="select select-bordered select-sm"
            data-submit-on-change
            aria-label="{{ Lang::txt('COM_MEMBERS_FILTER_QUOTA_CLASS') }}">
      <option value="">{{ Lang::txt('COM_MEMBERS_FILTER_QUOTA_CLASS') }}</option>
      @foreach($classes as $class)
        <option value="{{ $class->get('alias') }}"
                @selected(($filters['class_alias'] ?? '') == $class->get('alias'))>
          {{ $class->get('alias') }}
        </option>
      @endforeach
    </select>
  </x-admin-filters>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_MEMBERS_QUOTA_USER_ID', 'user_id', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_MEMBERS_QUOTA_USERNAME', 'username', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_QUOTA_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_MEMBERS_QUOTA_CLASS', 'class_alias', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('COM_MEMBERS_QUOTA_DISK_USAGE') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('user_id'), false
            );
            $quotaUrl = Route::url(
                'index.php?option=com_members&controller=quotas&task=getQuotaUsage&id=' . $row->get('id'),
                false, false
            );
          @endphp
          <tr data-quota="{{ $quotaUrl }}">
            @php
              $cbLabel = $row->get('username') ?: $row->get('name') ?: Lang::txt('COM_MEMBERS_QUOTA_USER_ID') . ' ' . $row->get('user_id');
            @endphp
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('user_id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $cbLabel) }}"
                     data-check-item />
            </td>
            <td class="priority-5">
              <a href="{!! $editUrl !!}" class="link link-hover text-primary">
                {{ $row->get('user_id') }}
              </a>
            </td>
            <td class="priority-4">
              <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium"
                 @unless($row->get('username')) aria-label="{{ $cbLabel }}" @endunless>
                {{ $row->get('username') }}
              </a>
            </td>
            <td>{{ $row->get('name') }}</td>
            <td class="priority-3">{{ $row->get('class_alias', 'custom') }}</td>
            <td>
              <span class="text-sm text-muted-foreground">{{ Lang::txt('COM_MEMBERS_QUOTA_CALCULATING') }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
