{{--
  Login required — displays login message and login module.

  Variables from controller:
    $title — page title

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<x-page-container :title="$title">
  <div role="alert" class="alert alert-warning mb-4">
    <span>{{ Lang::txt('COM_CART_NOT_LOGGEDIN') }}</span>
  </div>
  {!! \Hubzero\Module\Helper::displayModules('force_mod') !!}
</x-page-container>
