{{--
  API documentation page — overview + OAuth sections with sidebar navigation.

  Variables from controller:
    $documentation  — Array from API Doc Generator
    $tokens         — Collection of user's active access tokens

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$host = $_SERVER['HTTP_HOST'];
list($base,) = explode('.', $host);
$url = 'https://' . $host . '/api';

$versions = array_reverse($documentation['versions']['available']);
$activeVersion = \Hubzero\Facades\Request::getString('version', reset($versions));

$apiHomeUrl = Route::url('index.php?option=com_developer&controller=api&version=' . $activeVersion);
@endphp

<x-page-container :title="Lang::txt('COM_DEVELOPER_API_DOCS')">
  @slot('actions')
    <a class="btn btn-ghost btn-sm" href="{{ $apiHomeUrl }}">
      {{ Lang::txt('COM_DEVELOPER_API_HOME') }}
    </a>
  @endslot

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
    {{-- Left sidebar nav --}}
    <aside class="lg:sticky lg:top-4 lg:self-start lg:max-h-[calc(100vh-2rem)] lg:overflow-y-auto">
      {!! $__view->view('_menu')
            ->set('documentation', $documentation)
            ->set('active', '')
            ->set('version', $activeVersion)
            ->loadTemplate() !!}
    </aside>

    {{-- Main content --}}
    <div class="min-w-0">
      {{-- Active tokens --}}
      @if (!empty($tokens) && count($tokens) > 0)
        {!! $__view->view('_active_tokens')
              ->set('tokens', $tokens)
              ->loadTemplate() !!}
      @endif

      {{-- Overview --}}
      {!! $__view->view('_docs_overview')
            ->set('url', $url)
            ->set('base', $base)
            ->loadTemplate() !!}

      {{-- OAuth --}}
      {!! $__view->view('_docs_oauth')
            ->set('url', $url)
            ->loadTemplate() !!}
    </div>
  </div>

</x-page-container>
