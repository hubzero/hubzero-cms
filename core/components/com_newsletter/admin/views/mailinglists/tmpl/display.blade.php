{{--
  Mailing Lists — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Newsletter\Helpers\Permissions::getActions('mailinglist');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'ASC';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') }}"
    icon="list"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_NEWSLETTER_FILTER_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_NEWSLETTER_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot
    </x-admin-filters>
  @endslot

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
          <th>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_NAME') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ACTIVE_SUBSCRIBERS') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_TOTAL_SUBSCRIBERS') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $lists->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($lists as $i => $list)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $list->id,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $list->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $list->name }}"
                     data-check-item />
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $list->name }}
                </a>
              @else
                {{ $list->name }}
              @endif
            </td>
            <td class="priority-3">
              @if($list->private)
                <span class="badge badge-sm badge-ghost">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PRIVATE') }}</span>
              @else
                <span class="badge badge-sm badge-success">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_PRIVACY_PUBLIC') }}</span>
              @endif
            </td>
            <td class="priority-2">
              {{ $list->emails()->whereEquals('status', 'active')->total() }}
            </td>
            <td class="priority-2">
              {{ $list->emails()->total() }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_NO_LISTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
