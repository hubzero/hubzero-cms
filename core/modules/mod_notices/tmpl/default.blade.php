{{--
  Notices module — dismissable alert banner, daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($publish)
  @php
    $alertClass = match ($alertlevel) {
        'critical' => 'alert-error',
        'warning'  => 'alert-warning',
        'info'     => 'alert-info',
        default    => 'alert-info',
    };
  @endphp
  <div id="{{ $moduleid }}" role="alert" class="alert {{ $alertClass }}">
    <div class="grow">
      {!! stripslashes($message) !!}
    </div>
    @if ($params->get('allowClose', 1))
      @php
        $page = Request::getString('REQUEST_URI', '', 'server');
        if ($page) {
            $page .= (strstr($page, '?')) ? '&' : '?';
            $page .= $moduleid . '=close';
            $page = htmlspecialchars($page, ENT_COMPAT, 'UTF-8');
        }
      @endphp
      @if ($page)
        <a href="{{ $page }}"
           data-$duration="{{ $days_left }}"
           $title="{{ Lang::txt('MOD_NOTICES_CLOSE_TITLE') }}"
           class="btn btn-sm btn-ghost">
          {{ Lang::txt('MOD_NOTICES_CLOSE') }}
        </a>
      @endif
    @endif
  </div>
@endif
