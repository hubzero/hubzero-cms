{{--
  com_templates — Template list

  Variables: $rows (paginated), $filters (search/client_id/sort/sort_Dir), $preview (bool)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Templates\Helpers\Utilities::getActions();

  Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_TEMPLATES'), 'thememanager');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_templates');
      Toolbar::spacer();
  }
  Toolbar::help('templates');

  $sortDir = $filters['sort_Dir'];
  $sort    = $filters['sort'];
@endphp

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
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('COM_TEMPLATES_TEMPLATES_FILTER_SEARCH_DESC') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
      @endslot

      <select name="filter_client_id"
              id="filter_client_id"
              class="select select-bordered select-sm"
              aria-label="{{ Lang::txt('JGLOBAL_FILTER_CLIENT') }}"
              data-submit-on-change>
        <option value="*">{{ Lang::txt('JGLOBAL_FILTER_CLIENT') }}</option>
        {!! Html::select('options',
            \Components\Templates\Helpers\Utilities::getClientOptions(),
            'value', 'text',
            $filters['client_id']) !!}
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th scope="col" class="w-20">&#160;</th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'a.element', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'JCLIENT', 'a.client_id', $sortDir, $sort) !!}
          </th>
          <th scope="col" class="priority-4">{{ Lang::txt('HVERSION') }}</th>
          <th scope="col" class="priority-5">{{ Lang::txt('JDATE') }}</th>
          <th scope="col" class="priority-3">{{ Lang::txt('JAUTHOR') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">{!! $rows->pagination !!}</div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($rows as $item)
          @php
            $filesUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=files&id=' . (int) $item->extension_id, false
            );
            $author = $item->xml->get('author');
            $email  = $item->xml->get('authorEmail');
            $url    = $item->xml->get('authorUrl');
          @endphp
          <tr>
            <td>
              {!! \Components\Templates\Helpers\Utilities::thumb($item->element, $item->protected) !!}
            </td>
            <td>
              <a href="{{ $filesUrl }}" class="link link-hover text-primary font-medium">
                {{ Lang::txt('COM_TEMPLATES_TEMPLATE_DETAILS', ucfirst($item->name)) }}
              </a>
              <p class="text-xs text-muted-foreground mt-0.5">
                @if ($preview && $item->client_id == '0')
                  @php $previewUrl = Request::root() . 'index.php?tp=1&template=' . $item->element; @endphp
                  <a href="{{ $previewUrl }}" rel="noopener" target="_blank" class="link link-hover">
                    {{ Lang::txt('COM_TEMPLATES_TEMPLATE_PREVIEW') }}
                  </a>
                @elseif ($item->client_id == '1')
                  {{ Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW_ADMIN') }}
                @else
                  {{ Lang::txt('COM_TEMPLATES_TEMPLATE_NO_PREVIEW') }}
                @endif
              </p>
            </td>
            <td>
              {{ $item->client_id == 0 ? Lang::txt('JSITE') : Lang::txt('JADMINISTRATOR') }}
            </td>
            <td class="priority-4">{{ $item->xml->get('version') }}</td>
            <td class="priority-5">{{ $item->xml->get('creationDate') }}</td>
            <td class="priority-3 text-sm">
              @if ($author)<div>{{ $author }}</div>@else <span class="text-faint-foreground">—</span>@endif
              @if ($email)<div class="text-muted-foreground">{{ $email }}</div>@endif
              @if ($url)<div><a href="{{ $url }}" class="link link-hover text-xs">{{ $url }}</a></div>@endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_TEMPLATES_NO_MATCHING_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</x-admin-form>
