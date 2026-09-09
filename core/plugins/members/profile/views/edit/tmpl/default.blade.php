{{--
  Member Profile — inline edit form partial.

  Variables (set by parent view):
    $isUser             — boolean
    $registration       — field registration state
    $registration_field — field name for registration
    $profile_field      — field name for profile
    $title              — field label
    $profile            — member profile object
    $inputs             — HTML for input fields
    $access             — HTML for access/privacy control

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

@if ($isUser)
  <div class="section-edit-container">
    @if ($registration == \Components\Members\Models\Profile\Field::STATE_READONLY)
      <p class="notice warning">{{ Lang::txt('PLG_MEMBERS_PROFILE_READONLY', $title) }}</p>
    @else
      <div class="section-edit-content">
        <form action="{{ Route::url('index.php?option=com_members') }}"
              method="post"
              data-section-registration="{{ $registration_field }}"
              data-section-profile="{{ $profile_field }}">
          <span class="section-edit-errors"></span>

          <div class="input-wrap">
            {!! $inputs !!}
          </div>
          @if ($access)
            <div class="input-wrap">
              {!! $access !!}
            </div>
          @endif

          <input type="submit"
                 class="section-edit-submit btn"
                 value="{{ Lang::txt('PLG_MEMBERS_PROFILE_SAVE') }}" />
          <input type="reset"
                 class="section-edit-cancel btn"
                 value="{{ Lang::txt('JCANCEL') }}" />
          <input type="hidden" name="field_to_check[]" value="{{ $registration_field }}" />
          <input type="hidden" name="option" value="com_members" />
          <input type="hidden" name="controller" value="profiles" />
          <input type="hidden" name="id" value="{{ $profile->get('id') }}" />
          <input type="hidden" name="task" value="save" />
          <input type="hidden" name="no_html" value="1" />
          {!! Html::input('token') !!}
        </form>
      </div>
    @endif
  </div>
@endif
