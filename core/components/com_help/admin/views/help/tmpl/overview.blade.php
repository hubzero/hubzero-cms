{{--
  Help Center — Admin overview

  Card grid directory of help topics. Clicking a link opens the help
  content in the admin popup modal (same as toolbar Help button).

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_HELP'), 'help');
  Toolbar::help('index');

  $helpUrl = function ($comp) use ($option) {
      return Route::url(
          'index.php?option=' . $option . '&tmpl=help&component=' . $comp,
          false, false
      );
  };

  // Static categories with their component links
  $categories = [
      [
          'heading' => Lang::txt('COM_HELP_USERS'),
          'links'   => [
              ['url' => $helpUrl('com_members'), 'text' => Lang::txt('COM_HELP_USER_ACCOUNTS')],
              ['url' => $helpUrl('com_groups'),  'text' => Lang::txt('COM_HELP_USER_GROUPS')],
          ],
      ],
      [
          'heading' => Lang::txt('COM_HELP_MENUS'),
          'links'   => [
              ['url' => $helpUrl('com_menus'), 'text' => Lang::txt('COM_HELP_MENU_MANAGER')],
          ],
      ],
      [
          'heading' => Lang::txt('COM_HELP_CONTENT'),
          'links'   => [
              ['url' => $helpUrl('com_content'),    'text' => Lang::txt('COM_HELP_ARTICLE_MANAGER')],
              ['url' => $helpUrl('com_categories'), 'text' => Lang::txt('COM_HELP_CATEGORY_MANAGER')],
              ['url' => $helpUrl('com_media'),      'text' => Lang::txt('COM_HELP_MEDIA_MANAGER')],
          ],
      ],
      [
          'heading' => Lang::txt('COM_HELP_EXTENSIONS'),
          'links'   => [
              ['url' => $helpUrl('com_modules'),   'text' => Lang::txt('COM_HELP_MODULE_MANAGER')],
              ['url' => $helpUrl('com_plugins'),   'text' => Lang::txt('COM_HELP_PLUGIN_MANAGER')],
              ['url' => $helpUrl('com_templates'), 'text' => Lang::txt('COM_HELP_TEMPLATE_MANAGER')],
              ['url' => $helpUrl('com_languages'), 'text' => Lang::txt('COM_HELP_LANGUAGE_MANAGER')],
          ],
      ],
  ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

  @foreach($categories as $cat)
    <div class="admin-fieldset">
      <h3 class="admin-fieldset-heading">{{ $cat['heading'] }}</h3>
      <div class="admin-fieldset-body">
        <ul class="menu menu-sm">
          @foreach($cat['links'] as $link)
            <li>
              <a href="#"
                 data-href="{{ $link['url'] }}"
                 data-title="{{ $link['text'] }}"
                 data-width="800"
                 data-height="550"
                 class="toolbar toolbar-popup">{{ $link['text'] }}</a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  @endforeach

  {{-- Dynamic components card --}}
  @if(!empty($components))
    <div class="admin-fieldset sm:col-span-2 lg:col-span-3 xl:col-span-4">
      <h3 class="admin-fieldset-heading">{{ Lang::txt('Components') }}</h3>
      <div class="admin-fieldset-body">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-x-6 gap-y-1">
          @foreach($components as $comp)
            <a href="#"
               data-href="{{ $helpUrl($comp->element) }}"
               data-title="{{ Lang::txt($comp->text) }}"
               data-width="800"
               data-height="550"
               class="toolbar toolbar-popup text-sm py-1 hover:text-primary transition-colors">
              {{ Lang::txt($comp->text) }}
            </a>
          @endforeach
        </div>
      </div>
    </div>
  @endif

</div>
