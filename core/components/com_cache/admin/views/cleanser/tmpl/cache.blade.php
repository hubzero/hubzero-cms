{{--
  Cache Manager — Clear Cache list view

  Variables from controller:
    $data        — array of objects with ->group, ->count, ->size (KB)
    $client      — object with ->id
    $state       — Hubzero\Base\Obj  (list.ordering, list.direction, clientId)
    $pagination  — Hubzero\Pagination\Paginator

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Components\Cache\Helpers\Helper;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;
  use Hubzero\Utility\Number;

  Toolbar::title(Lang::txt('COM_CACHE_CLEAR_CACHE'), 'clear');
  Toolbar::custom('delete', 'delete', '', 'JTOOLBAR_DELETE', true);
  Toolbar::divider();
  if (User::authorise('core.admin', 'com_cache')) {
      Toolbar::preferences('com_cache');
      Toolbar::divider();
  }
  Toolbar::help('clear');

  $sort    = $state->get('list.ordering', 'group');
  $sortDir = $state->get('list.direction', 'asc');

  $clientOptions = Helper::getClientOptions();
  $currentClient = $state->get('clientId');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  {{-- Filters --}}
  <x-admin-filters>
    <label for="filter_client_id" class="sr-only">{{ Lang::txt('COM_CACHE_SELECT_CLIENT') }}</label>
    <select name="filter_client_id"
            id="filter_client_id"
            class="select select-sm"
            data-submit-on-change>
      @foreach($clientOptions as $opt)
        <option value="{{ $opt->value }}"
                @if($opt->value == $currentClient) selected @endif>
          {{ ucfirst(e($opt->text)) }}
        </option>
      @endforeach
    </select>
  </x-admin-filters>

  <div class="mt-4 bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_CACHE_GROUP', 'group', $sortDir, $sort) !!}</th>
          <th class="text-center">{!! Html::grid('sort', 'COM_CACHE_NUMBER_OF_FILES', 'count', $sortDir, $sort) !!}</th>
          <th class="text-center">{!! Html::grid('sort', 'COM_CACHE_SIZE', 'size', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <div class="admin-pagination">
              {!! $pagination->render() !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($data as $i => $item)
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="cid[]"
                     id="cb{{ $i }}"
                     value="{{ $item->group }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
              <label for="cb{{ $i }}" class="sr-only">{{ $item->group }}</label>
            </td>
            <td><strong>{{ $item->group }}</strong></td>
            <td class="text-center">{{ $item->count }}</td>
            <td class="text-center">{{ Number::formatBytes($item->size * 1024) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-6 text-muted-foreground">
              {{ Lang::txt('COM_CACHE_NO_GROUPS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="client" value="{{ (int) $client->id }}" />
</x-admin-form>
