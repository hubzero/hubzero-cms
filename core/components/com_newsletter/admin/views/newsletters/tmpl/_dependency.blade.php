{{--
  Newsletter dependency warning partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<div role="alert" class="alert alert-warning mb-4">
  <span>
    {{ Lang::txt('COM_NEWSLETTER_DEPENDENCY') }}
    <br />
    <a href="{!! Route::url('index.php?option=com_cron', false) !!}" class="link link-primary">
      {{ Lang::txt('COM_NEWSLETTER_CHECK_CRON') }}
    </a>
  </span>
</div>
