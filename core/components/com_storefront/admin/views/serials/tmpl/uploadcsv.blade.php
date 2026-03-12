{{--
  Storefront Serial Numbers — CSV upload results (popup)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $returnUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&sId=' . $sId,
      false, false
  );
@endphp

<div class="p-4">
  <h3 class="text-lg font-semibold mb-4">
    {{ Lang::txt('COM_STOREFRONT_UPLOAD_SERIAL_NUMBERS_CSV') }}
  </h3>

  @if ($__view->getError())
    <div class="alert alert-error">
      {{ $__view->getError() }}
    </div>
  @else
    <div class="space-y-2">
      <p>
        {{ $inserted }} serial {{ $inserted == 1 ? 'number' : 'numbers' }} inserted.
      </p>

      @if (!empty($skipped))
        @php $skippedCount = count($skipped); @endphp
        <p>
          {{ $skippedCount }} duplicate serial {{ $skippedCount == 1 ? 'number' : 'numbers' }} skipped.
        </p>
      @endif

      @if (!empty($ignored))
        @php $ignoredCount = count($ignored); @endphp
        <p>
          {{ $ignoredCount }} serial {{ $ignoredCount == 1 ? 'number' : 'numbers' }}
          {{ $ignoredCount == 1 ? 'was' : 'were' }} ignored:
        </p>
        <ul class="list-disc list-inside text-sm">
          @foreach ($ignored as $ignore)
            <li>{{ $ignore }}</li>
          @endforeach
        </ul>
      @endif
    </div>
  @endif

  <div class="flex justify-end mt-4">
    <a href="{{ $returnUrl }}"
       class="btn btn-primary btn-sm"
       data-parent-redirect>
      {{ Lang::txt('JCLOSE') }}
    </a>
  </div>
</div>
