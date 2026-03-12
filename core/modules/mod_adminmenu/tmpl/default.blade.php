{{--
  mod_adminmenu — admin sidebar navigation (Blade)

  Builds the admin menu tree and renders it as a daisyUI sidebar menu
  with collapsible <details> sections. Replaces the legacy Tree/Node
  renderMenu() output with semantic, accessible markup.

  Variables: $menu, $enabled, $lang, $user, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Modules\Adminmenu\Adminmenu;
  use Modules\Adminmenu\Node;

  $shownew = (bool) $params->get('shownew', 1);
  $active  = Request::getCmd('option');

  // ── Icon SVG map (keyed by class:name suffix) ─────────────
  $svgIcons = [
    'site'        => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    'cpanel'      => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
    'config'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
    'maintenance' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
    'users'       => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    'members'     => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    'groups'      => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    'menus'       => '<line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>',
    'articles'    => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
    'components'  => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
    'extensions'  => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
    'help'        => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
    'logout'      => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
    'info'        => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
  ];

  // Helper: get SVG paths for a class:name key
  $iconSvg = function ($classStr) use ($svgIcons) {
      if (!$classStr) return '';
      $name = str_replace('class:', '', explode(' ', $classStr)[0] ?? '');
      return $svgIcons[$name] ?? '';
  };

  // ── Build menu sections ────────────────────────────────────
  // Each section: ['title'=>, 'icon'=>, 'active'=>, 'items'=>[...]]
  $sections = [];

  // ─── Site ──────────────────────────────────────────────────
  $siteActives = ['com_system', 'com_cpanel', 'com_config', 'com_checkin', 'com_cache', 'com_redirect'];
  $siteItems = [];
  $siteItems[] = ['title' => Lang::txt('MOD_MENU_CONTROL_PANEL'), 'link' => 'index.php', 'icon' => 'cpanel', 'active' => ($active == 'com_cpanel')];

  if (User::authorise('core.admin')) {
      $siteItems[] = ['title' => Lang::txt('MOD_MENU_CONFIGURATION'), 'link' => 'index.php?option=com_config', 'icon' => 'config', 'active' => ($active == 'com_config')];
  }

  $chm = User::authorise('core.admin', 'com_checkin');
  $cam = User::authorise('core.manage', 'com_cache');
  if ($chm || $cam) {
      if ($chm) {
          $siteItems[] = ['title' => Lang::txt('MOD_MENU_GLOBAL_CHECKIN'), 'link' => 'index.php?option=com_checkin', 'active' => ($active == 'com_checkin')];
      }
      if ($cam) {
          $siteItems[] = ['title' => Lang::txt('MOD_MENU_CLEAR_CACHE'), 'link' => 'index.php?option=com_cache'];
          $siteItems[] = ['title' => Lang::txt('MOD_MENU_SYS_APC'), 'link' => 'index.php?option=com_system&controller=cache'];
      }
      $siteItems[] = ['title' => Lang::txt('MOD_MENU_SYS_LDAP'), 'link' => 'index.php?option=com_system&controller=ldap'];
      $siteItems[] = ['title' => Lang::txt('MOD_MENU_SYS_GEO'), 'link' => 'index.php?option=com_system&controller=geodb'];
      $siteItems[] = ['title' => Lang::txt('MOD_MENU_SYS_ROUTES'), 'link' => 'index.php?option=com_redirect', 'active' => ($active == 'com_redirect')];
  }
  if (User::authorise('core.admin')) {
      $siteItems[] = ['title' => Lang::txt('MOD_MENU_SYSTEM_INFORMATION'), 'link' => 'index.php?option=com_system&controller=info'];
  }
  $siteItems[] = ['title' => Lang::txt('MOD_MENU_LOGOUT'), 'link' => 'index.php?option=com_login&task=logout&' . Session::getFormToken() . '=1', 'icon' => 'logout'];

  $sections[] = ['title' => Lang::txt('JSITE'), 'icon' => 'site', 'active' => in_array($active, $siteActives), 'items' => $siteItems];

  // ─── Users ─────────────────────────────────────────────────
  if (User::authorise('core.manage', 'com_members')) {
      $userItems = [];
      $userItems[] = ['title' => Lang::txt('MOD_MENU_COM_MEMBERS'), 'link' => 'index.php?option=com_members', 'active' => ($active == 'com_members')];
      if (User::authorise('core.manage', 'com_groups')) {
          $userItems[] = ['title' => Lang::txt('MOD_MENU_COM_GROUPS'), 'link' => 'index.php?option=com_groups', 'active' => ($active == 'com_groups')];
      }
      if (User::authorise('core.admin', 'com_members')) {
          $userItems[] = ['title' => Lang::txt('MOD_MENU_COM_USERS_GROUPS'), 'link' => 'index.php?option=com_members&controller=accessgroups'];
          $userItems[] = ['title' => Lang::txt('MOD_MENU_COM_USERS_LEVELS'), 'link' => 'index.php?option=com_members&controller=accesslevels'];
      }
      $userItems[] = ['title' => Lang::txt('MOD_MENU_COM_USERS_NOTES'), 'link' => 'index.php?option=com_members&controller=notes'];
      $userItems[] = ['title' => Lang::txt('MOD_MENU_MASS_MAIL_USERS'), 'link' => 'index.php?option=com_members&controller=mail'];
      $sections[] = ['title' => Lang::txt('MOD_MENU_COM_USERS_USERS'), 'icon' => 'users', 'active' => ($active == 'com_members' || $active == 'com_groups'), 'items' => $userItems];
  }

  // ─── Menus ─────────────────────────────────────────────────
  if (User::authorise('core.manage', 'com_menus')) {
      $menuItems = [];
      $menuItems[] = ['title' => Lang::txt('MOD_MENU_MENU_MANAGER'), 'link' => 'index.php?option=com_menus&view=menus'];
      foreach (Adminmenu::getMenus() as $menuType) {
          $menuItems[] = ['title' => $menuType->title, 'link' => 'index.php?option=com_menus&view=items&menutype=' . $menuType->menutype];
      }
      $sections[] = ['title' => Lang::txt('MOD_MENU_MENUS'), 'icon' => 'menus', 'active' => ($active == 'com_menus'), 'items' => $menuItems];
  }

  // ─── Content ───────────────────────────────────────────────
  if (User::authorise('core.manage', 'com_content')) {
      $contentItems = [];
      $contentItems[] = ['title' => Lang::txt('MOD_MENU_COM_CONTENT_ARTICLE_MANAGER'), 'link' => 'index.php?option=com_content', 'active' => ($active == 'com_content')];
      $contentItems[] = ['title' => Lang::txt('MOD_MENU_COM_CONTENT_CATEGORY_MANAGER'), 'link' => 'index.php?option=com_categories&extension=com_content'];
      if (User::authorise('core.manage', 'com_media')) {
          $contentItems[] = ['title' => Lang::txt('MOD_MENU_MEDIA_MANAGER'), 'link' => 'index.php?option=com_media', 'active' => ($active == 'com_media')];
      }
      $sections[] = ['title' => Lang::txt('MOD_MENU_COM_CONTENT'), 'icon' => 'articles', 'active' => in_array($active, ['com_content', 'com_categories', 'com_media']), 'items' => $contentItems];
  }

  // ─── Components ────────────────────────────────────────────
  $components = Adminmenu::getComponents(true);
  if ($components) {
      $compItems = [];
      $compActives = [];
      foreach ($components as $component) {
          if (in_array($component->element, ['com_members', 'com_groups', 'com_system'])) continue;
          $compActives[] = $component->element;
          $compItems[] = ['title' => $component->text, 'link' => $component->link, 'active' => ($active == $component->element)];
      }
      if ($compItems) {
          $sections[] = ['title' => Lang::txt('MOD_MENU_COMPONENTS'), 'icon' => 'components', 'active' => in_array($active, $compActives), 'items' => $compItems];
      }
  }

  // ─── Extensions ────────────────────────────────────────────
  $im = User::authorise('core.manage', 'com_installer');
  $mm = User::authorise('core.manage', 'com_modules');
  $pm = User::authorise('core.manage', 'com_plugins');
  $tm = User::authorise('core.manage', 'com_templates');
  $lm = User::authorise('core.manage', 'com_languages');
  if ($im || $mm || $pm || $tm || $lm) {
      $extItems = [];
      if ($im) $extItems[] = ['title' => Lang::txt('MOD_MENU_EXTENSIONS_EXTENSION_MANAGER'), 'link' => 'index.php?option=com_installer', 'active' => ($active == 'com_installer')];
      if ($mm) $extItems[] = ['title' => Lang::txt('MOD_MENU_EXTENSIONS_MODULE_MANAGER'), 'link' => 'index.php?option=com_modules', 'active' => ($active == 'com_modules')];
      if ($pm) $extItems[] = ['title' => Lang::txt('MOD_MENU_EXTENSIONS_PLUGIN_MANAGER'), 'link' => 'index.php?option=com_plugins', 'active' => ($active == 'com_plugins')];
      if ($tm) $extItems[] = ['title' => Lang::txt('MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER'), 'link' => 'index.php?option=com_templates', 'active' => ($active == 'com_templates')];
      if ($lm) $extItems[] = ['title' => Lang::txt('MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER'), 'link' => 'index.php?option=com_languages', 'active' => ($active == 'com_languages')];
      $sections[] = ['title' => Lang::txt('MOD_MENU_EXTENSIONS_EXTENSIONS'), 'icon' => 'extensions', 'active' => in_array($active, ['com_installer', 'com_modules', 'com_plugins', 'com_templates', 'com_languages']), 'items' => $extItems];
  }

  // ─── Help ──────────────────────────────────────────────────
  if ($params->get('showhelp', 0) == 1) {
      $helpItems = [];
      $helpItems[] = ['title' => Lang::txt('MOD_MENU_HELP_PAGES'), 'link' => 'index.php?option=com_help'];
      if ($forumUrl = $params->get('forum_url')) {
          $helpItems[] = ['title' => Lang::txt('MOD_MENU_HELP_SUPPORT_CUSTOM_FORUM'), 'link' => $forumUrl, 'target' => '_blank'];
      }
      $helpItems[] = ['title' => Lang::txt('MOD_MENU_HELP_DOCUMENTATION'), 'link' => 'http://hubzero.org/documentation', 'target' => '_blank'];
      $helpItems[] = ['title' => Lang::txt('MOD_MENU_HELP_HUBZERO'), 'link' => 'http://hubzero.org/support', 'target' => '_blank'];
      $sections[] = ['title' => Lang::txt('MOD_MENU_HELP'), 'icon' => 'help', 'active' => ($active == 'com_help'), 'items' => $helpItems];
  }
@endphp

@if (!$enabled)
  {{-- Disabled menu (readonly labels) --}}
  <ul class="menu menu-xs">
    @foreach ($sections as $section)
      <li class="opacity-40">
        <span class="font-medium text-xs">{{ $section['title'] }}</span>
      </li>
    @endforeach
  </ul>
@else
  {{-- Active sidebar menu --}}
  <ul class="menu menu-xs gap-0.5 w-full">
    @foreach ($sections as $section)
      @php
        $hasActiveChild = false;
        foreach ($section['items'] as $item) {
            if (!empty($item['active'])) { $hasActiveChild = true; break; }
        }
        $sectionActive = $section['active'] || $hasActiveChild;
        $iconPaths = $svgIcons[$section['icon']] ?? '';
      @endphp
      <li>
        <details {{ $sectionActive ? 'open' : '' }}>
          <summary class="font-medium text-xs {{ $sectionActive ? 'text-primary' : '' }}">
            @if ($iconPaths)
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                   stroke-linejoin="round" class="shrink-0 opacity-60">
                {!! $iconPaths !!}
              </svg>
            @endif
            {{ $section['title'] }}
          </summary>
          <ul>
            @foreach ($section['items'] as $item)
              @php
                $itemActive = !empty($item['active']);
                $itemIcon   = $svgIcons[$item['icon'] ?? ''] ?? '';
                $link       = $item['link'];
                if (str_starts_with($link, 'index.php')) {
                    $link = Route::url($link, false);
                }
              @endphp
              <li>
                <a href="{{ $link }}"
                   class="{{ $itemActive ? 'active font-medium' : '' }}"
                   {!! !empty($item['target']) ? 'target="' . $item['target'] . '" rel="noopener"' : '' !!}>
                  @if ($itemIcon)
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="shrink-0 opacity-50">
                      {!! $itemIcon !!}
                    </svg>
                  @endif
                  {{ $item['title'] }}
                </a>
              </li>
            @endforeach
          </ul>
        </details>
      </li>
    @endforeach
  </ul>
@endif
