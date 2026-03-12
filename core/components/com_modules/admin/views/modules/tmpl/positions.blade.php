{{--
  com_modules — Position picker (tmpl=component popup)

  Variables: $items (array keyed by position value => templates array),
             $filters (search/sort/sort_Dir/client_id/state/template/type), $total (int)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $function  = Request::getCmd('function', 'jSelectPosition');
  $clientId  = $filters['client_id'] ?? 0;
  $sort      = $filters['sort'] ?? 'value';
  $sortDir   = $filters['sort_Dir'] ?? 'ASC';

  $formAction = Route::url(
      'index.php?option=com_modules&task=positions&tmpl=component'
      . '&function=' . $function . '&client_id=' . $clientId, false
  );
@endphp

<div class="p-4">
  <h2 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_MODULES') }}</h2>

  <form action="{{ $formAction }}" method="post" name="adminForm" id="adminForm">

    <div class="flex flex-wrap gap-2 mb-4">
      <div class="flex items-center gap-2">
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-48"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_MODULES_FILTER_SEARCH_DESC') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}</button>
      </div>
      <div class="flex items-center gap-2 ml-auto">
        <select name="filter_state" class="select select-bordered select-sm" aria-label="{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}" data-submit-on-change>
          <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
          {!! Html::select('options',
              \Components\Modules\Helpers\Modules::templateStates(),
              'value', 'text',
              $filters['state'] ?? '',
              true) !!}
        </select>

        <select name="filter_type" class="select select-bordered select-sm" aria-label="{{ Lang::txt('COM_MODULES_OPTION_SELECT_TYPE') }}" data-submit-on-change>
          <option value="">{{ Lang::txt('COM_MODULES_OPTION_SELECT_TYPE') }}</option>
          {!! Html::select('options',
              \Components\Modules\Helpers\Modules::types(),
              'value', 'text',
              $filters['type'] ?? '',
              true) !!}
        </select>

        <select name="filter_template" class="select select-bordered select-sm" aria-label="{{ Lang::txt('JOPTION_SELECT_TEMPLATE') }}" data-submit-on-change>
          <option value="">{{ Lang::txt('JOPTION_SELECT_TEMPLATE') }}</option>
          {!! Html::select('options',
              \Components\Modules\Helpers\Modules::templates($clientId),
              'value', 'text',
              $filters['template'] ?? '',
              true) !!}
        </select>
      </div>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th scope="col">
            {!! Html::grid('sort', 'JGLOBAL_TITLE', 'value', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_MODULES_HEADING_TEMPLATES', 'templates', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="2">
            @php
              $pagination = $this->pagination($total, $filters['limit'] ?? 0, $filters['start'] ?? 0);
              echo $pagination;
            @endphp
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($items as $value => $templates)
          @php $callbackArgs = json_encode([$value]); @endphp
          <tr>
            <td>
              <a class="link link-hover text-primary cursor-pointer"
                 data-parent-callback="{{ $function }}"
                 data-callback-args="{{ $callbackArgs }}">
                {{ $value }}
              </a>
            </td>
            <td class="text-sm">
              @if (!empty($templates))
                <a class="cursor-pointer"
                   data-parent-callback="{{ $function }}"
                   data-callback-args="{{ $callbackArgs }}">
                  <ul class="list-none m-0 p-0 space-y-0.5">
                    @foreach ($templates as $template => $label)
                      <li>
                        @php $langObj = Lang::getRoot(); @endphp
                        @if ($langObj->hasKey($label))
                          {{ Lang::txt('COM_MODULES_MODULE_TEMPLATE_POSITION', Lang::txt($template), Lang::txt($label)) }}
                        @else
                          {{ Lang::txt($template) }}
                        @endif
                      </li>
                    @endforeach
                  </ul>
                </a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="2" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_MODULES_MSG_MANAGE_NO_MODULES') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <input type="hidden" name="task" value="positions" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="client_id" value="{{ $clientId }}" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="{{ $sort }}" />
    <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
    {!! Html::input('token') !!}

  </form>
</div>
