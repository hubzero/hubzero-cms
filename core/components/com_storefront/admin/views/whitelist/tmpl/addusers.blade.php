{{--
  Storefront Whitelist — Add users results (popup)

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

<form action="{{ $formAction }}" method="post" name="adminForm" id="component-form"
      class="p-4">
  <h3 class="text-lg font-semibold mb-4">
    {{ Lang::txt('Add new users') }}
  </h3>

  <div class="mb-4">
    <p>
      <strong>{{ $matched }}</strong> user(s) added.
    </p>

    @if (count($noUserMatch) > 0)
      <p>
        <strong>{{ count($noUserMatch) }}</strong> user(s) could not be added
        (no matching users):<br>
        {{ implode(', ', $noUserMatch) }}
      </p>
    @endif
  </div>

  {!! Html::input('token') !!}
</form>
