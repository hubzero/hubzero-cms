{{--
  Member edit — Messaging tab (iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $msgUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=messages&tmpl=component&task=settings&id='
      . $profile->get('id') . '&t=' . time(), false
  );
@endphp

<x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_MENU_MESSAGING') }}">
  <iframe class="w-full border-0 rounded"
          height="500"
          name="messaging"
          id="messaging-settings"
          src="{!! $msgUrl !!}"></iframe>
</x-admin-fieldset>
