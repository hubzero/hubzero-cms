{{--
  Basic Search — Admin interface

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH') }}: {{ Lang::txt('COM_SEARCH_SITEMAP') }}"
    icon="search"
    option="{{ $option }}"
    :preferences="true"
/>

@php
  $context = [];
  if (array_key_exists('search-task', $_POST)) {
      foreach (Event::trigger('search.onSearchTask' . $_POST['search-task']) as $resp) {
          list($name, $html, $ctx) = $resp;
          echo $html;
          if (array_key_exists($name, $context)) {
              $context[$name] = array_merge($context[$name], $ctx);
          } else {
              $context[$name] = $ctx;
          }
      }
  }

  foreach (Event::trigger('search.onSearchAdministrate', [$context]) as $plugin) {
      list($name, $html) = $plugin;
      echo $html;
  }
@endphp
