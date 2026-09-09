{{--
  Subscription selection and payment form.

  Variables from controller (subscribeTask):
    $title        — Page title string
    $config       — Component params (Registry)
    $option       — Component option string
    $subscription — Subscription object
    $employer     — Employer object
    $services     — Services iterator
    $funds        — Available user funds
    $uid          — User ID
    $emp          — Employer flag
    $admin        — Admin flag
    $task         — Current task

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $loginUrl = Route::url('index.php?option=' . $option . '&task=view&action=login');
  $dashboardUrl = Route::url('index.php?option=' . $option . '&task=dashboard');
  $shortlistUrl = Route::url('index.php?option=' . $option . '&task=resumes') . '?filterby=shortlisted';
  $resumeUrl = Route::url('index.php?option=' . $option . '&task=addresume');
  $confirmUrl = Route::url('index.php?option=' . $option . '&task=confirm');

  $now = Date::toSql();

  $btn = $subscription->id
      ? Lang::txt('COM_JOBS_SUBSCRIPTION_SAVE')
      : Lang::txt('COM_JOBS_SUBSCRIPTION_PROCESS_ORDER');
@endphp

<x-page-container :title="$title" bodyClass="edit-form">
  @slot('actions')
    @if(User::isGuest())
      <span class="text-sm">
        {{ Lang::txt('COM_JOBS_PLEASE') }}
        <a class="link link-primary" href="{{ $loginUrl }}">{{ Lang::txt('COM_JOBS_ACTION_LOGIN') }}</a>
        {{ Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS') }}
      </span>
    @elseif($emp && $config->get('allowsubscriptions', 0))
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD') }}</a>
      <a class="btn" href="{{ $shortlistUrl }}">{{ Lang::txt('COM_JOBS_SHORTLIST') }}</a>
    @elseif($admin)
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_ADMIN_DASHBOARD') }}</a>
    @else
      <a class="btn" href="{{ $resumeUrl }}">{{ Lang::txt('COM_JOBS_MY_RESUME') }}</a>
    @endif
  @endslot

  @if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ $__view->getError() }}</span>
    </div>
  @endif

  <form action="{{ $confirmUrl }}" method="post" id="hubForm">
    <x-form-section :heading="Lang::txt('COM_JOBS_SUBSCRIPTION_EMPLOYER_INFORMATION')">
      <x-form-field name="companyName"
                    :label="Lang::txt('COM_JOBS_EMPLOYER_COMPANY_NAME')"
                    :required="true">
        <input type="text" class="input input-bordered w-full"
               name="companyName" id="companyName" maxlength="100"
               value="{{ e($employer->companyName) }}" required />
      </x-form-field>

      <x-form-field name="companyLocation"
                    :label="Lang::txt('COM_JOBS_EMPLOYER_COMPANY_LOCATION')"
                    :required="true">
        <input type="text" class="input input-bordered w-full"
               name="companyLocation" id="companyLocation" maxlength="200"
               value="{{ e($employer->companyLocation) }}" required />
      </x-form-field>

      <x-form-field name="companyWebsite"
                    :label="Lang::txt('COM_JOBS_EMPLOYER_COMPANY_WEBSITE')">
        <input type="text" class="input input-bordered w-full"
               name="companyWebsite" id="companyWebsite" maxlength="200"
               value="{{ e($employer->companyWebsite) }}" />
      </x-form-field>
    </x-form-section>

    <x-form-section :heading="Lang::txt('COM_JOBS_SUBSCRIPTION_DETAILS')">
      <p class="text-sm text-base-content/60 mb-4">
        {{ Lang::txt('COM_JOBS_SUBSCRIBE_SELECT_SERVICE') }}
        <span class="text-error">*</span>
      </p>

      <div class="space-y-4">
        @php $services->rewind(); @endphp
        @while($services->valid())
          @php
            $svc = $services->current();
            $thissub = ($svc->id == $subscription->serviceid) ? 1 : 0;

            // Build unit selector options
            $units_select = [];
            $numunits = $svc->maxunits / $svc->unitsize;
            $unitsize = $svc->unitsize;
            if ($thissub) {
                $units_select[0] = 0;
            }
            for ($p = 1; $p <= $numunits; $p++) {
                $units_select[$unitsize] = $unitsize;
                $unitsize = $unitsize + $svc->unitsize;
            }

            $iniprice = $thissub ? 0 : $svc->unitprice;
          @endphp

          <div @class([
              'card bg-base-100 shadow-sm',
              'border-2 border-primary' => $thissub,
          ])>
            <div class="card-body p-4">
              <label class="flex items-start gap-3 cursor-pointer">
                <input type="radio" name="serviceid" value="{{ $svc->id }}"
                       class="radio radio-primary mt-1"
                       {{ $thissub || ($subscription->serviceid == 0 && $services->key() == 1) ? 'checked' : '' }} />
                <div class="flex-1">
                  <span class="font-semibold">{{ $svc->title }}</span>
                  <span class="text-sm text-base-content/60">
                    &mdash; {{ $svc->currency }} {{ $svc->unitprice }}
                    {{ Lang::txt('COM_JOBS_PER') }} {{ $svc->unitmeasure }}
                  </span>
                  @if($svc->description)
                    <p class="text-sm text-base-content/70 mt-1">{{ $svc->description }}</p>
                  @endif

                  @if($thissub)
                    @php
                      $length = $subscription->status == 0
                          ? $subscription->pendingunits
                          : $subscription->units;
                    @endphp
                    <div class="mt-2">
                      @if($subscription->status == 1)
                        @php
                          $isActive = $subscription->expires > $now;
                          $expText = $isActive
                              ? strtolower(Lang::txt('COM_JOBS_SUBSCRIPTION_STATUS_EXPIRES'))
                              : strtolower(Lang::txt('COM_JOBS_SUBSCRIPTION_STATUS_EXPIRED'));
                          $expiresDate = Date::of($subscription->expires)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                        @endphp
                        <p @class(['text-sm', 'text-success' => $isActive, 'text-error' => !$isActive])>
                          {{ Lang::txt('COM_JOBS_YOUR') }}
                          {{ $length }}-{{ $svc->unitmeasure }}
                          {{ Lang::txt('COM_JOBS_SUBSCRIPTION') }}
                          {{ $expText }}
                          {{ Lang::txt('COM_JOBS_ON') }} {{ $expiresDate }}.
                        </p>
                      @else
                        <p class="text-sm text-warning">
                          {{ Lang::txt('COM_JOBS_YOUR') }}
                          {{ $length }}-{{ $svc->unitmeasure }}
                          {{ Lang::txt('COM_JOBS_SUBSCRIPTION') }}
                          {{ Lang::txt('COM_JOBS_SUBSCRIPTION_IS_PENDING') }}
                        </p>
                      @endif
                    </div>
                  @endif

                  <div class="mt-3 flex flex-wrap items-center gap-2">
                    <label class="text-sm">
                      {{ $thissub ? Lang::txt('COM_JOBS_SUBSCRIPTION_EXTEND_OR_RENEW') : Lang::txt('COM_JOBS_ACTION_SIGN_UP') }}
                      {{ Lang::txt('COM_JOBS_FOR', 'for') }}
                    </label>
                    {!! \Components\Jobs\Helpers\Html::formSelect(
                        'units_' . $svc->id,
                        $units_select,
                        '',
                        'select select-bordered select-sm'
                    ) !!}
                    <span class="text-sm">{{ $svc->unitmeasure }}(s)</span>
                  </div>

                  <p class="text-sm mt-2">
                    {{ Lang::txt('COM_JOBS_SUBSCRIBE_YOUR_TOTAL') }}
                    @if($thissub) {{ strtolower(Lang::txt('COM_JOBS_NEW')) }} @endif
                    {{ Lang::txt('COM_JOBS_SUBSCRIBE_PAYMENT_WILL_BE') }}
                    <strong>{{ $svc->currency }}</strong>
                    <span id="injecttotal_{{ $svc->id }}">{{ $iniprice }}</span>
                  </p>

                  <input type="hidden" class="product-price" value="{{ e($svc->unitprice) }}" />
                  <input type="hidden" class="product-title" value="{{ e($svc->title) }}" />
                </div>
              </label>
            </div>
          </div>

          <input type="hidden" name="price_{{ $svc->id }}" id="price_{{ $svc->id }}"
                 value="{{ e($svc->unitprice) }}" />

          @php $services->next(); @endphp
        @endwhile
      </div>

      <x-form-field name="contact"
                    :label="Lang::txt('COM_JOBS_SUBSCRIPTION_CONTACT_PHONE')"
                    :hint="Lang::txt('COM_JOBS_REQUIRED_WITH_PAYMENT')">
        <input type="text" class="input input-bordered w-full"
               name="contact" id="contact" maxlength="15"
               value="{{ e($subscription->contact) }}" />
      </x-form-field>

      <input type="hidden" name="subid" value="{{ $employer->subscriptionid }}" />
      <input type="hidden" name="uid" value="{{ $uid }}" />
    </x-form-section>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">{{ $btn }}</button>
      <a class="btn btn-ghost" href="{{ $dashboardUrl }}">{{ Lang::txt('JCANCEL') }}</a>
    </div>
  </form>
</x-page-container>
