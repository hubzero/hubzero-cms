{{--
  ORCID — OAuth redirect landing page.

  Shows success (with or without manage permission) or error/deny state.

  Variables from controller:
    $userName          — User's display name (string)
    $userORCID         — User's ORCID iD (string)
    $permissionGranted — Whether manage permission was granted (bool|null)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
@endphp

<section class="main section">
  <div>
    @if (Request::getString('code'))

      <p>{{ Lang::txt('COM_MEMBERS_REDIRECT_ORCID_THANK_YOU', $userName) }}</p>

      @if (!empty($permissionGranted))
        <p>You have successfully granted permission to manage the ORCID record.</p>
      @else
        <p>
          {!! Lang::txt('COM_MEMBERS_REDIRECT_ORCID_YOUR_ORCID') !!}
          <img
            src="{{ Request::root() }}/core/components/com_members/site/assets/img/orcid_16x16.png"
            class="logo"
            width="16"
            height="16"
            alt="iD"
          />
          {!! Lang::txt('COM_MEMBERS_REDIRECT_ORCID_IS') !!}
          {{ e($userORCID) }}
        </p>
        <p>{!! Lang::txt('COM_MEMBERS_REDIRECT_ORCID_INDICATION_MESSAGE') !!}</p>
      @endif

    @elseif (Request::getString('error') && Request::getString('error_description'))

      <p>
        {!! Lang::txt('COM_MEMBERS_REDIRECT_ORCID_DENY') !!}
        <a class="btn" href="https://orcid.org/signin" rel="nofollow external">
          {{ Lang::txt('COM_MEMBERS_REDIRECT_ORCID_SIGN_IN_OR_REGISTER') }}
        </a>
      </p>

    @endif
  </div>
</section>
