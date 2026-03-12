{{--
  Citation Sponsor — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Citations\Helpers\Permissions::getActions('sponsor');
  $text  = $sponsor->get('id') ? Lang::txt('EDIT') : Lang::txt('NEW');

  Toolbar::title(Lang::txt('CITATIONS') . ' ' . Lang::txt('CITATION_SPONSORS') . ': ' . $text, 'citation');
  if ($canDo->get('core.edit')) {
      Toolbar::save();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('sponsor');


  $__view->js();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('CITATION_SPONSORS') }}">

      <div class="admin-field">
        <label for="field-sponsor" class="label">{{ Lang::txt('CITATION_SPONSORS_NAME') }}</label>
        <input type="text" name="sponsor[sponsor]" id="field-sponsor"
               class="input input-bordered w-full"
               value="{{ $sponsor->get('sponsor', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-link" class="label">{{ Lang::txt('CITATION_SPONSORS_LINK') }}</label>
        <input type="text" name="sponsor[link]" id="field-link"
               class="input input-bordered w-full"
               value="{{ $sponsor->get('link', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-image" class="label">{{ Lang::txt('CITATION_SPONSORS_IMAGE') }}</label>
        <input type="text" name="sponsor[image]" id="field-image"
               class="input input-bordered w-full"
               value="{{ $sponsor->get('image', '') }}" />
      </div>

  </x-admin-fieldset>

  {{-- Hidden fields --}}
  <input type="hidden" name="sponsor[id]" value="{{ $sponsor->get('id') }}" />
</x-admin-edit>
