{{--
  Admin Blade Page Shell — hzadmin Template

  daisyUI drawer layout: sidebar nav + main content area.
  Used when a component view has a .blade.php template.
  Legacy views fall back to index.php (Kameleon copy).

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
<html dir="{{ $direction }}" lang="{{ $lang }}" data-theme="hubzero">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $pageTitle }}</title>
  <link rel="stylesheet"
        href="/core/templates/hzadmin/css/admin.css?v={{ filemtime(PATH_ROOT . '/core/templates/hzadmin/css/admin.css') ?: '1' }}" />
  @php
    try {
        $headData = Document::getHeadData();
        // External stylesheets (from Html::behavior, $this->css(), etc.)
        if (!empty($headData['styleSheets'])) {
            foreach ($headData['styleSheets'] as $src => $attribs) {
                $type = $attribs['type'] ?? 'text/css';
                echo '<link rel="stylesheet" type="' . e($type) . '" href="' . e($src) . '" />' . "\n";
            }
        }
        // Inline style declarations are intentionally NOT rendered here.
        // Strict CSP (Apache) blocks inline <style> blocks.
        // Legacy callers of addStyleDeclaration() should migrate to external files.
    } catch (\Throwable $e) {
        // Document type may not support getHeadData
    }
  @endphp
  @stack('styles')
  <script src="/core/assets/js/jquery.js"></script>
</head>
<body class="bg-base-200 text-base-content min-h-screen">

  <div class="drawer lg:drawer-open">
    {{-- Drawer toggle (controlled by hamburger in topbar) --}}
    <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

    {{-- Main content area --}}
    <div class="drawer-content flex flex-col min-h-screen">
      @include('hzadmin::partials.topbar')
      @include('hzadmin::partials.submenu')
      @include('hzadmin::partials.messages')

      <main id="main-content" class="flex-1 p-6">
        {!! $content ?? '' !!}
      </main>

      @include('hzadmin::partials.footer')
    </div>

    {{-- Sidebar drawer --}}
    @include('hzadmin::partials.sidebar')
  </div>

  <script src="/core/templates/hzadmin/js/admin.js"></script>
  <script src="/core/templates/hzadmin/js/sortable.js"></script>
  @php
    try {
        $headData = $headData ?? Document::getHeadData();
        // External scripts (from Html::behavior, $this->js(), etc.)
        // jQuery + noconflict already loaded above; skip duplicates
        if (!empty($headData['scripts'])) {
            foreach ($headData['scripts'] as $src => $attribs) {
                if (str_contains($src, '/jquery.js')
                    || str_contains($src, '/jquery.noconflict.js')) {
                    continue;
                }
                $type = $attribs['type'] ?? 'text/javascript';
                echo '<script type="' . e($type) . '" src="' . e($src) . '"></script>' . "\n";
            }
        }
        // Inline script declarations are intentionally NOT rendered here.
        // Strict CSP (Apache) blocks inline <script> blocks.
        // Legacy callers of addScriptDeclaration() should migrate to external files.
    } catch (\Throwable $e) {
        // Document type may not support getHeadData
    }
  @endphp
  @stack('scripts')
</body>
</html>
