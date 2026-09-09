{{--
  Registration — account creation result page.

  Shows activation instructions on success, or an error message on failure.

  Variables from controller:
    $title     — Page title (string)
    $xprofile  — User profile object
    $sitename  — Site name (string)
    $option    — Component option (string)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Utility\Str;

  $__view->css('register');
@endphp

<x-page-container :title="$title">

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      @if ($__view->getError())
        <div class="alert alert-error">
          <p>{{ Lang::txt('COM_MEMBERS_REGISTER_ERROR_OCCURRED') }}</p>
        </div>
      @else
        <div class="alert alert-success">
          <p>{{ Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_CREATED') }}</p>
        </div>
      @endif
    </div>

    <div>
      @if ($__view->getError())
        <div class="alert alert-error">{!! $__view->getError() !!}</div>
      @elseif ($xprofile->get('activation') < 0)
        <div class="account-activation">
          <div class="instructions">
            @php
              $createdMsg = Lang::txt(
                  'COM_MEMBERS_REGISTER_ACCOUNT_CREATED_MESSAGE',
                  $sitename,
                  Str::obfuscate($xprofile->get('email'))
              );
            @endphp
            <p>{!! $createdMsg !!}</p>
            <ol>
              <li>{!! Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_FIND_EMAIL') !!}</li>
              <li>{!! Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_ACTIVATE') !!}</li>
              <li>{!! Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_LOGIN') !!}</li>
              <li>{!! Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_SUCCESS') !!}</li>
            </ol>
          </div>
          <div class="notes">
            @php
              $supportUrl = Route::url('index.php?option=com_support');
              $noteMsg = Lang::txt(
                  'COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_NOTE',
                  $supportUrl
              );
            @endphp
            <p>{!! $noteMsg !!}</p>
          </div>
        </div>
      @endif
    </div>
  </div>

</x-page-container>
