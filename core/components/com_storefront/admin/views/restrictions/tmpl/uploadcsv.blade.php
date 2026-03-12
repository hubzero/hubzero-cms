{{--
  Storefront SKU Restrictions — CSV upload results

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $redirectUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&id=' . $sId,
      false, false
  );
@endphp

<div class="p-4">
  <h3 class="text-lg font-semibold mb-4">
    {{ Lang::txt('COM_STOREFRONT_UPLOAD_FILE_WITH_USERS') }}
  </h3>

  @if ($__view->getError())
    <div class="alert alert-error">
      {{ $__view->getError() }}
    </div>
  @else
    <div class="space-y-2">
      <p>
        {{ $inserted }}
        {{ $inserted == 1
            ? Lang::txt('COM_STOREFRONT_USER_INSERTED')
            : Lang::txt('COM_STOREFRONT_USERS_INSERTED') }}
      </p>

      @if (!empty($skipped))
        @php $skippedCount = count($skipped); @endphp
        <p>
          {{ $skippedCount }}
          {{ Lang::txt('COM_STOREFRONT_DUPLICATE') }}
          {{ $skippedCount == 1
              ? Lang::txt('COM_STOREFRONT_USER_SKIPPED')
              : Lang::txt('COM_STOREFRONT_USERS_SKIPPED') }}
        </p>
      @endif

      @if (!empty($ignored))
        @php $ignoredCount = count($ignored); @endphp
        <p>
          {{ $ignoredCount }}
          {{ $ignoredCount == 1
              ? Lang::txt('COM_STOREFRONT_USER_COULD_NOT_BE_FOUND')
              : Lang::txt('COM_STOREFRONT_USERS_COULD_NOT_BE_FOUND') }}:
        </p>
        <p class="text-error">
          {{ implode(', ', $ignored) }}
        </p>
      @endif
    </div>
  @endif

  <div class="flex justify-end mt-4">
    <a href="{{ $redirectUrl }}"
       class="btn btn-primary btn-sm"
       data-parent-redirect>
      {{ Lang::txt('COM_STOREFRONT_CLOSE') }}
    </a>
  </div>
</div>
