{{--
  Related resources — compact sidebar list.

  Variables (from plugin):
    $option  — string: component option
    $related — array: related resource objects

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<div class="container" id="whatsrelated">
  <h3>{{ Lang::txt('PLG_RESOURCES_RELATED_HEADER') }}</h3>

  @if($related)
    <ul>
      @foreach($related as $line)
        @php
          if ($line->section != 'Topic') {
              $sef = $line->alias
                  ? Route::url('index.php?option=' . $option . '&alias=' . $line->alias)
                  : Route::url('index.php?option=' . $option . '&id=' . $line->id);
              $class = 'series';
          } else {
              if ($line->scope == 'group' && !!$line->scope_id) {
                  $query = "SELECT cn from `#__xgroups` where gidNumber = " . (int) $line->scope_id;
                  $groupAlias = App::get('db')->setQuery($query)->loadObjectList()[0]->cn;
                  $sef = Route::url("/groups/$groupAlias/wiki/$line->alias");
              } else {
                  $sef = Route::url('index.php?option=com_wiki&scope=' . $line->scope . '&pagename=' . $line->alias);
              }
              $class = 'wiki';
          }
        @endphp
        <li class="{{ $class }}">
          <a href="{{ $sef }}">
            @if($line->section == 'Series')
              <span>{{ Lang::txt('PLG_RESOURCES_RELATED_PART_OF') }}</span>
            @endif
            {{ e(stripslashes($line->title)) }}
          </a>
        </li>
      @endforeach
    </ul>
  @else
    <p>{{ Lang::txt('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND') }}</p>
  @endif
</div>
