{{--
  Help Index — Site page listing

  Lists available help pages for a component. Rendered when a component's
  help index is requested but no index.phtml file exists.

  Variables from controller (displayTask via View):
    $pages — array of component info, each with 'name', 'option', 'pages'

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

@foreach($pages as $component)
  <h2 class="text-xl font-semibold mt-6 mb-2">
    {{ Lang::txt('COM_HELP_COMPONENT_HELP', $component['name']) }}
  </h2>

  @if(count($component['pages']) > 0)
    <p class="mb-3">
      {{ Lang::txt('COM_HELP_PAGE_INDEX_EXPLANATION', $component['name']) }}
    </p>
    <ul class="menu menu-sm bg-base-200 rounded-box w-full max-w-md">
      @foreach($component['pages'] as $pageFile)
        @php
          $name = str_replace('.phtml', '', $pageFile);
          $comp = str_replace('com_', '', $component['option']);
          $url = Route::url(
              'index.php?option=com_help&component=' . $comp
              . '&page=' . $name
          );
          $label = ucwords(str_replace('_', ' ', $name));
        @endphp
        <li><a href="{{ $url }}">{{ $label }}</a></li>
      @endforeach
    </ul>
  @else
    <p class="text-base-content/60">{{ Lang::txt('COM_HELP_NO_PAGES_FOUND') }}</p>
  @endif
@endforeach
