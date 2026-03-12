{{--
  com_menus — Menu type picker (component iframe)

  Variables: $types (assoc array: groupName => [items]), $recordId (int)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $__view->js('menutypes.blade.js');
@endphp

{{-- Sticky search header --}}
<div class="admin-picker-header">
  <h2 class="text-base font-semibold shrink-0">{{ Lang::txt('COM_MENUS_TYPE_CHOOSE') }}</h2>
  <input
    id="type-search"
    type="search"
    class="admin-picker-search"
    placeholder="{{ Lang::txt('JSEARCH_FILTER_LABEL') }}"
    autocomplete="off"
  />
  <button type="button" class="admin-picker-close" data-picker-close
          aria-label="{{ Lang::txt('JCLOSE') }}">
    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
    </svg>
  </button>
</div>

{{-- Accordion grid --}}
<div class="p-4 admin-picker-grid">

  @foreach($types as $name => $list)
    <details class="type-group collapse collapse-arrow bg-base-200 rounded-box admin-picker-card">
      <summary class="collapse-title text-sm font-semibold min-h-0 py-2.5 px-3 cursor-pointer">
        {{ Lang::txt($name) }}
        <span class="text-xs font-normal text-muted-foreground ml-1">({{ count($list) }})</span>
      </summary>
      <div class="collapse-content px-2 pb-2 pt-0">
        <ul class="menu menu-xs p-0 w-full">
          @foreach($list as $typeItem)
            @php
              $itemData = base64_encode(json_encode([
                  'id'      => $recordId,
                  'title'   => $typeItem->title,
                  'request' => $typeItem->request,
              ]));
            @endphp
            <li class="type-item">
              <a href="#"
                 title="{{ Lang::txt($typeItem->description) }}"
                 data-menutype="{{ $itemData }}">
                {{ Lang::txt($typeItem->title) }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </details>
  @endforeach

  {{-- System types --}}
  <details class="type-group collapse collapse-arrow bg-base-200 rounded-box admin-picker-card">
    <summary class="collapse-title text-sm font-semibold min-h-0 py-2.5 px-3 cursor-pointer">
      {{ Lang::txt('COM_MENUS_TYPE_SYSTEM') }}
      <span class="text-xs font-normal text-muted-foreground ml-1">(3)</span>
    </summary>
    <div class="collapse-content px-2 pb-2 pt-0">
      <ul class="menu menu-xs p-0 w-full">
        <li class="type-item">
          @php $urlData = base64_encode(json_encode(['id' => $recordId, 'title' => 'url'])); @endphp
          <a href="#"
             title="{{ Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL_DESC') }}"
             data-menutype="{{ $urlData }}">
            {{ Lang::txt('COM_MENUS_TYPE_EXTERNAL_URL') }}
          </a>
        </li>
        <li class="type-item">
          @php $aliasData = base64_encode(json_encode(['id' => $recordId, 'title' => 'alias'])); @endphp
          <a href="#"
             title="{{ Lang::txt('COM_MENUS_TYPE_ALIAS_DESC') }}"
             data-menutype="{{ $aliasData }}">
            {{ Lang::txt('COM_MENUS_TYPE_ALIAS') }}
          </a>
        </li>
        <li class="type-item">
          @php $sepData = base64_encode(json_encode(['id' => $recordId, 'title' => 'separator'])); @endphp
          <a href="#"
             title="{{ Lang::txt('COM_MENUS_TYPE_SEPARATOR_DESC') }}"
             data-menutype="{{ $sepData }}">
            {{ Lang::txt('COM_MENUS_TYPE_SEPARATOR') }}
          </a>
        </li>
      </ul>
    </div>
  </details>

</div>
