{{--
  Campaigns — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $canDo   = \Components\Newsletter\Helpers\Permissions::getActions('campaign');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'ASC';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_CAMPAIGNS') }}"
    icon="campaigns"
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
          <th class="w-16">
            {!! Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_NAME', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_EXPIRE_DATE', 'expire_date', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {{ Lang::txt('COM_NEWSLETTER_CAMPAIGN_DESCRIPTION') }}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_DATE', 'campaign_date', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_MOD_DATE', 'modified', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_NEWSLETTER_CAMPAIGN_MOD_BY', 'modified_by', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              {!! $campaigns->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($campaigns as $i => $campaign)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $campaign->id,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $campaign->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $campaign->title }}"
                     data-check-item />
            </td>
            <td>{{ $campaign->id }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $campaign->title }}
                </a>
              @else
                {{ $campaign->title }}
              @endif
            </td>
            <td class="priority-3">
              {{ Date::of($campaign->expire_date)->toLocal('Y-m-d H:ia') }}
            </td>
            <td class="priority-4">
              {{ $campaign->description }}
            </td>
            <td class="priority-3">
              {{ Date::of($campaign->campaign_date)->toLocal('Y-m-d H:ia') }}
            </td>
            <td class="priority-3">
              {{ Date::of($campaign->modified)->toLocal('Y-m-d H:ia') }}
            </td>
            <td class="priority-3">
              {{ $campaign->modified_by ? User::one($campaign->modified_by)->name : '' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_NEWSLETTER_NO_CAMPAIGNS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
