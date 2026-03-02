{{--
  Logout sub-template.

  Displays a logout button for authenticated users, with optional
  description text and image from menu parameters.

  Loaded via $this->loadTemplate('logout') from the dispatcher.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // If the user is a guest (not logged in), redirect away
  if (User::isGuest()) {
      $returnUrl = base64_decode(Request::getString('return', ''));
      if ($returnUrl) {
          App::redirect(Route::url($returnUrl, false));
      } else {
          App::redirect(Route::url('index.php?option=com_users&view=login', false));
      }
      return;
  }

  $showLogoutDesc = $params->get('logoutdescription_show') == 1;
  $logoutDesc     = $params->get('logout_description');
  $logoutImage    = $params->get('logout_image');
  $logoutUrl      = Route::url('index.php?option=' . $option . '&task=user.logout');
  $returnVal      = base64_encode(
      $params->get('logout_redirect_url', $form->getValue('return'))
  );
@endphp

@if($params->get('show_page_heading', 1))
  <header class="page-header">
    <h1>{{ e($params->get('page_heading', Lang::txt('COM_USERS_LOGOUT'))) }}</h1>
  </header>
@endif

<section class="py-8">
  <div class="login-wrapper">
    <div class="login-card card bg-base-100 shadow-sm">
      <div class="card-body">

        @if($showLogoutDesc && trim($logoutDesc) !== '')
          <p class="mb-4">{{ e($logoutDesc) }}</p>
        @endif

        @if($logoutImage)
          <img src="{{ e($logoutImage) }}"
               class="mb-4 mx-auto"
               alt="{{ Lang::txt('COM_USERS_LOGOUT_IMAGE_ALT') }}" />
        @endif

        <form action="{{ $logoutUrl }}" method="post">
          <button type="submit" class="btn btn-primary w-full">
            {{ Lang::txt('JLOGOUT') }}
          </button>
          <input type="hidden" name="return" value="{{ e($returnVal) }}" />
          {!! Html::input('token') !!}
        </form>

      </div>
    </div>
  </div>
</section>
