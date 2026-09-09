{{--
  mod_rapid_contact -- quick contact form

  Variables: $module, $params, $recipient, $replacement, $pre_text,
             $error, $name_label, $email_label, $subject_label,
             $message_label, $enable_anti_spam, $anti_spam_q,
             $button_text, $posted, $url, $mod_class_suffix

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="{{ $module->module }}">
  @if ($recipient === '')
    <div role="alert" class="alert alert-error">
      <span>{{ Lang::txt('MOD_RAPID_CONTACT_ERROR_NO_RECIPIENT') }}</span>
    </div>
  @else
    @php
      $formId = $module->module . '-form-' . $module->id;
    @endphp
    <form method="post"
          action="{{ $url }}"
          id="{{ $formId }}"
          class="{{ $mod_class_suffix }}">
      <fieldset class="fieldset">
        <legend class="fieldset-legend">
          {{ Lang::txt('MOD_RAPID_CONTACT_FORM') }}
        </legend>

        @if ($replacement)
          <div role="alert" class="alert alert-success my-2">
            <span>{{ $replacement }}</span>
          </div>
        @endif

        @if ($pre_text)
          <p class="my-2">{{ $pre_text }}</p>
        @endif

        @if ($error)
          <div role="alert" class="alert alert-error my-2">
            <span>{{ $error }}</span>
          </div>
        @endif

        {{-- Name --}}
        <div class="mt-3">
          <label class="label" for="contact-name{{ $module->id }}">
            {{ $name_label }}
          </label>
          <input type="text"
                 class="input input-bordered w-full"
                 id="contact-name{{ $module->id }}"
                 name="rp[name]"
                 value="{{ $posted['name'] }}" />
        </div>

        {{-- Email --}}
        <div class="mt-3">
          <label class="label" for="contact-email{{ $module->id }}">
            {{ $email_label }}
            <span class="badge badge-error badge-sm">{{ Lang::txt('JREQUIRED') }}</span>
          </label>
          <input type="email"
                 class="input input-bordered w-full"
                 id="contact-email{{ $module->id }}"
                 name="rp[email]"
                 required
                 value="{{ $posted['email'] }}" />
        </div>

        {{-- Subject --}}
        <div class="mt-3">
          <label class="label" for="contact-subject{{ $module->id }}">
            {{ $subject_label }}
          </label>
          <input type="text"
                 class="input input-bordered w-full"
                 id="contact-subject{{ $module->id }}"
                 name="rp[subject]"
                 value="{{ $posted['subject'] }}" />
        </div>

        {{-- Message --}}
        <div class="mt-3">
          <label class="label" for="contact-comments{{ $module->id }}">
            {{ $message_label }}
          </label>
          <textarea class="textarea textarea-bordered w-full"
                    name="rp[$message]"
                    id="contact-comments{{ $module->id }}"
                    $rows="6">{{ $posted['message'] ?? '' }}</textarea>
        </div>

        {{-- Anti-spam --}}
        @if ($enable_anti_spam)
          <div class="mt-3">
            <label class="label" for="contact-antispam{{ $module->id }}">
              {{ $anti_spam_q }}
              <span class="badge badge-error badge-sm">{{ Lang::txt('JREQUIRED') }}</span>
            </label>
            <input type="text"
                   class="input input-bordered w-full"
                   id="contact-antispam{{ $module->id }}"
                   name="rp[anti_spam_answer]"
                   required />
          </div>
        @endif

        <div class="mt-4">
          {!! Html::input('token') !!}
          <button type="submit" class="btn btn-primary">
            {{ $button_text }}
          </button>
        </div>
      </fieldset>
    </form>
  @endif
</div>
