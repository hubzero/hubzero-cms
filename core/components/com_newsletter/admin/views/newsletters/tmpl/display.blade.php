{{--
  Newsletters — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Newsletter\Helpers\Permissions::getActions('newsletter');
  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'ASC';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER') }}"
    icon="newsletter"
    :canDo="$canDo"
    option="{{ $option }}"
/>

@if(!$dependency)
  @include('com_newsletter::admin.views.newsletters.tmpl._dependency')
@endif

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

      <label for="filter-type" class="sr-only">{{ Lang::txt('COM_NEWSLETTER_ALL_TYPES') }}</label>
      <select name="type" id="filter-type"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="" @selected(($filters['type'] ?? '') === '')>{{ Lang::txt('COM_NEWSLETTER_ALL_TYPES') }}</option>
        <option value="html" @selected(($filters['type'] ?? '') === 'html')>{{ Lang::txt('COM_NEWSLETTER_TYPE_HTML') }}</option>
        <option value="plain" @selected(($filters['type'] ?? '') === 'plain')>{{ Lang::txt('COM_NEWSLETTER_TYPE_PLAIN') }}</option>
      </select>
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
            {!! Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_FORMAT', 'type', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_TEMPLATE', 'template_id', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_PUBLIC', 'published', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_SENT', 'sent', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_NEWSLETTER_NEWSLETTER_TRACKING', 'tracking', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $newsletter)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $newsletter->id,
                false, false
            );
            $token   = Session::getFormToken();
            $pubBase = 'index.php?option=' . $option . '&controller=' . $controller;

            // Template name
            if ($newsletter->get('template_id') == '-1') {
                $tplName = Lang::txt('COM_NEWSLETTER_NO_TEMPLATE');
            } else {
                $tplName = $newsletter->template
                    ? $newsletter->template->name
                    : Lang::txt('COM_NEWSLETTER_NO_TEMPLATE_FOUND');
            }
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $newsletter->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $newsletter->name }}"
                     data-check-item />
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $newsletter->name }}
                </a>
              @else
                {{ $newsletter->name }}
              @endif
            </td>
            <td class="priority-3">
              @if($newsletter->type == 'html')
                <span class="badge badge-sm badge-info">{{ Lang::txt('COM_NEWSLETTER_FORMAT_HTML') }}</span>
              @else
                <span class="badge badge-sm badge-ghost">{{ Lang::txt('COM_NEWSLETTER_FORMAT_PLAIN') }}</span>
              @endif
            </td>
            <td class="priority-4">{{ $tplName }}</td>
            <td class="priority-2">
              @if($newsletter->published)
                <a href="{{ Route::url($pubBase . '&task=unpublish&id=' . $newsletter->id . '&' . $token . '=1', false) }}">
                  <span class="badge badge-sm badge-success">{{ Lang::txt('JYES') }}</span>
                </a>
              @else
                <a href="{{ Route::url($pubBase . '&task=publish&id=' . $newsletter->id . '&' . $token . '=1', false) }}">
                  <span class="badge badge-sm badge-ghost">{{ Lang::txt('JNO') }}</span>
                </a>
              @endif
            </td>
            <td>
              @if($newsletter->sent)
                <span class="badge badge-sm badge-success">{{ Lang::txt('JYES') }}</span>
              @else
                <span class="badge badge-sm badge-ghost">{{ Lang::txt('JNO') }}</span>
              @endif
            </td>
            <td class="priority-3">
              @if($newsletter->tracking)
                <span class="badge badge-sm badge-success">{{ Lang::txt('JYES') }}</span>
              @else
                <span class="badge badge-sm badge-ghost">{{ Lang::txt('JNO') }}</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_NEWSLETTER_NO_NEWSLETTER') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
