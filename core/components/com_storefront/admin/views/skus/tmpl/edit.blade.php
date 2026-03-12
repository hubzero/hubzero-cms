{{--
  Storefront SKU — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');
  $text  = ($task == 'edit')
      ? Lang::txt('COM_STOREFRONT_EDIT')
      : Lang::txt('COM_STOREFRONT_NEW');

  $skuMeta = $row->getMeta();

  $inventoryNotificationThreshold = '';
  if (!empty($skuMeta['inventoryNotificationThreshold'])) {
      $inventoryNotificationThreshold = $skuMeta['inventoryNotificationThreshold'];
  }

  $showInventoryOptions = true;
  if (
      $pInfo->ptModel == 'software'
      && isset($skuMeta['serialManagement'])
      && $skuMeta['serialManagement'] == 'multiple'
  ) {
      $showInventoryOptions = false;
  }

  $publishUp    = $row->getPublishTime()->publish_up;
  $publishUpVal = ($publishUp && $publishUp != '0000-00-00 00:00:00')
      ? e(Date::of($publishUp)->toLocal('Y-m-d H:i:s'))
      : '';

  $publishDown    = $row->getPublishTime()->publish_down;
  $publishDownVal = ($publishDown && $publishDown != '0000-00-00 00:00:00')
      ? e(Date::of($publishDown)->toLocal('Y-m-d H:i:s'))
      : '';

  $productSlug = !empty($pInfo->pAlias) ? $pInfo->pAlias : $pInfo->pId;
  $directUrl   = Request::root() . 'storefront/product/' . $productSlug;
  if (!empty($options)) {
      $directUrl .= '/' . implode(',', $options);
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_STOREFRONT') }}: {{ Lang::txt('COM_STOREFRONT_SKU') }}: {{ $text }}"
    icon="storefront"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_DETAILS') }}">

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_STOREFRONT_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[sSku]"
               id="field-title"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               required
               value="{{ $row->getName() }}" />
      </div>

      <div class="admin-field">
        <label for="field-price" class="label">
          {{ Lang::txt('COM_STOREFRONT_PRICE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[sPrice]"
               id="field-price"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               required
               value="{{ $row->getPrice() }}" />
      </div>

      @if ($pInfo->ptId == 1)
        <div class="admin-field">
          <label for="field-weight" class="label">
            {{ Lang::txt('COM_STOREFRONT_WEIGHT') }}
          </label>
          <input type="text"
                 name="fields[sWeight]"
                 id="field-weight"
                 class="input input-bordered input-sm w-full"
                 maxlength="100"
                 value="{{ $row->getWeight() }}" />
        </div>
      @endif

  </x-admin-fieldset>

  {{-- Checkout Options --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_CHECKOUT_OPTIONS') }}">

      <div class="admin-field">
        <label for="field-checkoutNotes" class="label">
          {{ Lang::txt('COM_STOREFRONT_CHECKOUT_NOTES_MESSAGE') }}
        </label>
        <input type="text"
               name="fields[checkoutNotes]"
               id="field-checkoutNotes"
               class="input input-bordered input-sm w-full"
               maxlength="100"
               value="{{ $row->getCheckoutNotes() }}" />
      </div>

      <div class="admin-field">
        <label for="field-checkoutNotesRequired" class="label">
          {{ Lang::txt('COM_STOREFRONT_CHECKOUT_NOTES_REQUIRED') }}
        </label>
        <select name="fields[checkoutNotesRequired]"
                id="field-checkoutNotesRequired"
                class="select select-bordered select-sm w-full">
          <option value="0" @selected($row->getCheckoutNotesRequired() == 0)>
            {{ Lang::txt('COM_STOREFRONT_NO') }}
          </option>
          <option value="1" @selected($row->getCheckoutNotesRequired() == 1)>
            {{ Lang::txt('COM_STOREFRONT_YES') }}
          </option>
        </select>
      </div>

  </x-admin-fieldset>

  {{-- Product Options --}}
  @if (!empty($allOptions))
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_PRODUCT_OPTIONS') }}">

        @foreach ($allOptions as $optionGroup)
          <div class="admin-field">
            <label for="field-options-{{ $optionGroup->ogId }}" class="label">
              {{ $optionGroup->ogName }}
              <span class="text-error">*</span>
            </label>

            @php
              $optionsToDisplay = false;
              foreach ($optionGroup->options as $opt) {
                  if ($opt->oActive || in_array($opt->oId, $options)) {
                      $optionsToDisplay = true;
                  }
              }
            @endphp

            @if ($optionsToDisplay)
              <select name="fields[options][]"
                      id="field-options-{{ $optionGroup->ogId }}"
                      class="select select-bordered select-sm w-full">
                <option value="">-- {{ Lang::txt('COM_STOREFRONT_SELECT_OPTION') }} --</option>
                @foreach ($optionGroup->options as $opt)
                  @if ($opt->oActive || in_array($opt->oId, $options))
                    <option value="{{ $opt->oId }}"
                            @selected(in_array($opt->oId, $options))>
                      {{ $opt->oName }}
                    </option>
                  @endif
                @endforeach
              </select>
            @else
              @php
                $ogAdminUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=options&task=display&id=' . $optionGroup->ogId, false
                );
              @endphp
              <p class="text-warning text-sm">
                {{ Lang::txt('COM_STOREFRONT_NO_OPTIONS_AVAILABLE') }}
                <a href="{{ $ogAdminUrl }}">
                  {{ $optionGroup->ogName }} {{ Lang::txt('COM_STOREFRONT_OPTIONS_ADMIN') }}
                </a>
              </p>
            @endif
          </div>
        @endforeach

    </x-admin-fieldset>
  @endif

  {{-- Software meta --}}
  @if ($pInfo->ptModel == 'software')
    @include('com_storefront::admin.views.meta.tmpl._sku-software', [
        'skuMeta' => $skuMeta,
        'row'     => $row,
        'option'  => $option,
    ])
  @endif

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_STOREFRONT_ID') }}</td>
              <td>{{ $row->getId() ?: Lang::txt('JNEW') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_STOREFRONT_PRODUCT') }}</td>
              <td>{{ $pInfo->pName }}</td>
            </tr>
            @if ($pInfo->ptModel == 'software')
              <tr>
                <td>{{ Lang::txt('COM_STOREFRONT_DOWNLOADED') }}</td>
                <td>
                  {{ $downloaded }}
                  {{ $downloaded == 1 ? 'time' : 'times' }}
                </td>
              </tr>
            @endif
            <tr>
              <td>{{ Lang::txt('COM_STOREFRONT_DIRECT_URL') }}</td>
              <td class="break-all text-xs">{{ $directUrl }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Options --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_OPTIONS') }}">

        <div class="admin-field">
          <label for="field-sAllowMultiple" class="label">
            {{ Lang::txt('COM_STOREFRONT_ALLOW_MULTIPLE') }}
          </label>
          <select name="fields[sAllowMultiple]"
                  id="field-sAllowMultiple"
                  class="select select-bordered select-sm w-full">
            <option value="0" @selected($row->getAllowMultiple() == 0)>
              {{ Lang::txt('COM_STOREFRONT_NO') }}
            </option>
            <option value="1" @selected($row->getAllowMultiple() == 1)>
              {{ Lang::txt('COM_STOREFRONT_YES') }}
            </option>
          </select>
        </div>

        @if ($showInventoryOptions)
          <div class="admin-field">
            <label for="field-sTrackInventory" class="label">
              {{ Lang::txt('COM_STOREFRONT_TRACK_INVENTORY') }}
            </label>
            <select name="fields[sTrackInventory]"
                    id="field-sTrackInventory"
                    class="select select-bordered select-sm w-full">
              <option value="0" @selected($row->getTrackInventory() == 0)>
                {{ Lang::txt('COM_STOREFRONT_NO') }}
              </option>
              <option value="1" @selected($row->getTrackInventory() == 1)>
                {{ Lang::txt('COM_STOREFRONT_YES') }}
              </option>
            </select>
            <p class="text-xs text-muted-foreground mt-1">
              {{ Lang::txt('COM_STOREFRONT_TRACK_INVENTORY_HINT') }}
            </p>
          </div>

          <div class="admin-field">
            <label for="field-inventory" class="label">
              {{ Lang::txt('COM_STOREFRONT_INVENTORY') }}
            </label>
            <input type="text"
                   name="fields[sInventory]"
                   id="field-inventory"
                   class="input input-bordered input-sm w-full"
                   maxlength="10"
                   value="{{ $row->getInventoryLevel() }}" />
            <p class="text-xs text-muted-foreground mt-1">
              {{ Lang::txt('COM_STOREFRONT_INVENTORY_HINT') }}
            </p>
          </div>
        @endif

        <div class="admin-field">
          <label for="field-inventory-notification-threshold" class="label">
            {{ Lang::txt('COM_STOREFRONT_INVENTORY_NOTIFICATION_THRESHOLD') }}
          </label>
          <input type="text"
                 name="fields[meta][inventoryNotificationThreshold]"
                 id="field-inventory-notification-threshold"
                 class="input input-bordered input-sm w-full"
                 maxlength="10"
                 value="{{ $inventoryNotificationThreshold }}" />
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_STOREFRONT_INVENTORY_NOTIFICATION_HINT') }}
          </p>
        </div>

    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_PUBLISH_OPTIONS') }}">

        <div class="admin-field">
          <label for="field-state" class="label">
            {{ Lang::txt('COM_STOREFRONT_STATE') }}
          </label>
          <select name="fields[state]"
                  id="field-state"
                  class="select select-bordered select-sm w-full">
            <option value="0" @selected($row->getActiveStatus() == 0)>
              {{ Lang::txt('JUNPUBLISHED') }}
            </option>
            <option value="1" @selected($row->getActiveStatus() == 1)>
              {{ Lang::txt('JPUBLISHED') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-publish_up" class="label">
            {{ Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_UP') }}
          </label>
          {!! Html::input('calendar', 'fields[publish_up]', $publishUpVal, ['id' => 'field-publish_up']) !!}
        </div>

        <div class="admin-field">
          <label for="field-publish_down" class="label">
            {{ Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_DOWN') }}
          </label>
          {!! Html::input('calendar', 'fields[publish_down]', $publishDownVal, ['id' => 'field-publish_down']) !!}
        </div>

    </x-admin-fieldset>

    {{-- Restrictions --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_RESTRICTIONS') }}">

        <div class="admin-field">
          <label for="field-restricted" class="label">
            {{ Lang::txt('COM_STOREFRONT_RESTRICT_BY_USERS') }}
          </label>
          <select name="fields[restricted]"
                  id="field-restricted"
                  class="select select-bordered select-sm w-full">
            <option value="0" @selected($row->getRestricted() == 0)>
              {{ Lang::txt('COM_STOREFRONT_NO') }}
            </option>
            <option value="1" @selected($row->getRestricted() == 1)>
              {{ Lang::txt('COM_STOREFRONT_YES') }}
            </option>
          </select>
        </div>

        @if ($row->getRestricted())
          @php
            $restrictUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=restrictions&id=' . $row->getId(), false
            );
          @endphp
          <p>
            <a href="{{ $restrictUrl }}">
              {{ Lang::txt('COM_STOREFRONT_MANAGE_RESTRICTIONS') }}
            </a>
          </p>
        @endif

    </x-admin-fieldset>

    {{-- Whitelist --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_STOREFRONT_WHITELIST') }}">

        <p class="text-sm text-muted-foreground">
          {{ Lang::txt('COM_STOREFRONT_WHITELIST_DESC') }}
        </p>

        @php
          $whitelistUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=whitelist&id=' . $row->getId(), false
          );
        @endphp
        <p>
          <a href="{{ $whitelistUrl }}">
            {{ Lang::txt('COM_STOREFRONT_MANAGE_WHITELIST') }}
          </a>
        </p>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[sId]" value="{{ $row->getId() }}" />
  <input type="hidden" name="pId" value="{{ $pInfo->pId }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
