{{--
  Articles Categories — recursive items sub-template.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@foreach ($list as $item)
  @php
    $catRoute = Route::url(\Components\Content\Site\Helpers\Route::getCategoryRoute($item->id));
    $levelup = $item->level - $startLevel - 1;
  @endphp
  <li{{ request()->getRequestUri() == $catRoute ? ' class=active' : '' }}>
    <a href="{{ $catRoute }}">{{ $item->title }}</a>
    @if ($params->get('show_description', 0))
      {!! Html::$content('prepare', $item->description, $item->getParams(), 'mod_articles_categories.$content') !!}
    @endif
    @php
      $showChildren = $params->get('show_children', 0);
      $maxLevel = $params->get('maxlevel', 0);
      $levelDiff = $item->level - $startLevel;
    @endphp
    @if ($showChildren && (($maxLevel == 0) || ($maxLevel >= $levelDiff)) && count($item->getChildren()))
      @php
        $parentList = $list;
        $list = $item->getChildren();
      @endphp
      <ul>
        @include('mod_articles_categories::default_items')
      </ul>
      @php $list = $parentList; @endphp
    @endif
  </li>
@endforeach
