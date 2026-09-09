{{--
  Resource tab navigation — plugin-provided content tabs.

  Variables:
    $option   — component option string
    $cats     — array of tab categories (each is [name => label])
    $resource — Entry model
    $active   — string, currently active tab name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $base = Request::get('tab_base_url')
      ?: 'index.php?option=' . $option;
  $base .= '&' . ($resource->alias ? 'alias=' . $resource->alias : 'id=' . $resource->id);

  $activeKey = Request::get('tab_active_key') ?: 'active';
@endphp

<nav aria-label="Resource sections">
  <div role="tablist" class="tabs tabs-border">
    @foreach($cats as $cat)
      @php
        $name = key($cat);
        if (!$name) {
            continue;
        }
        $url = $base . '&' . $activeKey . '=' . $name;
        $isActive = (strtolower($name) == $active);

        if ($isActive) {
            Pathway::append($cat[$name], $url);
            if ($name != 'about') {
                Document::setTitle(Document::getTitle() . ': ' . $cat[$name]);
            }
        }
      @endphp
      <a role="tab"
         class="tab {{ $isActive ? 'tab-active' : '' }}"
         data-rel="{{ $name }}"
         href="{{ Route::url($url) }}"
         aria-selected="{{ $isActive ? 'true' : 'false' }}">
        {{ $cat[$name] }}
      </a>
    @endforeach
  </div>
</nav>
