{{--
  Member edit — Groups tab (iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $groupsUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=groups&tmpl=component&id='
      . $profile->get('id') . '&t=' . time(), false
  );
@endphp

<x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_GROUPS') }}">
  <iframe class="w-full border-0 rounded"
          height="500"
          name="grouper"
          id="grouper"
          src="{!! $groupsUrl !!}"></iframe>
</x-admin-fieldset>
