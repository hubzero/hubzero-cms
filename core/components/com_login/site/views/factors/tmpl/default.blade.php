{{--
  Multi-factor authentication challenge.

  Displays MFA challenge forms rendered by authfactors plugins.

  Variables from controller (factorsTask):
    $factors — array: factor objects with ->html from event trigger
    $option  — string: 'com_login'

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<section class="py-8">
  <div class="login-wrapper">
    <div class="login-card card bg-base-100 shadow-sm">
      <div class="card-body">

        <div class="login-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
          </svg>
        </div>
        <h2 class="login-heading">
          {{ Lang::txt('COM_LOGIN_FACTOR_VERIFICATION') }}
        </h2>

        @foreach($factors as $factor)
          <div class="mb-4">
            {!! $factor->html !!}
          </div>
        @endforeach

      </div>
    </div>
  </div>
</section>
