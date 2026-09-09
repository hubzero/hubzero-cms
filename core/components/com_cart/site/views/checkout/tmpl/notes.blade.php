{{--
  Checkout notes — notes/comments entry step.

  Variables from controller (notesTask):
    $noteFields    — array keyed by SKU ID with pName, sSku, sCheckoutNotes, sCheckoutNotesRequired
    $notifications — array of [message, type]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<x-page-container :title="Lang::txt('COM_CART_CHECKOUT_NOTES')">
  @if(!empty($checkoutSteps))
    <x-step-nav :steps="$checkoutSteps" :current="$currentStepIndex" />
  @endif

  <x-alert-list :notifications="$notifications" />

  <form name="notes" method="post" class="max-w-2xl">
    <x-form-section :heading="Lang::txt('COM_CART_CHECKOUT_NOTES')">
      @php
        $genericNotesLabel = Lang::txt('COM_CART_ADD_NOTES');
      @endphp

      @if(!empty($noteFields))
        @foreach($noteFields as $sId => $field)
          {{-- Per-SKU notes use raw label markup (compound bold+text+required) --}}
          <div class="form-field">
            <label class="form-field-label" for="notes-{{ $sId }}">
              <strong>{{ $field['pName'] }}, {{ $field['sSku'] }}:</strong>
              {{ $field['sCheckoutNotes'] }}
              @if($field['sCheckoutNotesRequired'])
                <span class="text-error">{{ Lang::txt('COM_CART_REQUIRED') }}</span>
              @endif
            </label>
            <textarea name="notes-{{ $sId }}" id="notes-{{ $sId }}"
                      class="textarea textarea-bordered w-full"
                      rows="3"></textarea>
          </div>
        @endforeach
        @php $genericNotesLabel = Lang::txt('COM_CART_OTHER_NOTES_LABEL'); @endphp
      @endif

      <x-form-field name="notes" :label="$genericNotesLabel">
        <textarea name="notes" id="notes"
                  class="textarea textarea-bordered w-full"
                  rows="3"></textarea>
      </x-form-field>

      <div class="mt-4">
        <button type="submit" name="submitNotes" id="submitNotes" class="btn btn-primary">
          {{ Lang::txt('COM_CART_NEXT') }}
        </button>
      </div>
    </x-form-section>
  </form>
</x-page-container>
