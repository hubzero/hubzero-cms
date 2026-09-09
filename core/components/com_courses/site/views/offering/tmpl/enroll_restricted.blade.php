{{--
  Enrollment restricted — coupon code redemption form.

  Variables from controller (enrollTask):
    $course        — Course model instance (with offering/section set)
    $notifications — Array of notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  $courseTitle = e(stripslashes($course->get('title')));

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Pathway::append($courseTitle, $course->link());
  Document::setTitle($courseTitle);

  $__view->css();

  $enrollUrl = Route::url($course->offering()->link() . '&task=enroll', false);
  $offeringAlias = e(
      $course->offering()->get('alias') . ':' . $course->offering()->section()->get('alias')
  );
@endphp

<x-page-container :title="$courseTitle">
  {{-- Notifications --}}
  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'error' ? 'error' : 'info' }} mb-4"
         role="alert">
      <span>{{ $notification['message'] }}</span>
    </div>
  @endforeach

  <div class="max-w-lg mx-auto py-8">
    <div class="alert alert-warning mb-6">
      {{ Lang::txt('COM_COURSES_ENROLLMENT_RESTRICTED') }}
    </div>

    <form action="{{ $enrollUrl }}" method="post" class="max-w-md">
      <x-form-section :heading="Lang::txt('COM_COURSES_REDEEM_COUPON_CODE')">
        <x-form-field
            name="code"
            inputId="field-code"
            :label="Lang::txt('COM_COURSES_FIELD_COUPON_CODE')"
            :required="true">
          <input type="text"
                 name="code"
                 id="field-code"
                 class="input input-bordered w-full"
                 value=""
                 required />
        </x-form-field>
      </x-form-section>

      <p class="text-sm text-base-content/70 mb-6">
        <strong>{{ Lang::txt('COM_COURSES_CODE_NOT_WORKING') }}</strong><br />
        {{ Lang::txt('COM_COURSES_CODE_NOT_WORKING_EXPLANATION') }}
      </p>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">
          {{ Lang::txt('COM_COURSES_REDEEM') }}
        </button>
      </div>

      <input type="hidden" name="offering" value="{{ $offeringAlias }}" />
      <input type="hidden" name="gid" value="{{ e($course->get('alias')) }}" />
      <input type="hidden" name="option" value="{{ e($option) }}" />
      <input type="hidden" name="controller" value="offering" />
      <input type="hidden" name="task" value="enroll" />
    </form>
  </div>
</x-page-container>
