{{--
  Storefront SKU Restrictions — Add users results

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $formAction = Route::url('index.php?option=' . $option, false);
@endphp

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="component-form"
      class="p-4">
  <h3 class="text-lg font-semibold mb-4">
    {{ Lang::txt('COM_STOREFRONT_ADD_NEW_USERS') }}
  </h3>

  <div class="space-y-2">
    <p>
      <strong>{{ $matched }}</strong>
      {{ Lang::txt('COM_STOREFRONT_USERS_ADDED') }}
    </p>

    @if (count($noUserMatch))
      <p>
        <strong>{{ count($noUserMatch) }}</strong>
        {{ Lang::txt('COM_STOREFRONT_USERS_COULD_NOT_BE_ADDED') }}:
      </p>
      <p class="text-error">
        {{ implode(', ', $noUserMatch) }}
      </p>
    @endif
  </div>

  {!! Html::input('token') !!}
</form>
