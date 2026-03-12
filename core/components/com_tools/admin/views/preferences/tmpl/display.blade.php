{{--
  Tool User Preferences — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'username';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS_USER_PREFS'), 'user');
  Toolbar::addNew();
  Toolbar::editList();
  Toolbar::custom('restoreDefault', 'restore', 'restore', 'COM_TOOLS_USER_PREFS_DEFAULT');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
    <div class="flex flex-wrap gap-2 items-end">
      <div class="flex gap-1">
        <label for="filter-search-field" class="sr-only">{{ Lang::txt('COM_TOOLS_USER_PREFS_USERNAME') }}</label>
        <select name="search_field" id="filter-search-field" class="select select-sm select-bordered">
          <option value="username" @selected(($filters['search_field'] ?? 'username') === 'username')>
            {{ Lang::txt('COM_TOOLS_USER_PREFS_USERNAME') }}
          </option>
          <option value="name" @selected(($filters['search_field'] ?? '') === 'name')>
            {{ Lang::txt('COM_TOOLS_USER_PREFS_NAME') }}
          </option>
        </select>
        <input type="text"
               name="search"
               class="input input-sm input-bordered"
               placeholder="{{ Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER') }}"
               value="{{ $filters['search'] ?? '' }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_TOOLS_GO') }}</button>
      </div>
      <label for="filter-class-alias" class="sr-only">{{ Lang::txt('COM_TOOLS_FILTER_SESSION_CLASS') }}</label>
      <select name="class_alias" id="filter-class-alias" class="select select-sm select-bordered" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_TOOLS_FILTER_SESSION_CLASS') }}</option>
        @foreach($classes as $class)
          <option value="{{ $class->alias }}" @selected(($filters['class_alias'] ?? '') === $class->alias)>
            {{ $class->alias }}
          </option>
        @endforeach
      </select>
    </div>
  </x-admin-filters>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_TOOLS_USER_PREFS_USER_ID', 'user_id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_USER_PREFS_USERNAME', 'username', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_USER_PREFS_NAME', 'name', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_USER_PREFS_CLASS', 'class_alias', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'COM_TOOLS_USER_PREFS_JOBS', 'jobs', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $editUrl = Route::url(
              'index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->id, false
            );
          @endphp
          <tr>
            <td>
              <input type="checkbox" name="id[]" id="cb{{ $i }}" value="{{ $row->id }}"
                     class="checkbox checkbox-sm" data-check-item aria-label="{{ $row->id }}" />
            </td>
            <td class="priority-4">
              <a href="{{ $editUrl }}" class="link link-hover">{{ $row->user_id }}</a>
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">{{ $row->username }}</a>
            </td>
            <td class="priority-3">{{ $row->name }}</td>
            <td>
              @if($row->class_alias)
                <span class="badge badge-sm badge-info">{{ $row->class_alias }}</span>
              @else
                <span class="text-muted-foreground text-sm">{{ Lang::txt('COM_TOOLS_USER_PREFS_CUSTOM') }}</span>
              @endif
            </td>
            <td class="priority-2 font-medium">{{ $row->jobs }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
