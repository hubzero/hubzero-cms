{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Event;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$__view->css();

$disabled = !empty($tos);

$autocomplete = Event::trigger(
    'hubzero.onGetMultiEntry',
    [['members', 'mbrs', 'members', '', $tos, '', $disabled]]
);
@endphp

<form action="{{ Route::url($member->link() . '&active=messages') }}"
      method="post"
      id="hubForm{{ $no_html ? '-ajax' : '' }}">
  <fieldset class="space-y-4">
    <legend class="text-lg font-semibold">
      {{ Lang::txt('PLG_MEMBERS_MESSAGES_COMPOSE_MESSAGE') }}
    </legend>

    <div class="form-control w-full">
      <label class="label" for="members">
        <span class="label-text">
          {{ Lang::txt('PLG_MEMBERS_MESSAGES_TO') }}
          <span class="text-error" aria-hidden="true">*</span>
        </span>
      </label>
      @if (count($autocomplete) > 0)
        {!! $autocomplete[0] !!}
      @else
        <input type="text"
               name="mbrs"
               id="members"
               value="{{ $tos }}"
               class="input input-bordered w-full"
               required
               aria-required="true" />
      @endif
    </div>

    <div class="form-control w-full">
      <label class="label" for="msg-subject">
        <span class="label-text">
          {{ Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT') }}
        </span>
      </label>
      <input type="text"
             name="subject"
             id="msg-subject"
             value="{{ e(Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT_MESSAGE')) }}"
             class="input input-bordered w-full" />
    </div>

    <div class="form-control w-full">
      <label class="label" for="msg-message">
        <span class="label-text">
          {{ Lang::txt('PLG_MEMBERS_MESSAGES_MESSAGE') }}
          <span class="text-error" aria-hidden="true">*</span>
        </span>
      </label>
      <textarea name="message"
                id="msg-message"
                rows="12"
                class="textarea textarea-bordered w-full"
                required
                aria-required="true"></textarea>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('PLG_MEMBERS_MESSAGES_SEND') }}
      </button>
    </div>
  </fieldset>

  <input type="hidden" name="id" value="{{ $member->get('id') }}" />
  <input type="hidden" name="task" value="view" />
  <input type="hidden" name="active" value="messages" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="action" value="send" />
  <input type="hidden" name="no_html" value="{{ $no_html }}" />

  {!! Html::input('token') !!}
</form>
