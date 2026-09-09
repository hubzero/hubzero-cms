{{--
  Admin cpanel Blade page shell — hzadmin Template

  Used for tmpl=cpanel (dashboard) pages.
  Same drawer layout as index.blade.php.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $sitename   = Config::get('sitename', 'Hubzero');
  $translator = App::get('language', null);
  $lang       = $translator ? $translator->getTag() : 'en';
  $direction  = $translator && $translator->isRTL() ? 'rtl' : 'ltr';
  $option     = $option ?? Request::getCmd('option', '');
  $pageTitle  = $title ?? $sitename . ' — Administration';

  $mod  = app()->bound('module') ? app('module') : null;
  $user = User::getInstance();
@endphp
<!DOCTYPE html>
<html dir="{{ $direction }}" lang="{{ $lang }}" data-theme="hubzero"
      data-css-framework="daisyui" data-view-engine="blade">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $pageTitle }}</title>
  <link rel="stylesheet"
        href="/core/templates/hzadmin/css/admin.css?v={{ filemtime(PATH_ROOT . '/core/templates/hzadmin/css/admin.css') ?: '1' }}" />
  @php
    try {
        $headData = Document::getHeadData();
        if (!empty($headData['styleSheets'])) {
            foreach ($headData['styleSheets'] as $src => $attribs) {
                $type = $attribs['type'] ?? 'text/css';
                echo '<link rel="stylesheet" type="' . e($type) . '" href="' . e($src) . '" />' . "\n";
            }
        }
        // Inline style declarations intentionally not rendered (strict CSP).
    } catch (\Throwable $e) {
        // Document type may not support getHeadData
    }
  @endphp
  @stack('styles')
</head>
<body class="bg-base-200 text-base-content min-h-screen">

  <div class="drawer lg:drawer-open">
    <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

    <div class="drawer-content flex flex-col min-h-screen">
      @include('hzadmin::partials.topbar')
      @include('hzadmin::partials.submenu')
      @include('hzadmin::partials.messages')

      <main id="main-content" class="flex-1 p-6">
        {!! $content ?? '' !!}
      </main>

      @include('hzadmin::partials.footer')
    </div>

    @include('hzadmin::partials.sidebar')
  </div>

  @php
    try {
        $headData = $headData ?? Document::getHeadData();
        if (!empty($headData['scripts'])) {
            foreach ($headData['scripts'] as $src => $attribs) {
                $type = $attribs['type'] ?? 'text/javascript';
                echo '<script type="' . e($type) . '" src="' . e($src) . '"></script>' . "\n";
            }
        }
        // Inline script declarations intentionally not rendered (strict CSP).
    } catch (\Throwable $e) {
        // Document type may not support getHeadData
    }
  @endphp
  <script src="/core/templates/hzadmin/js/admin.js"></script>
  @stack('scripts')
</body>
</html>
