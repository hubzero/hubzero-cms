{{--
  Member Profile — address display partial.

  Variables (set by parent view):
    $addresses        — array of address objects
    $displayEditLinks — boolean

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<div class="grid cf">
  @if (count($addresses) < 1)
    <div class="col span4">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_ENTER') }}
    </div>
  @else
    @foreach ($addresses as $k => $address)
      <div class="col span4{{ ($k + 1) % 3 == 0 ? ' omega' : '' }}">
        @if (!empty($address->addressTo))
          <strong>{{ $address->addressTo }}</strong><br />
        @endif

        @if (!empty($address->address1))
          {{ $address->address1 }}<br />
        @endif

        @if (!empty($address->address2))
          {{ $address->address2 }}<br />
        @endif

        {{ $address->addressCity }}
        {{ $address->addressRegion }},
        {{ $address->addressPostal }}<br />

        @if (
          !empty($address->addressCountry)
          && !in_array($address->addressCountry, ['US', 'USA', 'United States', 'United States of America'])
        )
          {{ $address->addressCountry }}<br />
        @endif

        @if ($displayEditLinks)
          <span class="address-links">
            <a class="edit edit-address"
               href="{{ Route::url($profile->link() . '&active=profile&action=editaddress&addressid=' . $address->id) }}">
              {{ Lang::txt('JACTION_EDIT') }}
            </a>
            |
            <a class="delete delete-address"
               href="{{ Route::url($profile->link() . '&active=profile&action=deleteaddress&addressid=' . $address->id) }}">
              {{ Lang::txt('JACTION_DELETE') }}
            </a>
          </span>
        @endif
      </div>

      @if (($k + 1) % 3 == 0 && count($addresses) > 3)
        </div>
        <div class="grid cf">
      @endif
    @endforeach
  @endif
</div>
