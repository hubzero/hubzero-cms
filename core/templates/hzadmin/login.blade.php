{{--
  Admin Login Page Shell — hzadmin Template

  Full-page dark shell for tmpl=login.
  No sidebar/toolbar — just the centered login card.
  Receives $content (rendered mod_adminlogin HTML) from the document pipeline.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $sitename   = Config::get('sitename', 'Hubzero');
  $translator = App::get('language', null);
  $lang       = $translator ? $translator->getTag() : 'en';
  $direction  = $translator && $translator->isRTL() ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html dir="{{ $direction }}" lang="{{ $lang }}" data-theme="hubzero"
      data-css-framework="daisyui" data-view-engine="blade">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ Lang::txt('COM_LOGIN_ADMINISTRATION_LOGIN') }} — {{ $sitename }}</title>
  <link rel="stylesheet"
        href="/core/templates/hzadmin/css/admin.css?v={{ filemtime(PATH_ROOT . '/core/templates/hzadmin/css/admin.css') ?: '1' }}" />
  @php
    try {
        $headData = Document::getHeadData();
        foreach ($headData['styleSheets'] ?? [] as $src => $attribs) {
            $type = $attribs['type'] ?? 'text/css';
            echo '<link rel="stylesheet" type="' . e($type) . '" href="' . e($src) . '" />' . "\n";
        }
        // Inline style declarations intentionally not rendered (strict CSP).
    } catch (\Throwable $e) { /* ignore */ }
  @endphp
</head>
<body class="login-page">

  {{-- Flash messages above the card --}}
  @php
    $msgs = App::get('notification') ? App::get('notification')->messages() : [];
  @endphp
  @if(!empty($msgs))
    <div class="login-wrap mb-3 space-y-2" role="region" aria-label="Notifications">
      @foreach($msgs as $msg)
        @php
          $alertClass = match($msg['type'] ?? 'info') {
            'error'   => 'alert-error',
            'warning' => 'alert-warning',
            'success' => 'alert-success',
            default   => 'alert-info',
          };
          $alertRole = in_array($msg['type'] ?? '', ['error', 'warning']) ? 'alert' : 'status';
        @endphp
        <div role="{{ $alertRole }}" class="alert {{ $alertClass }} text-sm py-2">
          <span>{!! $msg['message'] !!}</span>
        </div>
      @endforeach
    </div>
  @endif

  {{-- Login card (rendered by component → mod_adminlogin) --}}
  <main id="main-content" tabindex="-1" class="login-wrap">
    {!! $content ?? '' !!}
  </main>

  @php
    try {
        $headData = $headData ?? Document::getHeadData();
        foreach ($headData['scripts'] ?? [] as $src => $attribs) {
            $type = $attribs['type'] ?? 'text/javascript';
            echo '<script type="' . e($type) . '" src="' . e($src) . '"></script>' . "\n";
        }
        // Inline script declarations intentionally not rendered (strict CSP).
    } catch (\Throwable $e) { /* ignore */ }
  @endphp

</body>
</html>
