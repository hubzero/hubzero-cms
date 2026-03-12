{{--
  Admin Help Shell — hzadmin Template

  Minimal Blade shell for tmpl=help (help content in popup/iframe).
  No sidebar, topbar, or drawer — just the component output with
  daisyUI styling. Identical to component.blade.php.

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
<html dir="{{ $direction }}" lang="{{ $lang }}" data-theme="hubzero">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $pageTitle }}</title>
  <link rel="stylesheet"
        href="/core/templates/hzadmin/css/admin.css" />
  @stack('styles')
</head>
<body class="bg-base-100 text-base-content">
  {!! $content ?? '' !!}
  <script src="/core/templates/hzadmin/js/admin.js"></script>
  @php
    // Inline script declarations intentionally not rendered (strict CSP).
  @endphp
  @stack('scripts')
</body>
</html>
