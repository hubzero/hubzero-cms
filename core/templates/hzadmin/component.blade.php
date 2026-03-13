{{--
  Admin Component Shell — hzadmin Template

  Minimal Blade shell for tmpl=component (modal/iframe content).
  No sidebar, topbar, or drawer — just the component output with
  daisyUI styling.

  Loads admin.css automatically; component-registered stylesheets and
  scripts (via $__view->css(), $__view->js(), Document::addStyleSheet(), etc.)
  are rendered from headData.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $translator = App::get('language', null);
  $lang       = $translator ? $translator->getTag() : 'en';
  $direction  = $translator && $translator->isRTL() ? 'rtl' : 'ltr';
  $pageTitle  = $title ?? '';
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
        // External stylesheets (from $__view->css(), Document::addStyleSheet(), etc.)
        if (!empty($headData['styleSheets'])) {
            foreach ($headData['styleSheets'] as $src => $attribs) {
                // Skip admin.css — already loaded above
                if (str_contains($src, 'admin.css')) continue;
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
<body class="bg-base-100 text-base-content"{!! Document::renderBodyAttributes() !!}>
  {!! $content ?? '' !!}

  <script src="/core/templates/hzadmin/js/admin.js"></script>
  @php
    try {
        $headData = $headData ?? Document::getHeadData();
        // External scripts (from $__view->js(), Document::addScript(), etc.)
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
  @stack('scripts')
</body>
</html>
