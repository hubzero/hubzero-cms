{{--
  Newsletter / Mailing List $subscription module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if (is_object($mailinglist))
  <div class="mb-3">
    <span class="font-semibold">{{ $mailinglist->name }}</span>
    @if ($mailinglist->description)
      <p class="text-sm text-base-content/60 mt-0.5">
        {!! nl2br(e($mailinglist->description)) !!}
      </p>
    @endif
  </div>

  <form action="{{ Route::url('index.php?option=com_newsletter') }}" method="post">
    <fieldset>
      @php
        $token = Session::getFormToken();
      @endphp
      @if (is_object($subscription))
        @php
          $subscribeUrl = Route::url('index.php?option=com_newsletter&task=subscribe');
        @endphp
        <span class="text-sm">{!! Lang::txt('MOD_NEWSLETTER_ALREADY_SUBSCRIBED', $subscribeUrl) !!}</span>
      @else
        <label for="email" class="form-control w-full">
          <div class="label">
            <span class="label-text">
              {{ Lang::txt('MOD_NEWSLETTER_EMAIL') }}
              <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
            </span>
          </div>
          <input type="text"
                 name="email_{{ $token }}"
                 id="email"
                 value="{{ User::get('email') }}"
                 data-invalid="{{ Lang::txt('MOD_NEWSLETTER_EMAIL_INVALID') }}"
                 class="input input-bordered w-full" />
        </label>

        <label for="hp1_{{ $token }}" class="hidden">
          {{ Lang::txt('MOD_NEWSLETTER_HONEYPOT') }}
          <input type="text" name="hp1" id="hp1_{{ $token }}" value="" />
        </label>

        <div class="mt-3">
          <input type="submit"
                 value="{{ Lang::txt('MOD_NEWSLETTER_SIGN_UP') }}"
                 id="sign-up-submit"
                 class="btn btn-sm btn-primary" />
        </div>

        <input type="hidden" name="list_{{ $token }}" value="{{ $mailinglist->id }}" />
        <input type="hidden" name="option" value="com_newsletter" />
        <input type="hidden" name="controller" value="mailinglists" />
        <input type="hidden" name="subscriptionid" value="{{ $subscriptionId }}" />
        <input type="hidden" name="task" value="dosinglesubscribe" />
        <input type="hidden" name="return" value="{{ base64_encode($_SERVER['REQUEST_URI']) }}" />
        {!! Html::input('token') !!}
      @endif
    </fieldset>
  </form>
@else
  <div role="alert" class="alert alert-warning">
    <span>{{ Lang::txt('MOD_NEWSLETTER_SETUP_INCOMPLETE') }}</span>
  </div>
@endif
