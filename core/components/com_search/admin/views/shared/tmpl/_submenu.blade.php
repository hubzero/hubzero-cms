{{--
  Search — Admin submenu

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Submenu;

  $receivedQuery = Request::query();
  $currentController = $receivedQuery['controller'] ?? null;

  $entries = [
      [
          'text' => Lang::txt('COM_SEARCH_SUBMENU_OVERVIEW'),
          'queryParams' => [
              'option' => $option,
              'task' => 'configure',
              'controller' => null,
          ],
      ],
      [
          'text' => Lang::txt('COM_SEARCH_SUBMENU_COMPONENTS'),
          'queryParams' => [
              'option' => $option,
              'task' => 'display',
              'controller' => 'searchable',
          ],
      ],
      [
          'text' => Lang::txt('COM_SEARCH_SUBMENU_BLACKLIST'),
          'queryParams' => [
              'option' => $option,
              'task' => 'manageBlacklist',
              'controller' => 'solr',
          ],
      ],
      [
          'text' => Lang::txt('COM_SEARCH_SUBMENU_BOOSTS'),
          'queryParams' => [
              'option' => $option,
              'task' => 'list',
              'controller' => 'boosts',
          ],
      ],
  ];

  foreach ($entries as $entry) {
      $params = $entry['queryParams'];
      $url = 'index.php?' . http_build_query($params);
      $active = $params['controller'] == $currentController;
      Submenu::addEntry($entry['text'], $url, $active);
  }
@endphp
