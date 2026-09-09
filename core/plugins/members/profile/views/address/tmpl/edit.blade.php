{{--
  Member Profile — address edit form.

  Variables (set by parent view):
    $member    — member model
    $address   — address object
    $addressId — address ID

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css();
  $__view->js();

  $countries = \Hubzero\Geocode\Geocode::countries();
  $noHtml = Request::getInt('no_html', 0);
  $formId = $noHtml ? 'hubForm-ajax' : 'hubForm';
  $legendTxt = ($address->id != 0)
      ? Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_EDIT')
      : Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_ADD');
  $manageUrl = Route::url(
      'index.php?option=com_members&id=' . $member->get('id')
      . '&active=profile&action=manageaddresses'
  );
@endphp

@if ($__view->getError())
  <p class="error">{{ $__view->getError() }}</p>
@endif

<form action="{{ Route::url('index.php?option=com_members') }}"
      method="post"
      id="{{ $formId }}"
      class="member-address-form">
  @if (!$noHtml)
    <div class="explaination">
      <h3>{{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE') }}</h3>
      <p>{{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE_EXPLANATION') }}</p>
      <p>
        <a class="btn" href="{{ $manageUrl }}">
          {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_MANAGE') }}
        </a>
      </p>
    </div>
  @endif

  <fieldset>
    <legend>{{ $legendTxt }}</legend>

    <label for="addressTo">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_TO') }}
      <input type="text"
             name="address[addressTo]"
             id="addressTo"
             value="{{ e($address->addressTo) }}" />
    </label>

    <label for="address1">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_LINE1') }}
      <input type="text"
             name="address[address1]"
             id="address1"
             value="{{ e($address->address1) }}" />
    </label>

    <label for="address2">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_LINE2') }}
      <input type="text"
             name="address[address2]"
             id="address2"
             value="{{ e($address->address2) }}" />
    </label>

    <label for="addressCity">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_CITY') }}
      <input type="text"
             name="address[addressCity]"
             id="addressCity"
             value="{{ e($address->addressCity) }}" />
    </label>

    <label for="addressRegion">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_PROVINCE') }}
      <input type="text"
             name="address[addressRegion]"
             id="addressRegion"
             value="{{ e($address->addressRegion) }}" />
    </label>

    <label for="addressPostal">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_POSTALCODE') }}
      <input type="text"
             name="address[addressPostal]"
             id="addressPostal"
             value="{{ e($address->addressPostal) }}" />
    </label>

    <label for="addressCountry">
      {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_COUNTRY') }}
      <select name="address[addressCountry]" id="addressCountry">
        <option value="">{{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_SELECT_COUNTRY') }}</option>
        @foreach ($countries as $country)
          <option value="{{ $country->name }}"
                  @if ($country->name == $address->addressCountry) selected @endif>
            {{ e($country->name) }}
          </option>
        @endforeach
      </select>
    </label>

    <input type="hidden" name="address[addressLatitude]"
           id="addressLatitude" value="{{ e($address->addressLatitude) }}" />
    <input type="hidden" name="address[addressLongitude]"
           id="addressLongitude" value="{{ e($address->addressLongitude) }}" />
  </fieldset>

  <p class="submit">
    <input type="submit" value="{{ Lang::txt('PLG_MEMBERS_PROFILE_SAVE') }}" />
  </p>

  <input type="hidden" name="option" value="com_members" />
  <input type="hidden" name="id" value="{{ $member->get('id') }}" />
  <input type="hidden" name="active" value="profile" />
  <input type="hidden" name="action" value="saveaddress" />
  <input type="hidden" name="address[id]" value="{{ $addressId }}" />
  {!! Html::input('token') !!}
</form>
