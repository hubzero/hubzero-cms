{{--
  Mailings — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $sort    = $filters['sort'] ?? 'date';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGS') }}"
    icon="mailing"
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
          <th>
            {!! Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER', 'subject', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_NEWSLETTER_MAILING_DATE', 'date', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">{{ Lang::txt('COM_NEWSLETTER_MAILING_PERCENT_COMPLETE') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_NEWSLETTER_MAILING_REOCCUR') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $mailings->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($mailings as $i => $mailing)
          @php
            $pct = $mailing->emails_total > 0
                ? number_format(($mailing->emails_sent / $mailing->emails_total) * 100, 2)
                : '0.00';
            $sentCount  = number_format($mailing->emails_sent);
            $totalCount = number_format($mailing->emails_total);

            $autogenLabels = [
                0 => 'N/A',
                1 => 'Daily',
                2 => 'Weekly',
                3 => 'Monthly',
            ];
            $autogen = $mailing->newsletter
                ? ($autogenLabels[$mailing->newsletter->get('autogen')] ?? 'N/A')
                : 'N/A';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $mailing->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $mailing->newsletter ? $mailing->newsletter->get('name', Lang::txt('COM_NEWSLETTER_UNKNOWN')) : Lang::txt('COM_NEWSLETTER_UNKNOWN') }}"
                     data-check-item />
            </td>
            <td>
              {{ $mailing->newsletter ? $mailing->newsletter->get('name', Lang::txt('COM_NEWSLETTER_UNKNOWN')) : Lang::txt('COM_NEWSLETTER_UNKNOWN') }}
            </td>
            <td class="priority-3">
              <time datetime="{{ $mailing->date }}">
                {{ Date::of($mailing->date)->format('M d, Y @ g:ia') }}
              </time>
            </td>
            <td class="priority-2">
              {{ $pct }} %
              ({{ Lang::txt('COM_NEWSLETTER_NUM_OF_EMAILS_SENT', $sentCount, $totalCount) }})
            </td>
            <td class="priority-4">
              {{ Lang::txt($autogen) }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_NEWSLETTER_NO_MAILINGS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
