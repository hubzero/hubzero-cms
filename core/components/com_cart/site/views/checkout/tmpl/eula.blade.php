{{--
  Checkout EULA — user agreement acceptance step.

  Variables from controller (eulaTask):
    $productInfo   — object with pName, oName
    $productEula   — HTML string of EULA text
    $notifications — array of [message, type]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $product = $productInfo->pName . ', ' . $productInfo->oName;
  $cartUrl = Route::url('index.php?option=com_cart', false);
@endphp

<x-page-container :title="Lang::txt('COM_CART_CHECKOUT_EULA')">
  @if(!empty($checkoutSteps))
    <x-step-nav :steps="$checkoutSteps" :current="$currentStepIndex" />
  @endif

  <x-alert-list :notifications="$notifications" />

  <p class="mb-4">
    {!! Lang::txt('COM_CART_EULA_INTRO', e($product)) !!}
  </p>

  <form name="eula" method="post" class="max-w-2xl">
    <x-form-section :heading="Lang::txt('COM_CART_EULA_READ')">
      <div class="border border-base-300 rounded-lg p-4 max-h-96 overflow-y-auto bg-base-200/50 mb-4">
        {!! $productEula !!}
      </div>

      <p class="font-medium mb-2">{{ Lang::txt('COM_CART_EULA_CONFIRM') }}</p>

      <x-form-field name="acceptEula" :label="Lang::txt('COM_CART_ACCEPT')" type="checkbox">
        <input type="checkbox" class="checkbox" name="acceptEula" id="acceptEula" />
      </x-form-field>

      <p class="text-sm text-base-content/60 mt-2">
        {!! Lang::txt('COM_CART_EULA_CANCEL', $cartUrl) !!}
      </p>

      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />

      <div class="mt-4">
        <button type="submit" name="submitEula" id="submitEula" class="btn btn-primary">
          {{ Lang::txt('COM_CART_NEXT') }}
        </button>
      </div>
    </x-form-section>
  </form>
</x-page-container>
